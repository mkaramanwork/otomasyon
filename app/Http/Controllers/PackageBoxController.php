<?php

namespace App\Http\Controllers;

use App\Models\PackageBox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PackageBoxController extends Controller
{
    /**
     * Koli görseli kaydet
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id'  => 'required|exists:products,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'file'        => 'required|mimes:png,jpg,jpeg,pdf|max:4096',
        ]);

        // Dosyayı kaydet
        $path = $request->file('file')->store('package_boxes', 'public');

        PackageBox::create([
            'product_id'  => $request->product_id,
            'name'        => $request->name,
            'description' => $request->description,
            'file_path'   => $path,
        ]);

        return back()->with('success', 'Koli görseli başarıyla yüklendi.');
    }

    /**
     * Koli görselini indir
     */
    public function download(PackageBox $packageBox)
    {
        if ($packageBox->file_path && Storage::disk('public')->exists($packageBox->file_path)) {
            $extension = pathinfo($packageBox->file_path, PATHINFO_EXTENSION);
            return Storage::disk('public')->download(
                $packageBox->file_path,
                $packageBox->name . '.' . $extension
            );
        }

        return back()->with('error', 'Dosya bulunamadı.');
    }

    /**
     * Koli görselini sil
     */
    public function destroy(PackageBox $packageBox)
    {
        if ($packageBox->file_path && Storage::disk('public')->exists($packageBox->file_path)) {
            Storage::disk('public')->delete($packageBox->file_path);
        }

        $packageBox->delete();

        return back()->with('success', 'Koli görseli silindi.');
    }
}