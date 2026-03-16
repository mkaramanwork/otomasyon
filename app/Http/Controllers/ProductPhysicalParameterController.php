<?php

namespace App\Http\Controllers;

use App\Models\PhysicalFormula;
use Illuminate\Http\Request;

class ProductPhysicalParameterController extends Controller
{
    public function index()
    {
        $formulas = PhysicalFormula::orderByDesc('id')->get();
        return view('product_physical.index', compact('formulas'));
    }

    public function create()
    {
        return view('product_physical.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_code' => 'required|string|max:50',
            'product_name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        PhysicalFormula::create($validated);

        return redirect()->route('product_physical.index')->with('success', 'Fiziksel reçete oluşturuldu.');
    }

    public function edit(PhysicalFormula $physicalFormula)
    {
        return view('product_physical.edit', compact('physicalFormula'));
    }

    public function update(Request $request, PhysicalFormula $physicalFormula)
    {
        $validated = $request->validate([
            'product_code' => 'required|string|max:50',
            'product_name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $physicalFormula->update($validated);

        return redirect()->route('product_physical.index')->with('success', 'Fiziksel reçete güncellendi.');
    }

    public function destroy(PhysicalFormula $physicalFormula)
    {
        $physicalFormula->delete();
        return redirect()->route('product_physical.index')->with('success', 'Fiziksel reçete silindi.');
    }
}