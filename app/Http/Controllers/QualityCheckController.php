<?php

namespace App\Http\Controllers;

use App\Models\QualityCheck;
use App\Models\RawMaterial;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QualityCheckController extends Controller
{
    // Listeleme
    public function index()
    {
        $checks = QualityCheck::with('rawMaterial', 'user')->latest()->paginate(25);
        return view('quality_checks.index', compact('checks'));
    }

    // Formu göster
    public function create()
    {
        $rawMaterials = RawMaterial::all();
        return view('quality_checks.create', compact('rawMaterials'));
    }

    // Kaydı oluştur
    public function store(Request $request)
    {
        $validated = $request->validate([
            'raw_material_id' => 'required|exists:raw_materials,id',
            'batch_number' => 'nullable|string',
            'delivery_note_number' => 'nullable|string',
            'received_at' => 'required|date',
            'supplier_name' => 'nullable|string',
            'kategori' => 'required|in:katık,baz yağ,meg',
            'amount' => 'required|numeric|min:0.01',
            'unit' => 'required|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:bekliyor,onaylandı,reddedildi',
        ]);

        $check = QualityCheck::create([
            ...$validated,
            'checked_by' => Auth::id(),
        ]);

        // Otomatik stok girişi
        StockMovement::create([
            'type' => 'in', // ✅ enum ile uyumlu
            'material_id' => $validated['raw_material_id'],
            'material_type' => \App\Models\RawMaterial::class,
            'amount' => $validated['amount'],
            'related_recipe_id' => null,
            'triggered_by' => auth()->id(),
        ]);


        return redirect()->route('quality-checks.index')->with('success', 'Kalite kontrol ve stok girişi tamamlandı.');
    }
}