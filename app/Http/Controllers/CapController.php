<?php

namespace App\Http\Controllers;

use App\Models\Cap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CapController extends Controller
{
    /**
     * Kapak kaydet
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id'  => 'required|exists:products,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'file'        => 'required|mimes:png,jpg,jpeg,pdf|max:4096',
        ]);

        $path = $request->file('file')->store('caps', 'public');

        Cap::create([
            'product_id'  => $request->product_id,
            'name'        => $request->name,
            'description' => $request->description,
            'file_path'   => $path,
        ]);

        return back()->with('success', 'Kapak başarıyla eklendi.');
    }

    /**
     * Kapak indir
     */
    public function download(Cap $cap)
    {
        if ($cap->file_path && Storage::disk('public')->exists($cap->file_path)) {
            $extension = pathinfo($cap->file_path, PATHINFO_EXTENSION);
            return Storage::disk('public')->download(
                $cap->file_path,
                $cap->name . '.' . $extension
            );
        }

        return back()->with('error', 'Dosya bulunamadı.');
    }

    /**
     * Kapak sil
     */
    public function destroy(Cap $cap)
    {
        if ($cap->file_path && Storage::disk('public')->exists($cap->file_path)) {
            Storage::disk('public')->delete($cap->file_path);
        }

        $cap->delete();

        return back()->with('success', 'Kapak silindi.');
    }
}