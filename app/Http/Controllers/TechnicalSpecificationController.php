<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\TechnicalSpecification;
use Illuminate\Http\Request;

class TechnicalSpecificationController extends Controller

{
    public function create(Inventory $inventory)
    {
        return view('envanter.technicalspec_create', compact('inventory'));
    }

    public function store(Request $request, Inventory $inventory)
    {
        $data = $request->validate([
            'spec_title' => 'required|string|max:255',
            'spec_detail' => 'nullable|string',
            'standards' => 'nullable|string',
            'compliance' => 'nullable|string',
            'document_code' => 'nullable|string|max:255',
        ]);

        $inventory->technicalSpecification()->create($data);

        return redirect()->route('inventories.show',$inventory->id)
                         ->with('success','Teknik şartname eklendi.');
    }

    public function edit(TechnicalSpecification $spec)
    {
        return view('envanter.technicalspec_edit', compact('spec'));
    }

    public function update(Request $request, TechnicalSpecification $spec)
    {
        $data = $request->validate([
            'spec_title' => 'required|string|max:255',
            'spec_detail' => 'nullable|string',
            'standards' => 'nullable|string',
            'compliance' => 'nullable|string',
            'document_code' => 'nullable|string|max:255',
        ]);

        $spec->update($data);

        return redirect()->route('inventories.show',$spec->inventory_id)
                         ->with('success','Teknik şartname güncellendi.');
    }

    public function destroy(TechnicalSpecification $spec)
    {
        $inventoryId = $spec->inventory_id;
        $spec->delete();

        return redirect()->route('inventories.show',$inventoryId)
                         ->with('success','Teknik şartname silindi.');
    }
}