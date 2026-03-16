<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Filling;
use App\Models\ProductionOrder;
use App\Models\User;
use App\Models\StockMovement;
use App\Models\Product;         

class ReportController extends Controller
{
    /**
     * Dolum raporu sayfası (filtreli)
     */
    public function fillings(Request $request)
    {
        $fillings = Filling::with([
            'productionOrder.product',
            'fillingLine',
            'physicalFormula.details.parameter',
            'productionOrder.formula.details.rawMaterial',
            'productionOrder.logs.user'
        ])
        ->when($request->filled('start_date'), fn($q) => $q->whereDate('created_at', '>=', $request->start_date))
        ->when($request->filled('end_date'), fn($q) => $q->whereDate('created_at', '<=', $request->end_date))
        ->when($request->filled('batch'), function ($q) use ($request) {
            $q->whereHas('productionOrder', fn($sub) => $sub->where('batch_number', 'like', '%' . $request->batch . '%'));
        })
        ->latest()
        ->get();

        return view('reports.fillings', compact('fillings'));
    }

    /**
     * Stok hareketleri raporu (giriş + çıkış)
     */
    public function stochReport(Request $request)
    {
        $movements = StockMovement::with([
            'material',
            'triggeredBy',
            'productionOrder.product'
        ])
        ->when($request->filled('start_date'), fn($q) => $q->whereDate('created_at', '>=', $request->start_date))
        ->when($request->filled('end_date'), fn($q) => $q->whereDate('created_at', '<=', $request->end_date))
        ->when($request->filled('material'), function ($q) use ($request) {
            $q->whereHasMorph('material', '*', function ($sub) use ($request) {
                $sub->where('name', 'like', '%' . $request->material . '%');
            });
        })
        ->latest()
        ->get();

        return view('reports.stoch', compact('movements', 'request'));
    }


    public function stockCard($productId)
{
    $product = Product::findOrFail($productId);

    $movements = StockMovement::where('material_id',$productId)
        ->where('material_type', Product::class)
        ->orderBy('created_at')
        ->get();

    // Güncel stok hesaplama
    $stock = 0;
    foreach($movements as $m){
        if($m->type === 'in') $stock += $m->amount;
        elseif($m->type === 'out') $stock -= $m->amount;
        elseif($m->type === 'adjustment') $stock = $m->amount;
    }

    return view('reports.card', compact('product','movements','stock'));
}

public function stockAdjustment(Request $request, $productId)
{
    $request->validate([
        'counted_qty' => 'required|numeric|min:0',
    ]);

    StockMovement::create([
        'type'          => 'adjustment',
        'material_id'   => $productId,
        'material_type' => Product::class,
        'amount'        => $request->counted_qty,
        'reference'     => 'Depo Sayımı',
        'triggered_by'  => Auth::id(),
    ]);

    return redirect()->route('reports.card',$productId)
        ->with('success','Sayım sonucu kaydedildi, stok güncellendi.');
}

}