<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    // 📋 Ürün listesi
   public function index()
    {
        $products = Product::with('addedBy')->latest()->get();
        return view('products.index', compact('products'));
    } 

    // 🆕 Yeni ürün formu
    public function create()
    {
        return view('products.create');
    }

    // 💾 Ürün kaydet
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:products,code',
            'unit' => 'required|string|in:kg,lt,adet',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'code' => $request->code,
            'unit' => $request->unit,
            'added_by' => Auth::id(),
        ]);

        return redirect()->route('products.index')
            ->with([
                'success' => 'Ürün başarıyla eklendi. Ürün ekleme modülüne yönlendiriliyorsunuz...',
                'new_product_id' => $product->id
            ]);
    }
    // ✏️ Ürün düzenleme formu
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    // 🔁 Ürün güncelle
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:products,code,' . $product->id,
            'unit' => 'required|string|in:kg,lt,adet',
        ]);

        $product->update([
            'name' => $request->name,
            'code' => $request->code,
            'unit' => $request->unit,
        ]);

        return redirect()->route('products.index')->with('success', 'Ürün başarıyla güncellendi.');
    }

    // 🗑️ Ürün sil
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Ürün silindi.');
    }
}