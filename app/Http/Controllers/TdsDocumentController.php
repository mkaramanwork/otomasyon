<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use App\Models\TdsDocument;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Models\LabParameter;
use Illuminate\Http\Request;
use App\Models\TdsPrompt;

class TdsDocumentController extends Controller
{
    public function index()
    {
        $documents = TdsDocument::with(['product','productGroup'])
            ->orderBy('created_at','desc')
            ->get();

        return view('tds.index', compact('documents'));
    }

    public function create()
    {
        return view('tds.create', [
            'products' => Product::orderBy('name')->get(),
            'groups'   => ProductGroup::orderBy('name')->get(),
            'params'   => LabParameter::orderBy('parameter')->get(),
        ]);
    }

public function store(Request $request)
{
    $data = $request->validate([
        'product_id'        => 'required',
        'product_group'     => 'required|string',   // ✅ artık string alan
        'product_code'      => 'nullable|string',
        'product_name'      => 'nullable|string',
        'description'       => 'nullable|string',
        'approvals'         => 'nullable|string',   // ✅ ekledik
        'features'          => 'array',
        'parameters'        => 'array',
    ]);

    // ✅ Parametreleri ID + değer + LabParameter bilgileriyle kaydet
    $selectedParams = [];
    if ($request->param_select) {
        foreach ($request->param_select as $id => $on) {
            $param = LabParameter::find($id);
            if ($param) {
                $selectedParams[] = [
                    'id'        => $id,
                    'parameter' => $param->parameter,
                    'unit'      => $param->unit,
                    'method_tr' => $param->method_tr,
                    'value'     => $request->param_value[$id] ?? null,
                ];
            }
        }
    }
    $data['parameters'] = $selectedParams;

    TdsDocument::create($data);

    return redirect()->route('tds.index')->with('success','TDS başarıyla oluşturuldu.');
}
    public function edit($id)
    {
        return view('tds.edit', [
            'doc'     => TdsDocument::findOrFail($id),
            'products'=> Product::orderBy('name')->get(),
            'groups'  => ProductGroup::orderBy('name')->get(),
            'params'  => LabParameter::orderBy('parameter')->get(),
        ]);
    }

public function update(Request $request, $id)
{
    $doc = TdsDocument::findOrFail($id);

    $data = $request->validate([
        'product_id'        => 'required',
        'product_group'     => 'required|string',   // ✅ string alan
        'product_code'      => 'nullable|string',
        'product_name'      => 'nullable|string',
        'description'       => 'nullable|string',
        'approvals'         => 'nullable|string',   // ✅ ekledik
        'features'          => 'array',
        'parameters'        => 'array',
    ]);

    // ✅ Parametreleri güncelle
    $selectedParams = [];
    if ($request->param_select) {
        foreach ($request->param_select as $id => $on) {
            $param = LabParameter::find($id);
            if ($param) {
                $selectedParams[] = [
                    'id'        => $id,
                    'parameter' => $param->parameter,
                    'unit'      => $param->unit,
                    'method_tr' => $param->method_tr,
                    'value'     => $request->param_value[$id] ?? null,
                ];
            }
        }
    }
    $data['parameters'] = $selectedParams;

    $doc->update($data);

    return redirect()->route('tds.index')->with('success','TDS başarıyla güncellendi.');
}

    public function destroy($id)
    {
        TdsDocument::findOrFail($id)->delete();
        return back()->with('success','TDS silindi.');
    }

    public function show($id)
    {
        $doc = TdsDocument::with('product')->findOrFail($id);
        return view('tds.show', compact('doc'));
    }

  public function exportWord($id)
{
    $doc = TdsDocument::findOrFail($id);

    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(storage_path('app/templates/tds.docx'));

    // Ürün bilgileri
    $templateProcessor->setValue('urunadi', $doc->product_name ?? '');
    $templateProcessor->setValue('urunkodu', $doc->product_code ?? '');
    $templateProcessor->setValue('urunaciklamasi', $doc->description ?? '');
    $templateProcessor->setValue('urungroup', $doc->product_group ?? '');

    // Özellikler
    $featuresText = '';
    foreach($doc->features ?? [] as $f){
        $featuresText .= "• ".($f['title'] ?? '').": ".($f['value'] ?? '')."\n";
    }
    $templateProcessor->setValue('urunozellikleri', $featuresText);

    // Onaylar
    $templateProcessor->setValue('urunonaylari', $doc->approvals ?? '—');

    // Parametreler
    $params = $doc->parameters ?? [];
    if(!empty($params)){
        $templateProcessor->cloneRow('parametre', count($params));
        foreach($params as $index => $p){
            $i = $index+1;
            $templateProcessor->setValue("parametre#{$i}", $p['parameter'] ?? '');
            $templateProcessor->setValue("birim#{$i}", $p['unit'] ?? '');
            $templateProcessor->setValue("metot#{$i}", $p['method_tr'] ?? '');
            $templateProcessor->setValue("deger#{$i}", $p['value'] ?? '');
        }
    }

    $fileName = 'TDS_'.$doc->product_name.'.docx';
    $tempFile = tempnam(sys_get_temp_dir(), 'word');
    $templateProcessor->saveAs($tempFile);

    return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
}
//2. taslaga  kayıt 

public function exportWordAlt($id)
{
    $doc = TdsDocument::findOrFail($id);

    // İkinci template dosyası 
    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(
        storage_path('app/templates/tds_alt.docx')
    );

    // Ürün bilgileri (aynı veriler)
    $templateProcessor->setValue('urunadi', $doc->product_name ?? '');
    $templateProcessor->setValue('urunkodu', $doc->product_code ?? '');
    $templateProcessor->setValue('urunaciklamasi', $doc->description ?? '');
    $templateProcessor->setValue('urungroup', $doc->product_group ?? '');

    // Özellikler
    $featuresText = '';
    foreach($doc->features ?? [] as $f){
        $featuresText .= "• ".($f['title'] ?? '').": ".($f['value'] ?? '')."\n";
    }
    $templateProcessor->setValue('urunozellikleri', $featuresText);

    // Onaylar
    $templateProcessor->setValue('urunonaylari', $doc->approvals ?? '—');

    // Parametreler
    $params = $doc->parameters ?? [];
    if(!empty($params)){
        $templateProcessor->cloneRow('parametre', count($params));
        foreach($params as $index => $p){
            $i = $index+1;
            $templateProcessor->setValue("parametre#{$i}", $p['parameter'] ?? '');
            $templateProcessor->setValue("birim#{$i}", $p['unit'] ?? '');
            $templateProcessor->setValue("metot#{$i}", $p['method_tr'] ?? '');
            $templateProcessor->setValue("deger#{$i}", $p['value'] ?? '');
        }
    }

    $fileName = 'TDS_ALT_'.$doc->product_name.'.docx';
    $tempFile = tempnam(sys_get_temp_dir(), 'word');
    $templateProcessor->saveAs($tempFile);

    return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
}


public function printAll()
{
    $docs = TdsDocument::all();
    $files = [];

    foreach($docs as $doc){
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(storage_path('app/templates/tds.docx'));

        // Ürün bilgileri
        $templateProcessor->setValue('urunadi', $doc->product_name ?? '');
        $templateProcessor->setValue('urunkodu', $doc->product_code ?? '');
        $templateProcessor->setValue('urunaciklamasi', $doc->description ?? '');
        $templateProcessor->setValue('urungroup', $doc->product_group ?? '');

        // Özellikler
        $featuresText = '';
        foreach($doc->features ?? [] as $f){
            $featuresText .= "• ".($f['title'] ?? '').": ".($f['value'] ?? '')."\n";
        }
        $templateProcessor->setValue('urunozellikleri', $featuresText);

        // Onaylar
        $templateProcessor->setValue('urunonaylari', $doc->approvals ?? '—');

        // Parametreler
        $params = $doc->parameters ?? [];
        if(!empty($params)){
            $templateProcessor->cloneRow('parametre', count($params));
            foreach($params as $index => $p){
                $i = $index+1;
                $templateProcessor->setValue("parametre#{$i}", $p['parameter'] ?? '');
                $templateProcessor->setValue("birim#{$i}", $p['unit'] ?? '');
                $templateProcessor->setValue("metot#{$i}", $p['method_tr'] ?? '');
                $templateProcessor->setValue("deger#{$i}", $p['value'] ?? '');
            }
        }

        $fileName = 'TDS_'.$doc->product_name.'.docx';
        $tempFile = tempnam(sys_get_temp_dir(), 'word');
        $templateProcessor->saveAs($tempFile);

        $files[] = [
            'path' => $tempFile,
            'name' => $fileName
        ];
    }

    // ZIP oluştur
    $zipFile = tempnam(sys_get_temp_dir(), 'tds_zip');
    $zip = new \ZipArchive();
    $zip->open($zipFile, \ZipArchive::CREATE);

    foreach($files as $f){
        $zip->addFile($f['path'], $f['name']);
    }

    $zip->close();

    return response()->download($zipFile, 'TDS_Dokumanlari.zip')->deleteFileAfterSend(true);
}

        // ✅ Karaman_Al Asistanı Arayüzü
        public function karamanAl()
        {
            // Geçmiş konuşmaları çekiyoruz
            $prompts = TdsPrompt::orderBy('created_at','desc')->get();

            return view('tds.karaman_al', compact('prompts'));
        }


    // ✅ Karaman_Al Soru-Cevap
public function ask(Request $request)
{
    $question = $request->input('question');

    // 1. Ürün adını ayrıştır (örnek: ilk kelimeyi al)
    preg_match('/\b(5W30|10W40|CI-4|SN)\b/i', $question, $matches);
    $productName = $matches[0] ?? $question; // eşleşme yoksa tüm soruyu kullan

    // 2. DB’de ürün araması
    $doc = TdsDocument::where('product_name', 'like', "%{$productName}%")->first();

    $rawAnswer = "Cevap alınamadı.";
    $field = "all";

    if ($doc) {
        // 3. Intent detection (HuggingFace + fallback)
        try {
            $fieldResponse = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . env('HUGGINGFACE_API_KEY'),
            ])->post('https://router.huggingface.co/models/google/flan-t5-base', [
                'inputs' => "Soru: {$question}\nAlan seçenekleri: approvals, features, parameters, description.\nBu soruda kullanıcı hangi alanı istiyor? Tek kelimeyle cevap ver.",
                'parameters' => [
                    'max_length' => 10,
                    'temperature' => 0.0
                ]
            ]);

            if ($fieldResponse->successful()) {
                $fieldJson = $fieldResponse->json();
                $field = strtolower(trim($fieldJson[0]['generated_text'] ?? 'all'));
            }
        } catch (\Exception $e) {
            if (stripos($question, 'onay') !== false || stripos($question, 'API') !== false) {
                $field = "approvals";
            } elseif (stripos($question, 'özellik') !== false || stripos($question, 'görünüm') !== false) {
                $field = "features";
            } elseif (stripos($question, 'parametre') !== false || stripos($question, 'viskozite') !== false) {
                $field = "parameters";
            } elseif (stripos($question, 'açıklama') !== false) {
                $field = "description";
            } else {
                $field = "all";
            }
        }

        // 4. DB’den ilgili alanı çek
        switch ($field) {
            case 'approvals':
                $rawAnswer = "Ürün: {$doc->product_name}\nOnaylar: {$doc->approvals}";
                break;
            case 'features':
                $rawAnswer = "Ürün: {$doc->product_name}\nÖzellikler:\n";
                foreach($doc->features ?? [] as $f){
                    $rawAnswer .= "- {$f['value']}\n";
                }
                break;
            case 'parameters':
                $rawAnswer = "Ürün: {$doc->product_name}\nParametreler:\n";
                foreach($doc->parameters ?? [] as $p){
                    $rawAnswer .= "{$p['parameter']} ({$p['unit']}): {$p['value']}\n";
                }
                break;
            case 'description':
                $rawAnswer = "Ürün: {$doc->product_name}\nAçıklama: {$doc->description}";
                break;
            default:
                $rawAnswer = "Ürün: {$doc->product_name}\n";
                $rawAnswer .= "Açıklama: {$doc->description}\n";
                $rawAnswer .= "Onaylar: {$doc->approvals}\n";
                $rawAnswer .= "Özellikler:\n";
                foreach($doc->features ?? [] as $f){
                    $rawAnswer .= "- {$f['value']}\n";
                }
                $rawAnswer .= "Parametreler:\n";
                foreach($doc->parameters ?? [] as $p){
                    $rawAnswer .= "{$p['parameter']} ({$p['unit']}): {$p['value']}\n";
                }
        }
    }

    // 5. HuggingFace ile cevabı özetle (fallback ile)
    try {
        $response = Http::timeout(60)->withHeaders([
            'Authorization' => 'Bearer ' . env('HUGGINGFACE_API_KEY'),
        ])->post('https://router.huggingface.co/models/google/flan-t5-base', [
            'inputs' => "Soru: {$question}\nVeritabanı cevabı: {$rawAnswer}\nBu cevabı kullanıcıya doğal dilde açıkla.",
            'parameters' => [
                'max_length' => 200,
                'temperature' => 0.7
            ]
        ]);

        if ($response->successful()) {
            $json = $response->json();
            $answer = $json[0]['generated_text'] ?? ($json['generated_text'] ?? $rawAnswer);
        } else {
            $answer = $rawAnswer;
        }
    } catch (\Exception $e) {
        $answer = $rawAnswer;
    }

    // 6. Hafızaya kaydet
    TdsPrompt::create([
        'question' => $question,
        'answer'   => $answer,
        'user_id'  => auth()->id() ?? null,
    ]);

    $prompts = TdsPrompt::orderBy('created_at','desc')->get();

    return view('tds.karaman_al', compact('prompts','answer'));
} 
}