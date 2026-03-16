<?php

namespace App\Http\Controllers;

abstract class Controller
{
public function index()
{
    $events = ProductionOrder::where('status', 'planned')
        ->with('product')
        ->get()
        ->map(function ($order) {
            return [
                'title' => $order->product->name . ' - ' . $order->batch_number,
                'start' => $order->planned_at->format('Y-m-d\TH:i:s'),
                'url' => route('production_orders.show', $order->id),
            ];
        });

    return view('pages.home', compact('events'));
}


}
