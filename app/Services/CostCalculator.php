<?php

namespace App\Services;

use App\Models\Formula;

class CostCalculator
{
    public function calculate(Formula $formula)
    {
        // Ham madde maliyeti
        $rawCost = $formula->details->sum(function($detail) {
            $material = $detail->rawMaterial;

            // Aktif para birimine göre fiyat seçimi
            switch ($material->currency) {
                case 'USD':
                    $price = $material->unit_price_usd;
                    break;
                case 'EUR':
                    $price = $material->unit_price_eur;
                    break;
                default:
                    $price = $material->unit_price_tl;
            }

            return $detail->amount * $price;
        });

        // Ambalaj maliyeti
        $packaging = $formula->packaging ? $formula->packaging->price : 0;

        // Ek maliyetler
        $labor    = $formula->labor_cost;
        $shipping = $formula->shipping_cost;
        $tax      = $formula->tax;

        // Kar marjı
        $profit = ($rawCost + $packaging + $labor + $shipping + $tax) * ($formula->profit_margin / 100);

        // Ara toplam
        $subtotal = $rawCost + $packaging + $labor + $shipping + $tax + $profit;

        // KDV
        $vat = $subtotal * ($formula->vat / 100);

        // Son toplam
        return [
            'rawCost'   => $rawCost,
            'packaging' => $packaging,
            'labor'     => $labor,
            'shipping'  => $shipping,
            'tax'       => $tax,
            'profit'    => $profit,
            'vat'       => $vat,
            'totalCost' => $subtotal + $vat,
        ];
    }
}