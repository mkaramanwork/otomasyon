<?php

namespace App\Http\Controllers;

use App\Models\Label;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LabelController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id'  => 'required|exists:products,id',
            'name'        => 'required|string|max:255',
            'type'        => 'required|string',
            'file'        => 'required|mimes:png,jpg,jpeg,pdf|max:2048',
            'description' => 'nullable|string',
        ]);

        // Dosyayı kaydet
        $path = $request->file('file')->store('etiketler', 'public');

        Label::create([
            'product_id'  => $request->product_id,
            'name'        => $request->name,
            'type'        => $request->type,
            'description' => $request->description,
            'file_path'   => $path,
        ]);

        return back()->with('success', 'Etiket görseli başarıyla yüklendi.');
    }

    public function download(Label $label)
    {
        if ($label->file_path && Storage::disk('public')->exists($label->file_path)) {
            return Storage::disk('public')->download($label->file_path, $label->name.'.'.pathinfo($label->file_path, PATHINFO_EXTENSION));
        }

        return back()->with('error', 'Dosya bulunamadı.');
    }

    public function destroy(Label $label)
    {
        if ($label->file_path && Storage::disk('public')->exists($label->file_path)) {
            Storage::disk('public')->delete($label->file_path);
        }

        $label->delete();

        return back()->with('success', 'Etiket görseli silindi.');
    }
}