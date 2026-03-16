<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SalesProduct;
use Illuminate\Http\Request;

use App\Exports\OrderExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrdersExport;



class OrderController extends Controller
{
    // Listeleme
    public function index()
    {
        $orders = Order::with(['items.product','user'])->latest()->get();
        return view('orders.index', compact('orders'));
    }

    // Yeni sipariş formu
    public function create()
    {
        $salesProducts = SalesProduct::all();
        return view('orders.create', compact('salesProducts'));
    }

    // Kaydetme
    public function store(Request $request)
    {
        $order = Order::create([
            'customer_name'  => $request->customer_name,
            'customer_email' => $request->customer_email,
            'type'           => $request->type,
            'status'         => $request->status,
            'total_price'    => 0,
            'total_price_usd'=> 0,
            'user_id'        => auth()->id(),
        ]);

        $totalTry = 0;
        $totalUsd = 0;

        foreach ($request->products as $productData) {
            $product   = SalesProduct::find($productData['id']);
            $quantity  = $productData['quantity'];
            $discount  = $productData['discount'] ?? 0;

            $unitPriceTry = $product->price_try;
            $unitPriceUsd = $product->price_usd;

            $discountedPriceTry = $unitPriceTry - ($unitPriceTry * $discount / 100);
            $discountedPriceUsd = $unitPriceUsd - ($unitPriceUsd * $discount / 100);

            $lineTotalTry = $discountedPriceTry * $quantity;
            $lineTotalUsd = $discountedPriceUsd * $quantity;

            $order->items()->create([
                'sales_product_id'=> $product->id,
                'quantity'        => $quantity,
                'unit_price'      => $unitPriceTry,
                'total_price'     => $lineTotalTry,
                'discount'        => $discount,
                'status'          => 'beklemede',
            ]);

            $totalTry += $lineTotalTry;
            $totalUsd += $lineTotalUsd;
        }

        $globalDiscount = $request->global_discount ?? 0;
        $finalTotalTry = $totalTry - ($totalTry * $globalDiscount / 100);
        $finalTotalUsd = $totalUsd - ($totalUsd * $globalDiscount / 100);

        $order->update([
            'total_price'     => $finalTotalTry,
            'total_price_usd' => $finalTotalUsd,
        ]);

        return redirect()->route('orders.index')->with('success', 'Sipariş/Teklif başarıyla oluşturuldu.');
    }

    // Düzenleme formu
    public function edit(Order $order)
    {
        $salesProducts = SalesProduct::all();
        $order->load('items.product');
        return view('orders.edit', compact('order','salesProducts'));
    }

    // Güncelleme
    public function update(Request $request, Order $order)
    {
        $order->update([
            'customer_name'  => $request->customer_name,
            'customer_email' => $request->customer_email,
            'type'           => $request->type,
            'status'         => $request->status,
        ]);

        $order->items()->delete();

        $totalTry = 0;
        $totalUsd = 0;

        foreach ($request->products as $productData) {
            $product   = SalesProduct::find($productData['id']);
            $quantity  = $productData['quantity'];
            $discount  = $productData['discount'] ?? 0;

            $unitPriceTry = $product->price_try;
            $unitPriceUsd = $product->price_usd;

            $discountedPriceTry = $unitPriceTry - ($unitPriceTry * $discount / 100);
            $discountedPriceUsd = $unitPriceUsd - ($unitPriceUsd * $discount / 100);

            $lineTotalTry = $discountedPriceTry * $quantity;
            $lineTotalUsd = $discountedPriceUsd * $quantity;

            $order->items()->create([
                'sales_product_id'=> $product->id,
                'quantity'        => $quantity,
                'unit_price'      => $unitPriceTry,
                'total_price'     => $lineTotalTry,
                'discount'        => $discount,
                'status'          => 'beklemede',
            ]);

            $totalTry += $lineTotalTry;
            $totalUsd += $lineTotalUsd;
        }

        $globalDiscount = $request->global_discount ?? 0;
        $finalTotalTry = $totalTry - ($totalTry * $globalDiscount / 100);
        $finalTotalUsd = $totalUsd - ($totalUsd * $globalDiscount / 100);

        $order->update([
            'total_price'     => $finalTotalTry,
            'total_price_usd' => $finalTotalUsd,
        ]);

        return redirect()->route('orders.index')->with('success', 'Sipariş/Teklif güncellendi.');
    }

    // Silme
    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Sipariş/Teklif silindi.');
    }

    // Ürün itemini tamamlandı işaretleme
    public function completeItem(OrderItem $item)
    {
        $item->update(['status' => 'tamamlandı']);
        return back()->with('success','Ürün tamamlandı olarak işaretlendi.');
    }

    public function export(Order $order)
    {
        return Excel::download(new OrderExport($order), 'order_'.$order->id.'.xlsx');
    }

}