<?php

namespace App\Http\Controllers;

use App\Models\LabProduct;
use App\Models\LabProductParameter;
use App\Models\LabParameter;
use Illuminate\Http\Request;

class LabProductController extends Controller
{
    public function index()
    {
        $labProducts = LabProduct::with('parameters.parameter')->latest()->get();
        return view('lab_products.index', compact('labProducts'));
    }

    public function create()
    {
        $parameters = LabParameter::all();
        return view('lab_products.create', compact('parameters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_code' => 'required|string|max:50',
            'product_name' => 'required|string|max:255',
            'parameters'   => 'required|array',
        ]);

        // Ürün kaydı
        $labProduct = LabProduct::create([
            'product_code' => $request->product_code,
            'product_name' => $request->product_name,
        ]);

        // Parametreleri kaydet
        foreach ($request->parameters as $paramId => $values) {
            LabProductParameter::create([
                'lab_product_id' => $labProduct->id,
                'parameter_id'   => $paramId,
                'min_value'      => $values['min'] ?? '-',
                'max_value'      => $values['max'] ?? '-',
                'net_value'      => $values['net'] ?? '-',
            ]);
        }

        return redirect()->route('lab_products.index')
                         ->with('success','Ürün ve parametreler başarıyla kaydedildi.');
    }

    public function show(LabProduct $labProduct)
    {
        $labProduct->load('parameters.parameter');
        return view('lab_products.show', compact('labProduct'));
    }

    public function edit(LabProduct $labProduct)
    {
        $parameters = LabParameter::all();
        $labProduct->load('parameters.parameter');
        return view('lab_products.edit', compact('labProduct','parameters'));
    }

    public function update(Request $request, LabProduct $labProduct)
    {
        $labProduct->update([
            'product_code' => $request->product_code,
            'product_name' => $request->product_name,
        ]);

        if ($request->has('parameters')) {
            foreach ($request->parameters as $id => $values) {
                $lp = LabProductParameter::find($id);
                if ($lp) {
                    $lp->update([
                        'min_value' => $values['min'] ?? '-',
                        'max_value' => $values['max'] ?? '-',
                        'net_value' => $values['net'] ?? '-',
                    ]);
                }
            }
        }

        return redirect()->route('lab_products.index')->with('success','Ürün ve parametreler başarıyla güncellendi.');
    }

    public function destroy(LabProduct $labProduct)
    {
        $labProduct->delete();
        return redirect()->route('lab_products.index')->with('success','Ürün silindi.');
    }
}
