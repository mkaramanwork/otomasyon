<?php

namespace App\Http\Controllers;

use App\Models\ProductionLine;
use Illuminate\Http\Request;

class ProductionLineController extends Controller
{
    // Listeleme
    public function index()
    {
        $productionLines = ProductionLine::with('addedBy')->latest()->get();
        return view('production-lines.index', compact('productionLines'));
    }

    // Ekleme formu
    public function create()
    {
        return view('production-lines.create');
    }

    // Veriyi kaydet
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:harmanlama,dolum',
            'capacity_litre' => 'nullable|integer|min:0',
            'description' => 'nullable|string'
        ]);

        ProductionLine::create([
            'name' => $request->name,
            'type' => $request->type,
            'capacity_litre' => $request->capacity_litre,
            'description' => $request->description,
            'added_by' => auth()->id()
        ]);

        return redirect()->route('production-lines.index')->with('success', 'Üretim hattı başarıyla eklendi.');
    }

    // Düzenleme formu
    public function edit(ProductionLine $productionLine)
    {
        return view('production-lines.edit', compact('productionLine'));
    }

    // Güncelleme işlemi
    public function update(Request $request, ProductionLine $productionLine)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:harmanlama,dolum',
            'capacity_litre' => 'nullable|integer|min:0',
            'description' => 'nullable|string'
        ]);

        $productionLine->update($request->all());

        return redirect()->route('production-lines.index')->with('success', 'Üretim hattı başarıyla güncellendi.');
    }

    // Silme işlemi
    public function destroy(ProductionLine $productionLine)
    {
        $productionLine->delete();
        return redirect()->route('production-lines.index')->with('success', 'Üretim hattı silindi.');
    }
}