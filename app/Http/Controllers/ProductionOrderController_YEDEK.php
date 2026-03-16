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
            ProductionStep::create([
                'formula_id' => $formula->id,
                'production_order_id' => $order->id,
                'step' => 'Adım ' . ($i + 1) . ': ' . $detail->rawMaterial->name,
                'status' => 'bekliyor',
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
        $formulas = Formula::with('details.rawMaterial')->get();
        $lines = ProductionLine::all();
        $products = Product::all();
        $customers = Customer::all();

        return view('production_orders.edit', compact('productionOrder', 'formulas', 'lines', 'products', 'customers'));
    }

    public function update(Request $request, ProductionOrder $productionOrder)
{
    $validated = $request->validate([
        'product_id' => 'required|exists:products,id',
        'production_line_id' => 'required|exists:production_lines,id',
        'formula_id' => 'required|exists:formulas,id',
        'customer_id' => 'required|exists:customers,id',
        'planned_at' => 'required|date',
        'quantity_kg' => 'required|numeric|min:0.01',
        'notes' => 'nullable|string',
        'status' => 'required|in:planned,in_progress,completed,cancelled',
    ]);

    $oldStatus = $productionOrder->status;

    $productionOrder->update($validated);

    if ($oldStatus !== $validated['status']) {
        ProductionLog::create([
            'production_order_id' => $productionOrder->id,
            'event' => 'Durum Güncellendi',
            'description' => "Durum '{$oldStatus}' → '{$validated['status']}' olarak değiştirildi.",
            'user_id' => Auth::id(),
            'logged_at' => now(),
        ]);

        if ($validated['status'] === 'in_progress') {
            ProductionLog::create([
                'production_order_id' => $productionOrder->id,
                'event' => 'Üretim Başlatıldı',
                'description' => 'İş emri üretime alındı.',
                'user_id' => Auth::id(),
                'logged_at' => now(),
            ]);
        }

        if ($validated['status'] === 'completed') {
            ProductionLog::create([
                'production_order_id' => $productionOrder->id,
                'event' => 'Üretim Tamamlandı',
                'description' => 'İş emri tamamlandı olarak işaretlendi.',
                'user_id' => Auth::id(),
                'logged_at' => now(),
            ]);
        }
    }

    return redirect()->route('production_orders.index')->with('success', 'İş emri başarıyla güncellendi.');
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

            // Aynı batch numarasına sahip diğer üretimler
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
                'url' => route('production_orders.show', $order->id),
                'color' => match($order->status) {
                    'planned' => '#6c757d',
                    'in_progress' => '#0d6efd',
                    'completed' => '#198754',
                    'cancelled' => '#dc3545',
                }
            ];
        });

        return view('production_orders.calendar', ['events' => $events]);
    }

    public function process()
    {
        $orders = ProductionOrder::with(['product', 'line', 'formula', 'steps'])
            ->where('status', 'in_progress')
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

        $productionOrder->update(['status' => 'completed']);

        ProductionLog::create([
            'production_order_id' => $productionOrder->id,
            'event' => 'Üretim Tamamlandı',
            'description' => 'Tüm ham maddeler tamamlandı',
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('production_orders.index')->with('success', 'Üretim başarıyla tamamlandı.');
    }
    public function start(ProductionOrder $productionOrder)
{
    $productionOrder->update(['status' => 'in_progress']);

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
        // Önce bağlı adımları sil
        $productionOrder->steps()->delete();

        // Sonra üretim detaylarını sil (varsa)
        $productionOrder->productionFormulaDetails()->delete();

        // Son olarak iş emrini sil
        $productionOrder->delete();

        return redirect()->route('production_orders.index')->with('success', 'İş emri ve bağlı adımlar silindi.');
    }

}