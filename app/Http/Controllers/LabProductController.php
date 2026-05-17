<?php

namespace App\Http\Controllers;

use App\Models\LabProduct;
use App\Models\LabProductParameter;
use App\Models\LabParameter;
use Illuminate\Http\Request;

class LabProductController extends Controller
{
    // Eksik parametreleri tamamlayan yardımcı fonksiyon
    private function syncParameters(LabProduct $labProduct)
    {
        $allParams = LabParameter::all();
        foreach ($allParams as $param) {
            LabProductParameter::firstOrCreate(
                [
                    'lab_product_id' => $labProduct->id,
                    'parameter_id'   => $param->id,
                ],
                [
                    'min_value' => '-',
                    'max_value' => '-',
                    'net_value' => '-',
                ]
            );
        }
    }

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

        // Eksik parametreleri tamamla
        $this->syncParameters($labProduct);

        return redirect()->route('lab_products.index')
                         ->with('success','Ürün ve parametreler başarıyla kaydedildi.');
    }

    public function show(LabProduct $labProduct)
    {
        $this->syncParameters($labProduct);
        $labProduct->load('parameters.parameter');
        return view('lab_products.show', compact('labProduct'));
    }

    public function edit(LabProduct $labProduct)
    {
        $this->syncParameters($labProduct);
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

        // Eksik parametreleri yine tamamla
        $this->syncParameters($labProduct);

        return redirect()->route('lab_products.index')->with('success','Ürün ve parametreler başarıyla güncellendi.');
    }

    public function destroy(LabProduct $labProduct)
    {
        $labProduct->delete();
        return redirect()->route('lab_products.index')->with('success','Ürün silindi.');
    }
}
