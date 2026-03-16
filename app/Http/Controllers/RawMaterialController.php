<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RawMaterialController extends Controller
{
    public function index()
    {
        $rawMaterials = RawMaterial::with('user')->latest()->paginate(10);
        return view('rawmaterials.index', compact('rawMaterials'));
    }

    public function create()
    {
        return view('rawmaterials.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:10',
            'stock_kg' => 'required|numeric|min:0',
            'unit_price_tl' => 'required|numeric|min:0',
            'unit_price_usd' => 'nullable|numeric|min:0',
            'unit_price_eur' => 'nullable|numeric|min:0',
            'currency' => 'required|string|in:TL,USD,EUR',
        ]);

        RawMaterial::create([
            'name' => $request->name,
            'unit' => $request->unit,
            'stock_kg' => $request->stock_kg,
            'unit_price_tl' => $request->unit_price_tl,
            'unit_price_usd' => $request->unit_price_usd ?? 0,
            'unit_price_eur' => $request->unit_price_eur ?? 0,
            'currency' => $request->currency,
            'added_by' => Auth::id(),
        ]);

        return redirect()->route('raw-materials.index')->with('success', 'Ham madde başarıyla eklendi.');
    }

    public function edit(RawMaterial $rawMaterial)
    {
        return view('rawmaterials.edit', compact('rawMaterial'));
    }

    public function update(Request $request, RawMaterial $rawMaterial)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:10',
            'stock_kg' => 'required|numeric|min:0',
            'unit_price_tl' => 'required|numeric|min:0',
            'unit_price_usd' => 'nullable|numeric|min:0',
            'unit_price_eur' => 'nullable|numeric|min:0',
            'currency' => 'required|string|in:TL,USD,EUR',
        ]);

        $rawMaterial->update($request->all());

        return redirect()->route('raw-materials.index')->with('success', 'Ham madde güncellendi.');
    }

    public function destroy(RawMaterial $rawMaterial)
    {
        $rawMaterial->delete();
        return redirect()->route('raw-materials.index')->with('success', 'Ham madde silindi.');
    }
}