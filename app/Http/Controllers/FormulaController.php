<?php

namespace App\Http\Controllers;

use App\Models\Formula;
use App\Models\FormulaDetail;
use App\Models\RawMaterial;
use App\Services\CostCalculator;
use Illuminate\Http\Request;

class FormulaController extends Controller
{
    public function index()
    {
        $formulas = Formula::with(['addedBy','details.rawMaterial'])->latest()->get();
        return view('formulas.index', compact('formulas'));
    }

    public function create(Request $request)
    {
        $materials = RawMaterial::all();
        $productId = $request->get('product_id');
        return view('formulas.create', compact('materials', 'productId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'description' => 'nullable|string',
            'materials' => 'required|array|min:1',
            'materials.*.raw_material_id' => 'required|exists:raw_materials,id',
            'materials.*.percentage' => 'required|numeric|min:0|max:100',
            'materials.*.unit' => 'nullable|string|max:10',
        ]);

        $totalPercentage = collect($request->materials)->sum('percentage');
        if ($totalPercentage != 100) {
            return back()->withErrors(['materials' => 'Ham madde oranlarının toplamı %100 olmalıdır.'])->withInput();
        }

        $formula = Formula::create([
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'product_id' => $request->product_id,
            'packaging_cost' => $request->packaging_cost ?? 0,
            'labor_cost' => $request->labor_cost ?? 0,
            'shipping_cost' => $request->shipping_cost ?? 0,
            'tax' => $request->tax ?? 0,
            'profit_margin' => $request->profit_margin ?? 0,
            'vat' => $request->vat ?? 20,
            'added_by' => auth()->id(),
        ]);

        foreach ($request->materials as $item) {
            FormulaDetail::create([
                'formula_id' => $formula->id,
                'raw_material_id' => $item['raw_material_id'],
                'percentage' => $item['percentage'],
                'unit' => $item['unit'] ?? null,
            ]);
        }

        // maliyet hesapla ve kaydet
        $formula->load('details.rawMaterial'); // detayları yükle
        $calculator = new CostCalculator();
        $costs = $calculator->calculate($formula);
        $formula->update(['total_cost' => $costs['totalCost']]);

        return redirect()->route('formulas.index')
            ->with('success', 'Formülasyon başarıyla kaydedildi.');
    }

    public function edit($id)
    {
        $formula = Formula::with('details')->findOrFail($id);
        $materials = RawMaterial::all();
        return view('formulas.edit', compact('formula', 'materials'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'description' => 'nullable|string',
            'materials' => 'required|array|min:1',
            'materials.*.raw_material_id' => 'required|exists:raw_materials,id',
            'materials.*.percentage' => 'required|numeric|min:0|max:100',
            'materials.*.unit' => 'nullable|string|max:10',
        ]);

        $totalPercentage = collect($request->materials)->sum('percentage');
        if ($totalPercentage != 100) {
            return back()->withErrors(['materials' => 'Ham madde oranlarının toplamı %100 olmalıdır.'])->withInput();
        }

        $formula = Formula::findOrFail($id);
        $formula->update([
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'packaging_cost' => $request->packaging_cost ?? 0,
            'labor_cost' => $request->labor_cost ?? 0,
            'shipping_cost' => $request->shipping_cost ?? 0,
            'tax' => $request->tax ?? 0,
            'profit_margin' => $request->profit_margin ?? 0,
            'vat' => $request->vat ?? 20,
        ]);

        $formula->details()->delete();
        foreach ($request->materials as $item) {
            FormulaDetail::create([
                'formula_id' => $formula->id,
                'raw_material_id' => $item['raw_material_id'],
                'percentage' => $item['percentage'],
                'unit' => $item['unit'] ?? null,
            ]);
        }

        // maliyet hesapla ve güncelle
        $formula->load('details.rawMaterial'); // detayları yükle
        $calculator = new CostCalculator();
        $costs = $calculator->calculate($formula);
        $formula->update(['total_cost' => $costs['totalCost']]);

        return redirect()->route('formulas.index')
            ->with('success', 'Formülasyon güncellendi.');
    }

    public function destroy($id)
    {
        $formula = Formula::findOrFail($id);
        $formula->details()->delete();
        $formula->delete();

        return redirect()->route('formulas.index')->with('success', 'Formülasyon silindi.');
    }
}
