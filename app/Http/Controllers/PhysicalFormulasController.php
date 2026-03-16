<?php

namespace App\Http\Controllers;

use App\Models\PhysicalFormula;
use App\Models\PhysicalFormulaDetail;
use App\Models\PhysicalParameter;
use Illuminate\Http\Request;

class PhysicalFormulasController extends Controller
{
    public function index()
    {
        $formulas = PhysicalFormula::orderByDesc('id')->get();
        return view('physcial_formula.index', compact('formulas'));
    }

    public function create(Request $request)
    {
        // route('physcial_formula.create', ['product_id' => $product->id]) ile çağrıldığında product_id gelir
        $productId = $request->get('product_id');

        return view('physcial_formula.create', compact('productId'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id'   => 'required|exists:products,id',
            'product_code' => 'required|string|max:50',
            'product_name' => 'required|string|max:255',
            'unit'         => 'nullable|string|max:50',
            'description'  => 'nullable|string',
        ]);

        $formula = PhysicalFormula::create($validated);

        return redirect()->route('physcial_formula.details', ['id' => $formula->id]);
    }

    public function edit(PhysicalFormula $physcial_formula)
    {
        return view('physcial_formula.edit', compact('physcial_formula'));
    }

    public function update(Request $request, PhysicalFormula $physcial_formula)
    {
        $validated = $request->validate([
            'product_code' => 'required|string|max:50',
            'product_name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $physcial_formula->update($validated);

        return redirect()->route('physcial_formula.index')->with('success', 'Reçete güncellendi.');
    }

    public function destroy(PhysicalFormula $physcial_formula)
    {
        $physcial_formula->delete();
        return redirect()->route('physcial_formula.index')->with('success', 'Reçete silindi.');
    }

    public function details($id)
    {
        $formula = PhysicalFormula::findOrFail($id);
        $details = $formula->details()->with('parameter')->get();
        $totalQuantity = $details->sum('quantity');

        return view('physcial_formula.details', compact('formula', 'details', 'totalQuantity'));
    }

    public function addDetail($id)
    {
        $formula = PhysicalFormula::findOrFail($id);
        $parameters = PhysicalParameter::orderBy('group')->get();

        return view('physcial_formula.add_detail', compact('formula', 'parameters'));
    }

    public function storeDetail(Request $request)
    {
        $validated = $request->validate([
            'physical_formula_id'   => 'required|exists:physical_formulas,id',
            'physical_parameter_id' => 'required|exists:physical_parameters,id',
            'selection_label'       => 'required|string|max:100',
            'quantity'              => 'required|numeric|min:0.001',
        ]);

        PhysicalFormulaDetail::create($validated);

        $formula = PhysicalFormula::findOrFail($validated['physical_formula_id']);
        $link = route('urunAgaci.show', $formula->product_id);

        return back()->with('success', 
            'Parametre başarıyla eklendi. Reçete tamamlandıysa ürün sicil kartına dönebilirsiniz: 
            <a href="'.$link.'" class="alert-link">Ürün Kartına Dön</a>'
        );
    }

}