<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Label;
use App\Models\Barcode;
use App\Models\PackageBox;
use App\Models\Cap;
use App\Models\TdsDocument;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;


class ProductTreeController extends Controller
{
    // Ürün ağacı ana sayfa
    public function index()
    {
        $products = Product::all();
        return view('urun_agaci.index', compact('products'));
    }

    // Ürün detaylarını accordion yapıda göstermek
    public function show($id)
    {
        $product = Product::with([
            'labels','barcodes','packageBoxes','caps','relations','formulas','orders'
        ])->findOrFail($id);

        return view('urun_agaci.show', compact('product'));
    }

    // Yeni alt modül ekleme (örnek: etiket)
    public function storeLabel(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        $label = new Label($request->only(['name','type','description']));
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('labels','public');
            $label->file_path = $path;
        }
        $product->labels()->save($label);

        return redirect()->route('urunAgaci.show', $productId)->with('success','Etiket eklendi.');
    }

    // Barkod ekleme
    public function storeBarcode(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $barcode = new Barcode($request->only(['code','description']));
        $product->barcodes()->save($barcode);

        return redirect()->route('urunAgaci.show', $productId)->with('success','Barkod eklendi.');
    }

    // Koli ekleme
    public function storeBox(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $box = new PackageBox($request->only(['name','description']));
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('boxes','public');
            $box->file_path = $path;
        }
        $product->packageBoxes()->save($box);

        return redirect()->route('urunAgaci.show', $productId)->with('success','Koli eklendi.');
    }

    // Kapak ekleme
    public function storeCap(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $cap = new Cap($request->only(['name','description']));
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('caps','public');
            $cap->file_path = $path;
        }
        $product->caps()->save($cap);

        return redirect()->route('urunAgaci.show', $productId)->with('success','Kapak eklendi.');
    }

        public function exportSicilCard($id)
        {
            $product = Product::with([
                'barcodes',
                'labels',
                'caps',
                'bottles',
                'tdsDocuments'
            ])->findOrFail($id);

            $templateProcessor = new TemplateProcessor(
                storage_path('app/templates/sicil_karti.docx')
            );

            // Ürün bilgileri
            $templateProcessor->setValue('urunadi', $product->name ?? '—');
            $templateProcessor->setValue('urunkodu', $product->code ?? '—');
            $templateProcessor->setValue('aciklama', $product->description ?? '—');

            // Barkod (isim + açıklama)
            if($product->barcodes->isNotEmpty()){
                $barcode = $product->barcodes->first();
                $templateProcessor->setValue('barkod_adi', $barcode->name ?? '—');
                $templateProcessor->setValue('barkod_aciklama', $barcode->description ?? '—');
            } else {
                $templateProcessor->setValue('barkod_adi', '—');
                $templateProcessor->setValue('barkod_aciklama', '—');
            }

            // Etiket
            if($product->labels->isNotEmpty()){
                $label = $product->labels->first();
                $templateProcessor->setValue('etiket_adi', $label->name ?? '—');
                $templateProcessor->setValue('etiket_aciklama', $label->description ?? '—');

                if(!empty($label->file_path) && file_exists(storage_path('app/public/'.$label->file_path))){
                    $templateProcessor->setImageValue('etiket_img', [
                        'path' => storage_path('app/public/'.$label->file_path),
                        'width' => 150,
                        'height' => 150,
                        'ratio' => true
                    ]);
                } else {
                    $templateProcessor->setValue('etiket_img', '—');
                }
            } else {
                $templateProcessor->setValue('etiket_adi', '—');
                $templateProcessor->setValue('etiket_aciklama', '—');
                $templateProcessor->setValue('etiket_img', '—');
            }

            // Kapak
            if($product->caps->isNotEmpty()){
                $cap = $product->caps->first();
                $templateProcessor->setValue('kapak_adi', $cap->name ?? '—');
                $templateProcessor->setValue('kapak_aciklama', $cap->description ?? '—');

                if(!empty($cap->file_path) && file_exists(storage_path('app/public/'.$cap->file_path))){
                    $templateProcessor->setImageValue('kapak_img', [
                        'path' => storage_path('app/public/'.$cap->file_path),
                        'width' => 150,
                        'height' => 150,
                        'ratio' => true
                    ]);
                } else {
                    $templateProcessor->setValue('kapak_img', '—');
                }
            } else {
                $templateProcessor->setValue('kapak_adi', '—');
                $templateProcessor->setValue('kapak_aciklama', '—');
                $templateProcessor->setValue('kapak_img', '—');
            }

            // Şişe
            if($product->bottles->isNotEmpty()){
                $bottle = $product->bottles->first();
                $templateProcessor->setValue('sise_adi', $bottle->name ?? '—');
                $templateProcessor->setValue('sise_aciklama', $bottle->description ?? '—');

                if(!empty($bottle->file_path) && file_exists(storage_path('app/public/'.$bottle->file_path))){
                    $templateProcessor->setImageValue('sise_img', [
                        'path' => storage_path('app/public/'.$bottle->file_path),
                        'width' => 150,
                        'height' => 150,
                        'ratio' => true
                    ]);
                } else {
                    $templateProcessor->setValue('sise_img', '—');
                }
            } else {
                $templateProcessor->setValue('sise_adi', '—');
                $templateProcessor->setValue('sise_aciklama', '—');
                $templateProcessor->setValue('sise_img', '—');
            }

            // TDS Dokümanları
            if($product->tdsDocuments->isNotEmpty()){
                $tds = $product->tdsDocuments->first();
                $templateProcessor->setValue('tds_onaylar', $tds->approvals ?? '—');

                // Özellikler
                $featuresText = '';
                foreach($tds->features ?? [] as $f){
                    $title = $f['title'] ?? '';
                    $value = $f['value'] ?? '';
                    $desc  = $f['description'] ?? '';
                    $featuresText .= "• {$title}: {$value} ({$desc})\n";
                }
                $templateProcessor->setValue('tds_ozellikler', $featuresText ?: '—');

                // Parametreler (cloneRow)
                $params = $tds->parameters ?? [];
                if(count($params) > 0){
                    $templateProcessor->cloneRow('parametre', count($params));
                    foreach($params as $index => $p){
                        $i = $index+1;
                        $templateProcessor->setValue("parametre#{$i}", $p['parameter'] ?? '—');
                        $templateProcessor->setValue("birim#{$i}", $p['unit'] ?? '—');
                        $templateProcessor->setValue("metot#{$i}", $p['method'] ?? '—');
                        $templateProcessor->setValue("deger#{$i}", $p['value'] ?? '—');
                        $templateProcessor->setValue("aciklama#{$i}", $p['description'] ?? '—');
                    }
                } else {
                    $templateProcessor->setValue('tds_parametreler', '—');
                }
            } else {
                $templateProcessor->setValue('tds_onaylar', '—');
                $templateProcessor->setValue('tds_ozellikler', '—');
                $templateProcessor->setValue('tds_parametreler', '—');
            }

            $fileName = 'Sicil_Karti_'.$product->name.'.docx';
            $tempFile = tempnam(sys_get_temp_dir(), 'sicil');
            $templateProcessor->saveAs($tempFile);

            return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
        }




}