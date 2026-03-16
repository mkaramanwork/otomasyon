<?php

namespace App\Http\Controllers;

use App\Models\Filling;
use App\Models\ProductionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FillingController extends Controller
{
    /**
     * Dolumdaki iş emirlerini listeler
     */
    public function index()
    {
        $fillings = Filling::with([
            'productionOrder.product',
            'productionOrder.line',
            'physicalFormula'
        ])
        ->whereHas('productionOrder', fn($q) => $q->where('status', 'in_filling'))
        ->get();

        return view('production_orders.filling', compact('fillings'));
    }
    

    /**
     * Dolumu adet girerek tamamlar
     */
    public function complete(Request $request, Filling $filling)
    {
        $validated = $request->validate([
            'filled_quantity' => 'required|integer|min:1',
        ]);

        // Dolum bilgilerini güncelle
        $filling->update([
            'filled_quantity' => $validated['filled_quantity'],
            'status' => 'completed',
        ]);

        // İlgili iş emrini güncelle
        $filling->productionOrder->update([
            'status' => 'filled',
        ]);

        // Log kaydı oluştur
        ProductionLog::create([
            'production_order_id' => $filling->production_order_id,
            'event' => 'Dolum Tamamlandı',
            'description' => 'Dolum işlemi tamamlandı. Adet: ' . $validated['filled_quantity'],
            'user_id' => Auth::id(),
            'logged_at' => now(),
        ]);

        return back()->with('success', 'Dolum başarıyla tamamlandı.');
    }
}