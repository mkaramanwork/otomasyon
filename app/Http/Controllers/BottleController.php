<?php

namespace App\Http\Controllers;

use App\Models\Bottle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BottleController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id'  => 'required|exists:products,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'file'        => 'required|mimes:png,jpg,jpeg,pdf|max:4096',
        ]);

        $path = $request->file('file')->store('bottles', 'public');

        Bottle::create([
            'product_id'  => $request->product_id,
            'name'        => $request->name,
            'description' => $request->description,
            'file_path'   => $path,
        ]);

        return back()->with('success', 'Şişe/ambalaj başarıyla eklendi.');
    }

    public function download(Bottle $bottle)
    {
        if ($bottle->file_path && Storage::disk('public')->exists($bottle->file_path)) {
            $extension = pathinfo($bottle->file_path, PATHINFO_EXTENSION);
            return Storage::disk('public')->download(
                $bottle->file_path,
                $bottle->name . '.' . $extension
            );
        }

        return back()->with('error', 'Dosya bulunamadı.');
    }

    public function destroy(Bottle $bottle)
    {
        if ($bottle->file_path && Storage::disk('public')->exists($bottle->file_path)) {
            Storage::disk('public')->delete($bottle->file_path);
        }

        $bottle->delete();

        return back()->with('success', 'Şişe/ambalaj silindi.');
    }
}