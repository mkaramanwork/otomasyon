<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Denetim;
use Illuminate\Support\Facades\Auth;

class DenetimController extends Controller
{
    public function index()
    {
        $documents = Denetim::with('user')->get();
        return view('denetim.index', compact('documents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|mimes:pdf|max:20480',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'category' => 'required|string',
        ]);

        $path = $request->file('file')->store('denetim', 'public');

        Denetim::create([
            'title' => $request->title,
            'category' => $request->category,
            'file_path' => 'storage/' . $path,
            'description' => $request->description,
            'date' => $request->date,
            'uploaded_by' => Auth::id(),
        ]);

        return back()->with('success', 'Doküman başarıyla yüklendi ✅');
    }

    public function destroy($id)
    {
        $doc = Denetim::findOrFail($id);
        $doc->delete();
        return back()->with('success', 'Doküman silindi ❌');
    }
}