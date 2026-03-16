<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $inventories = Inventory::with('technicalSpecification')->get();
        return view('envanter.index', compact('inventories'));
    }

    public function create()
    {
        return view('envanter.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_code' => 'required|string|max:255',
            'product_name' => 'required|string|max:255',
            'type' => 'required|string',
            'type_other' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        Inventory::create($data);

        return redirect()->route('inventories.index')->with('success','Envanter kaydı eklendi.');
    }

    public function show(Inventory $inventory)
    {
        $inventory->load('technicalSpecification');
        return view('envanter.show', compact('inventory'));
    }

    public function edit(Inventory $inventory)
    {
        return view('envanter.edit', compact('inventory'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $data = $request->validate([
            'product_code' => 'required|string|max:255',
            'product_name' => 'required|string|max:255',
            'type' => 'required|string',
            'type_other' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $inventory->update($data);

        return redirect()->route('inventories.index')->with('success','Envanter kaydı güncellendi.');
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        return redirect()->route('inventories.index')->with('success','Envanter kaydı silindi.');
    }
}