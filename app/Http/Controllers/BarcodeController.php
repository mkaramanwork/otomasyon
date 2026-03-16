<?php

namespace App\Http\Controllers;

use App\Models\Barcode;
use Illuminate\Http\Request;
use Milon\Barcode\DNS1D;

class BarcodeController extends Controller
{
    public function store(Request $request)
    {
        // Hiçbir validasyon yok, direkt kaydediyoruz
        Barcode::create([
            'product_id'  => $request->product_id,
            'code'        => (string) $request->code,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Barkod başarıyla eklendi.');
    }

    public function edit(Barcode $barcode)
    {
        return view('barcodes.edit', compact('barcode'));
    }

    public function update(Request $request, Barcode $barcode)
    {
        // Direkt güncelleme
        $barcode->update([
            'code'        => (string) $request->code,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Barkod güncellendi.');
    }

    public function destroy(Barcode $barcode)
    {
        $barcode->delete();
        return back()->with('success', 'Barkod silindi.');
    }

    public function download(Barcode $barcode)
    {
        if (!empty($barcode->code)) {
            $content = "Barkod: " . $barcode->code . "\nAçıklama: " . ($barcode->description ?? '-');

            return response($content)
                ->header('Content-Type', 'text/plain')
                ->header('Content-Disposition', 'attachment; filename=barcode_'.$barcode->id.'.txt');
        }

        return back()->with('error', 'Barkod kodu bulunamadı.');
    }

}