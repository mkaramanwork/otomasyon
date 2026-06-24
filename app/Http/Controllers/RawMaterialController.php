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

            // ✅ yeni alanlar
            'semi_doc' => 'nullable|file|mimes:pdf|max:2048',
            'usage_rate' => 'nullable|numeric|min:0|max:100',
            'performance_level' => 'nullable|string',
            'competitor_product' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        $data['added_by'] = Auth::id();

        if ($request->hasFile('semi_doc')) {
            $path = $request->file('semi_doc')->store('rawmaterials', 'public');
            $data['semi_doc'] = $path;
        }

        RawMaterial::create($data);

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

          
            'semi_doc' => 'nullable|file|mimes:pdf|max:2048',
            'usage_rate' => 'nullable|numeric|min:0|max:100',
            'performance_level' => 'nullable|string',
            'competitor_product' => 'nullable|string|max:255',
        ]);

        $data = $request->all();

        if ($request->hasFile('semi_doc')) {
            $path = $request->file('semi_doc')->store('rawmaterials', 'public');
            $data['semi_doc'] = $path;
        }

        $rawMaterial->update($data);

        return redirect()->route('raw-materials.index')->with('success', 'Ham madde güncellendi.');
    }

    public function destroy(RawMaterial $rawMaterial)
    {
        $rawMaterial->delete();
        return redirect()->route('raw-materials.index')->with('success', 'Ham madde silindi.');
    }
}
