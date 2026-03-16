<?php

namespace App\Services;

use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;

class StokService
{
    public static function düşümYap($order)
    {
        foreach ($order->productionFormulaDetails as $detail) {
            $hamMadde = $detail->formulaDetail->rawMaterial;
            $miktar = $detail->calculated_amount;

            StockMovement::create([
                'type' => 'düşüm',
                'material_id' => $hamMadde->id,
                'material_type' => get_class($hamMadde),
                'amount' => $miktar,
                'related_recipe_id' => $order->id,
                'triggered_by' => Auth::id(),
            ]);
        }
    }
}