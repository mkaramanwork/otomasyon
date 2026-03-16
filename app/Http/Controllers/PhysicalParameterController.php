<?php

namespace App\Http\Controllers;

use App\Models\PhysicalParameter;
use Illuminate\Http\Request;

class PhysicalParameterController extends Controller
{
    // Listeleme
    public function index()
    {
        $parameters = PhysicalParameter::orderBy('group')->get();
        return view('physical_parameter.index', compact('parameters'));
    }

    // Yeni parametre formu
    public function create()
    {
        return view('physical_parameter.create');
    }

    // Kaydetme
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'group' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        PhysicalParameter::create($validated);
        return redirect()->route('physical_parameter.index')->with('success', 'Parametre başarıyla eklendi.');
    }

    // Düzenleme formu
    public function edit(PhysicalParameter $physical_parameter)
    {
        return view('physical_parameter.edit', ['physicalParameter' => $physical_parameter]);
    }

    // Güncelleme
    public function update(Request $request, PhysicalParameter $physical_parameter)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'group' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $physical_parameter->update($validated);
        return redirect()->route('physical_parameter.index')->with('success', 'Parametre güncellendi.');
    }

    // Silme
    public function destroy(PhysicalParameter $physical_parameter)
    {
        $physical_parameter->delete();
        return redirect()->route('physical_parameter.index')->with('success', 'Parametre silindi.');
    }
}