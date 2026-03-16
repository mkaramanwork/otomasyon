<?php

namespace App\Http\Controllers;

use App\Models\SalesProduct;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SalesProductsImport;

class SalesProductController extends Controller
{
    // Listeleme
    public function index()
    {
        $products = SalesProduct::all();
        return view('sales_products.index', compact('products'));
    }

    // Yeni ürün formu
    public function create()
    {
        return view('sales_products.create');
    }

    // Yeni ürün kaydetme
    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'performance_class' => 'nullable|string|max:50',
            'volume'            => 'nullable|numeric|min:0',
            'price_usd'         => 'required|numeric|min:0',
            'price_try'         => 'required|numeric|min:0',
        ]);

        SalesProduct::create($request->all());
        return redirect()->route('sales_products.index')->with('success', 'Ürün başarıyla eklendi.');
    }

    // Düzenleme formu
    public function edit(SalesProduct $salesProduct)
    {
        return view('sales_products.edit', compact('salesProduct'));
    }

    // Güncelleme
    public function update(Request $request, SalesProduct $salesProduct)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'performance_class' => 'nullable|string|max:50',
            'volume'            => 'nullable|numeric|min:0',
            'price_usd'         => 'required|numeric|min:0',
            'price_try'         => 'required|numeric|min:0',
        ]);

        $salesProduct->update($request->all());
        return redirect()->route('sales_products.index')->with('success', 'Ürün başarıyla güncellendi.');
    }

    // Silme
    public function destroy(SalesProduct $salesProduct)
    {
        $salesProduct->delete();
        return redirect()->route('sales_products.index')->with('success', 'Ürün silindi.');
    }

    // Excel import
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new SalesProductsImport, $request->file('file'));

        return redirect()->route('sales_products.index')->with('success', 'Excel dosyası başarıyla içe aktarıldı.');
    }
}