<?php

namespace App\Http\Controllers;

use App\Models\Formula;
use App\Models\Product;
use App\Models\ProductionLine;
use App\Models\ProductionOrder;
use App\Models\ProductionStep;
use App\Models\ProductionLog;
use App\Models\Customer;
use App\Models\FormulaDetail;
use App\Models\ProductionFormulaDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\PhysicalFormula;
use App\Models\Filling;
use App\Models\StockMovement;
use App\Services\StokService;


class ProductionOrderController extends Controller
{
    public function index()
    {
        $orders = ProductionOrder::with(['product', 'formula', 'line', 'creator', 'customer'])
            ->orderByDesc('planned_at')
            ->get();

        return view('production_orders.index', compact('orders'));
    }

    public function create()
    {
        $formulas = Formula::with('details.rawMaterial')->get();
        $lines = ProductionLine::all();
        $products = Product::all();
        $customers = Customer::all();

        return view('production_orders.create', compact('formulas', 'lines', 'products', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'production_line_id' => 'required|exists:production_lines,id',
            'formula_id' => 'required|exists:formulas,id',
            'customer_id' => 'required|exists:customers,id',
            'planned_at' => 'required|date',
            'planned_end_at' => 'nullable|date|after_or_equal:planned_at',
            'quantity_kg' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        $plannedDate = Carbon::parse($validated['planned_at'])->format('Ymd');
        $lineId = $validated['production_line_id'];
        $product = Product::find($validated['product_id']);
        $productCode = $product->code ?? 'UNKNOWN';

        $count = ProductionOrder::whereDate('planned_at', $validated['planned_at'])
            ->where('production_line_id', $lineId)
            ->where('product_id', $product->id)
            ->count();

        do {
            $serial = str_pad(++$count, 5, '0', STR_PAD_LEFT);
            $batchNumber = "BATCH-{$plannedDate}-{$lineId}-{$productCode}-{$serial}";
        } while (ProductionOrder::where('batch_number', $batchNumber)->exists());

        $order = ProductionOrder::create([
            ...$validated,
            'batch_number' => $batchNumber,
            'unit' => 'kg',
            'created_by' => Auth::id(),
            'status' => 'planned',
        ]);


        $formula = Formula::with('details.rawMaterial')->find($validated['formula_id']);

        foreach ($formula->details as $i => $detail) {
            $requiredQty = ($detail->percentage / 100) * $validated['quantity_kg'];

            ProductionFormulaDetail::create([
                'production_order_id' => $order->id,
                'formula_detail_id' => $detail->id,
                'required_quantity' => $requiredQty,
            ]);

            ProductionStep::create([
                'formula_id' => $formula->id,
                'production_order_id' => $order->id,
                'step' => 'Adım ' . ($i + 1) . ': ' . $detail->rawMaterial->name,
                'status' => 'planlandı',
            ]);
        }

        ProductionLog::create([
            'production_order_id' => $order->id,
            'event' => 'Oluşturuldu',
            'description' => 'İş emri oluşturuldu',
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('production_orders.index')->with('success', 'İş emri başarıyla oluşturuldu.');
    }

public function edit(ProductionOrder $productionOrder)
{
    $products = Product::all();
    $lines = ProductionLine::all();
    $formulas = Formula::all();
    $customers = Customer::all();

    // ✅ Tüm fiziksel reçeteleri parametreleriyle birlikte çekiyoruz
    $physicalFormulas = PhysicalFormula::with('details.parameter')->get();

    // ✅ Üretim emrindeki ürünün koduna göre fiziksel reçeteyi eşleştiriyoruz
    $productCode = $productionOrder->product->code ?? null;
    $physicalFormula = PhysicalFormula::where('product_code', $productCode)->first();

    return view('production_orders.edit', compact(
        'productionOrder',
        'products',
        'lines',
        'formulas',
        'customers',
        'physicalFormulas',
        'physicalFormula' // ✅ Blade'de otomatik seçim için
    ));
}


public function update(Request $request, ProductionOrder $productionOrder)
{
    $validated = $request->validate([
        'product_id' => 'required|exists:products,id',
        'production_line_id' => 'required|exists:production_lines,id',
        'formula_id' => 'required|exists:formulas,id',
        'customer_id' => 'required|exists:customers,id',
        'planned_at' => 'required|date',
        'planned_end_at' => 'nullable|date|after_or_equal:planned_at',
        'quantity_kg' => 'required|numeric|min:0.01',
        'notes' => 'nullable|string',
        'status' => 'required|in:planned,in_production,produced,in_filling,filled,cancelled,addition',
    ]);

    $oldStatus = $productionOrder->status;

    // Normal güncelleme (addition hariç)
    if ($validated['status'] !== 'addition') {
        $productionOrder->update($validated);
    }

    // Durum değişikliği logu
    if ($oldStatus !== $validated['status']) {
        ProductionLog::create([
            'production_order_id' => $productionOrder->id,
            'event' => 'Durum Güncellendi',
            'description' => "Durum '{$oldStatus}' → '{$validated['status']}' olarak değiştirildi.",
            'user_id' => Auth::id(),
            'logged_at' => now(),
        ]);
    }

    // ✅ Revizyon (İlave) işlemi
    if ($validated['status'] === 'addition') {
        $oldTonaj = $productionOrder->quantity_kg;
        $newTonaj = $validated['quantity_kg']; // ✅ direkt formdan gelen değer

        // validated içinden notes’u çıkarıyoruz (eski notları korumak için)
        unset($validated['notes']);

        $productionOrder->fill($validated);
        $productionOrder->quantity_kg = $newTonaj;
        // eski notları koruyarak yeni notu ekle
        $productionOrder->notes = trim(($productionOrder->notes ?? '') . "\nİşemri üzerinden revizyon iletildi, kontrol ederek ilave veriniz.");
        $productionOrder->revision_no++;
        $productionOrder->revised_at = now();
        $productionOrder->revised_by = Auth::id();

        // İlave sonrası otomatik üretime gönder
        $productionOrder->status = 'in_production';
        $productionOrder->save();

        ProductionLog::create([
            'production_order_id' => $productionOrder->id,
            'event' => 'Tonaj Revizyonu',
            'description' => "Tonaj {$oldTonaj} kg → {$newTonaj} kg olarak revize edildi.",
            'user_id' => Auth::id(),
            'logged_at' => now(),
        ]);

        ProductionLog::create([
            'production_order_id' => $productionOrder->id,
            'event' => 'İlave Alındı',
            'description' => "{$productionOrder->batch_number} reçetesi revize edilerek üretime hazır hale getirildi.",
            'user_id' => Auth::id(),
            'logged_at' => now(),
        ]);

        ProductionLog::create([
            'production_order_id' => $productionOrder->id,
            'event' => 'Üretime Gönderildi',
            'description' => "İlave tonaj bilgisi girildi, üretime gönderildi.",
            'user_id' => Auth::id(),
            'logged_at' => now(),
        ]);
    }

    return redirect()->route('production_orders.index')
        ->with('success', 'İş emri başarıyla güncellendi.');
}


public function addition(ProductionOrder $order)
{
    $oldStatus = $order->status;

    $order->update(['status' => 'addition']);

    ProductionLog::create([
        'production_order_id' => $order->id,
        'event' => 'İlave Alındı',
        'description' => "{$order->batch_number} iş emri {$oldStatus} → addition olarak değiştirildi ve tekrar planlamaya düştü.",
        'user_id' => Auth::id(),
        'logged_at' => now(),
    ]);

    return redirect()->route('production_orders.index')
        ->with('success','İş emri ilave aldı ve planlamaya geri döndü.');
}




    public function show(ProductionOrder $productionOrder)
    {
        $productionOrder->load([
            'customer',
            'line',
            'formula',
            'productionFormulaDetails.formulaDetail.rawMaterial',
            'logs.user'
        ]);

        $relatedOrders = ProductionOrder::where('batch_number', $productionOrder->batch_number)
            ->where('id', '!=', $productionOrder->id)
            ->with(['product', 'line', 'customer'])
            ->get();

        return view('production_orders.show', compact('productionOrder', 'relatedOrders'));
    }



    public function calendar()
    {
        $events = ProductionOrder::with(['product', 'customer'])->get()->map(function ($order) {
            return [
                'title' => $order->product->name . ' - ' . $order->customer->name,
                'start' => $order->planned_at->format('Y-m-d\TH:i:s'),
                'end'   => optional($order->planned_end_at)->format('Y-m-d\TH:i:s'), // ✅ bitiş tarihi
                'url'   => route('production_orders.show', $order->id),
                'color' => match($order->status) {
                    'planned'       => '#6c757d',
                    'in_production' => '#0d6efd',
                    'produced'      => '#198754',
                    'in_filling'    => '#ffc107',
                    'filled'        => '#20c997',
                    'cancelled'     => '#dc3545',
                    default         => '#6c757d',
                }
            ];
        });

    return view('production_orders.calendar', ['events' => $events]);
}

    public function process()
    {
        $orders = ProductionOrder::with(['product', 'line', 'formula', 'steps'])
            ->where('status', 'in_production')
            ->whereDoesntHave('steps', function ($query) {
                $query->where('status', 'tamamlandı');
            })
            ->orderBy('planned_at')
            ->get();

        return view('production_orders.process', compact('orders'));
    }

        public function showStep(ProductionStep $step)
    {
        return view('production_orders.step', compact('step'));
    }

    public function completeStep(ProductionStep $step)
    {
        if ($step->status !== 'tamamlandı') {
            $step->update(['status' => 'tamamlandı']);
        }

        return redirect()->back()->with('success', 'Adım tamamlandı olarak işaretlendi.');
    }

public function complete(Request $request, ProductionOrder $productionOrder)
{
    $completed = $request->input('completed_materials', []);

    foreach ($completed as $detailId) {
        ProductionFormulaDetail::where('production_order_id', $productionOrder->id)
            ->where('formula_detail_id', $detailId)
            ->update(['is_completed' => true]);

        $detail = FormulaDetail::with('rawMaterial')->find($detailId);

        ProductionLog::create([
            'production_order_id' => $productionOrder->id,
            'event' => 'Ham Madde Tamamlandı',
            'description' => $detail->rawMaterial->name . ' işaretlendi',
            'user_id' => Auth::id(),
        ]);
    }

    // ✅ Üretim tamamlandı
    $productionOrder->update(['status' => 'produced']);

    ProductionLog::create([
        'production_order_id' => $productionOrder->id,
        'event' => 'Üretim Tamamlandı',
        'description' => 'Tüm ham maddeler tamamlandı',
        'user_id' => Auth::id(),
    ]);

    // ✅ Kimyasal reçeteye göre stoktan düşüm
    $productionOrder->load('formula.details.rawMaterial');

    foreach ($productionOrder->formula->details as $detail) {
        $percentage = $detail->percentage;
        $quantityKg = $productionOrder->quantity_kg;

        if ($percentage === null || $quantityKg === null) {
            continue;
        }

        $requiredQty = round(($percentage / 100) * $quantityKg, 2);

        StockMovement::create([
            'type' => 'out',
            'material_id' => $detail->rawMaterial->id,
            'material_type' => get_class($detail->rawMaterial),
            'amount' => $requiredQty,
            'related_recipe_id' => $productionOrder->id,
            'triggered_by' => Auth::id(),
        ]);
    }

    // ✅ Laboratuvar için numune kabul kaydı aç
    $lastRequestNo = \App\Models\SampleAcceptance::max('request_no') ?? 0;

    \App\Models\SampleAcceptance::create([
        'product_id'   => $productionOrder->product_id,
        'company_name' => $productionOrder->customer->name ?? '-', // müşteri adı
        'batch_no'     => $productionOrder->batch_number,          // üretimde otomatik gelen batch no
        'request_no'   => $lastRequestNo + 1,
        'sample_type'  => 'madeni yağ',                            // istersen product tipine göre set edebilirsin
        'taken_place'  => 'üretim hattı',
        'taken_by'     => 'üretim çalışanı',
        'taken_at'     => now(),
        'amount'       => 300,
        'protection'   => 'bilinmiyor',
        'status'       => 'pending',
    ]);

    return redirect()->route('production_orders.index')
        ->with('success', 'Üretim başarıyla tamamlandı ve numune laboratuvara gönderildi.');
}
    public function start(ProductionOrder $productionOrder)
    {
        $productionOrder->update(['status' => 'in_production']);

        ProductionLog::create([
            'production_order_id' => $productionOrder->id,
            'event' => 'Üretim Başlatıldı',
            'description' => 'İş emri üretime alındı.',
            'user_id' => Auth::id(),
            'logged_at' => now(),
        ]);

        return redirect()->route('production_orders.index')->with('success', 'Üretim başlatıldı.');
    }

    public function destroy(ProductionOrder $productionOrder)
    {
        $productionOrder->steps()->delete();
        $productionOrder->productionFormulaDetails()->delete();
        $productionOrder->logs()->delete();
        $productionOrder->delete();

        return redirect()->route('production_orders.index')->with('success', 'İş emri ve bağlı kayıtlar silindi.');
    }
    public function sendToFillingForm(ProductionOrder $productionOrder)
{
    if ($productionOrder->status !== 'produced') {
        return back()->with('error', 'Sadece üretimi tamamlanmış iş emirleri doluma gönderilebilir.');
    }

    $formulas = Formula::with('details.rawMaterial')->get();
    $lines = ProductionLine::all();

    return view('production_orders.send_to_filling', compact('productionOrder', 'formulas', 'lines'));
}

public function storeFilling(Request $request, ProductionOrder $productionOrder)
{
    // ✅ Validasyon
    $validated = $request->validate([
        'physical_formula_id' => 'required|exists:physical_formulas,id',
        'filling_line_id' => 'required|exists:production_lines,id',
        'filled_quantity' => 'required|numeric|min:1',
        'volume' => 'required|numeric|min:0.01',
        'density' => 'required|numeric|min:0.1',
    ]);

    // ✅ Fiziksel reçeteyi çek
    $formula = PhysicalFormula::with('details.parameter')->findOrFail($validated['physical_formula_id']);

    // ✅ Toplam dolum miktarı (kg)
    $totalKg = $validated['filled_quantity'] * $validated['volume'] * $validated['density'];

    // ✅ Fiziksel reçeteye göre malzeme hesaplaması
    $materials = $formula->details->map(function ($detail) use ($totalKg) {
        return [
            'parameter' => $detail->parameter->name ?? 'Tanımsız',
            'label' => $detail->selection_label ?? '-',
            'percentage' => $detail->quantity,
            'calculated_quantity' => round(($detail->quantity / 100) * $totalKg, 2),
        ];
    });

    // ✅ Dolum kaydı oluştur
    $filling = Filling::create([
        'production_order_id' => $productionOrder->id,
        'physical_formula_id' => $formula->id,
        'filling_line_id' => $validated['filling_line_id'],
        'filled_quantity' => $validated['filled_quantity'],
        'volume' => $validated['volume'],
        'density' => $validated['density'],
        'calculated_materials' => $materials,
        'status' => 'pending',
    ]);

    // ✅ Üretim emrini güncelle
    $productionOrder->update(['status' => 'in_filling']);

    // ✅ Log kaydı
    ProductionLog::create([
        'production_order_id' => $productionOrder->id,
        'event' => 'Doluma Gönderildi',
        'description' => "Dolum hattı: {$filling->fillingLine->name}, Reçete: {$formula->product_name}",
        'user_id' => Auth::id(),
        'logged_at' => now(),
    ]);

    // ✅ Kimyasal reçeteye göre stoktan düşüm
    $productionOrder->load('formula.details.rawMaterial');

    foreach ($productionOrder->formula->details as $detail) {
        $percentage = $detail->percentage;
        $requiredQty = round(($percentage / 100) * $totalKg, 2);

        if ($detail->rawMaterial && $requiredQty > 0) {
            StockMovement::create([
                'type' => 'out',
                'material_id' => $detail->rawMaterial->id,
                'material_type' => get_class($detail->rawMaterial),
                'amount' => $requiredQty,
                'related_recipe_id' => $productionOrder->id,
                'triggered_by' => Auth::id(),
            ]);
        }
    }

    return redirect()->route('production_orders.filling')->with('success', 'Doluma gönderildi.');
}

public function tvShow()
{
    $orders = ProductionOrder::with(['product', 'formula', 'customer', 'productionLine'])
        ->orderBy('planned_at')
        ->get();

    return view('tv.show', compact('orders'));
}

}