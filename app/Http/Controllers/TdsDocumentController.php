<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\TdsDocument;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Models\LabParameter;
use Illuminate\Http\Request;
use App\Models\TdsPrompt;
use App\Models\Brand;

class TdsDocumentController extends Controller
{
    public function index()
    {
        $documents = TdsDocument::with([
            'product',
            'productGroup',
            'brand'
        ])
            ->leftJoin('brands', 'tds_documents.brand_id', '=', 'brands.id')
            ->select('tds_documents.*')
            ->orderBy('brands.name', 'asc')
            ->orderBy('tds_documents.product_name', 'asc')
            ->get();

        $brands = Brand::orderBy('name')->get();

        return view('tds.index', compact('documents', 'brands'));
    }
    public function create()
    {
        return view('tds.create', [
            'products' => Product::orderBy('name')->get(),
            'groups' => ProductGroup::orderBy('name')->get(),
            'params' => LabParameter::orderBy('parameter')->get(),
            'brands' => Brand::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required',
            'product_group' => 'required|string',
            'product_code' => 'nullable|string',
            'product_name' => 'nullable|string',
            'description' => 'nullable|string',
            'approvals' => 'nullable|string',
            'features' => 'array',
            'parameters' => 'array',
            'brand_id' => 'required|exists:brands,id',
        ]);

        $selectedParams = [];

        if ($request->param_select) {
            foreach ($request->param_select as $id => $on) {
                $param = LabParameter::find($id);

                if ($param) {
                    $selectedParams[] = [
                        'id' => $id,
                        'parameter' => $param->parameter,
                        'unit' => $param->unit,
                        'method_tr' => $param->method_tr,
                        'value' => $request->param_value[$id] ?? null,
                    ];
                }
            }
        }

        $data['parameters'] = $selectedParams;

        TdsDocument::create($data);

        return redirect()
            ->route('tds.index')
            ->with('success', 'TDS başarıyla oluşturuldu.');
    }

    public function cloneForm()
    {
        $documents = TdsDocument::with([
            'brand',
            'product'
        ])
            ->orderBy('product_name')
            ->get();

        $brands = Brand::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $params = LabParameter::orderBy('parameter')->get();

        return view('tds.clone', compact(
            'documents',
            'brands',
            'products',
            'params'
        ));
    }

    public function cloneData($id)
    {
        $doc = TdsDocument::with([
            'brand',
            'product'
        ])->findOrFail($id);

        return response()->json([
            'id' => $doc->id,
            'brand_id' => $doc->brand_id,
            'brand_name' => $doc->brand?->name,
            'product_id' => $doc->product_id,
            'product_master_name' => $doc->product?->name,
            'product_group' => $doc->product_group,
            'product_code' => $doc->product_code,
            'product_name' => $doc->product_name,
            'description' => $doc->description,
            'approvals' => $doc->approvals,
            'features' => $doc->features ?? [],
            'parameters' => $doc->parameters ?? [],
        ]);
    }

    public function cloneStore(Request $request)
    {
        $data = $request->validate([
            'source_tds_id' => 'required|exists:tds_documents,id',
            'product_id' => 'required|exists:products,id',
            'brand_id' => 'required|exists:brands,id',
            'product_group' => 'required|string',
            'product_code' => 'nullable|string',
            'product_name' => 'required|string',
            'description' => 'nullable|string',
            'approvals' => 'nullable|string',
            'features' => 'nullable|array',
        ]);

        $selectedParams = [];

        if ($request->param_select) {
            foreach ($request->param_select as $id => $on) {
                $param = LabParameter::find($id);

                if ($param) {
                    $selectedParams[] = [
                        'id' => $param->id,
                        'parameter' => $param->parameter,
                        'unit' => $param->unit,
                        'method_tr' => $param->method_tr,
                        'value' => $request->param_value[$id] ?? null,
                    ];
                }
            }
        }

        TdsDocument::create([
            'product_id' => $data['product_id'],
            'brand_id' => $data['brand_id'],
            'product_group' => $data['product_group'],
            'product_code' => $data['product_code'] ?? null,
            'product_name' => $data['product_name'],
            'description' => $data['description'] ?? null,
            'approvals' => $data['approvals'] ?? null,
            'features' => $data['features'] ?? [],
            'parameters' => $selectedParams,
        ]);

        return redirect()
            ->route('tds.index')
            ->with('success', 'TDS başarıyla klonlandı.');
    }

    public function edit($id)
    {
        return view('tds.edit', [
            'doc' => TdsDocument::findOrFail($id),
            'products' => Product::orderBy('name')->get(),
            'groups' => ProductGroup::orderBy('name')->get(),
            'params' => LabParameter::orderBy('parameter')->get(),
            'brands' => Brand::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $doc = TdsDocument::findOrFail($id);

        $data = $request->validate([
            'product_id' => 'required',
            'product_group' => 'required|string',
            'product_code' => 'nullable|string',
            'product_name' => 'nullable|string',
            'description' => 'nullable|string',
            'approvals' => 'nullable|string',
            'features' => 'array',
            'parameters' => 'array',
            'brand_id' => 'required|exists:brands,id',
        ]);

        $selectedParams = [];

        if ($request->param_select) {
            foreach ($request->param_select as $id => $on) {
                $param = LabParameter::find($id);

                if ($param) {
                    $selectedParams[] = [
                        'id' => $id,
                        'parameter' => $param->parameter,
                        'unit' => $param->unit,
                        'method_tr' => $param->method_tr,
                        'value' => $request->param_value[$id] ?? null,
                    ];
                }
            }
        }

        $data['parameters'] = $selectedParams;

        $doc->update($data);

        return redirect()
            ->route('tds.index')
            ->with('success', 'TDS başarıyla güncellendi.');
    }

    public function destroy($id)
    {
        TdsDocument::findOrFail($id)->delete();

        return back()->with('success', 'TDS silindi.');
    }

    public function show($id)
    {
        $doc = TdsDocument::with([
            'product',
            'brand'
        ])->findOrFail($id);

        return view('tds.show', compact('doc'));
    }

    public function exportWord($id)
    {
        $doc = TdsDocument::with('brand')->findOrFail($id);

        if (!$doc->brand) {
            return back()->with(
                'error',
                'Bu TDS kaydına marka tanımlanmamış. Sistem yöneticisi ile iletişime geçiniz.'
            );
        }

        if (empty($doc->brand->template_file)) {
            return back()->with(
                'error',
                'Bu marka için TDS taslağı tanımlanmamış. Sistem yöneticisi ile iletişime geçiniz.'
            );
        }

        $templateFile = basename($doc->brand->template_file);

        $templatePath = storage_path(
            'app/templates/' . $templateFile
        );

        if (!file_exists($templatePath)) {
            return back()->with(
                'error',
                'Tanımlanan TDS taslağı sistemde bulunamadı. Sistem yöneticisi ile iletişime geçiniz.'
            );
        }

        $templateProcessor =
            new \PhpOffice\PhpWord\TemplateProcessor(
                $templatePath
            );

        $templateProcessor->setValue(
            'urunadi',
            $doc->product_name ?? ''
        );

        $templateProcessor->setValue(
            'urunkodu',
            $doc->product_code ?? ''
        );

        $templateProcessor->setValue(
            'urunaciklamasi',
            $doc->description ?? ''
        );

        $templateProcessor->setValue(
            'urungroup',
            $doc->product_group ?? ''
        );

        $featuresText = '';

        foreach ($doc->features ?? [] as $f) {

            $title = $f['title'] ?? '';
            $value = $f['value'] ?? '';

            if ($title !== '' && $value !== '') {

                $featuresText .=
                    "• {$title}: {$value}\n";

            } elseif ($value !== '') {

                $featuresText .=
                    "• {$value}\n";

            } elseif ($title !== '') {

                $featuresText .=
                    "• {$title}\n";
            }
        }

        $templateProcessor->setValue(
            'urunozellikleri',
            $featuresText
        );

        $templateProcessor->setValue(
            'urunonaylari',
            $doc->approvals ?? ''
        );

        $params = $doc->parameters ?? [];

        if (!empty($params)) {

            $templateProcessor->cloneRow(
                'parametre',
                count($params)
            );

            foreach ($params as $index => $p) {

                $i = $index + 1;

                $templateProcessor->setValue(
                    "parametre#{$i}",
                    $p['parameter'] ?? ''
                );

                $templateProcessor->setValue(
                    "birim#{$i}",
                    $p['unit'] ?? ''
                );

                $templateProcessor->setValue(
                    "metot#{$i}",
                    $p['method_tr'] ?? ''
                );

                $templateProcessor->setValue(
                    "deger#{$i}",
                    $p['value'] ?? ''
                );
            }
        }

        $safeProductName = preg_replace(
            '/[\\\\\/:*?"<>|]+/',
            '-',
            $doc->product_name ?? 'TDS'
        );

        $fileName =
            'TDS_' .
            $safeProductName .
            '.docx';

        $tempFile =
            tempnam(
                sys_get_temp_dir(),
                'tds_word_'
            );

        $templateProcessor->saveAs(
            $tempFile
        );

        return response()
            ->download(
                $tempFile,
                $fileName
            )
            ->deleteFileAfterSend(true);
    }
public function printAll()
{
    $docs = TdsDocument::with('brand')
        ->orderBy('brand_id')
        ->orderBy('product_name')
        ->get();

    if ($docs->isEmpty()) {
        return back()->with(
            'error',
            'Yazdırılacak TDS kaydı bulunamadı.'
        );
    }

    $files = [];

    foreach ($docs as $doc) {

        if (!$doc->brand) {

            foreach ($files as $file) {
                if (file_exists($file['path'])) {
                    unlink($file['path']);
                }
            }

            return back()->with(
                'error',
                ($doc->product_name ?? 'TDS') .
                ' ürünü için marka tanımlanmamış. Sistem yöneticisi ile iletişime geçiniz.'
            );
        }

        if (empty($doc->brand->template_file)) {

            foreach ($files as $file) {
                if (file_exists($file['path'])) {
                    unlink($file['path']);
                }
            }

            return back()->with(
                'error',
                $doc->brand->name .
                ' markası için TDS taslağı tanımlanmamış. Sistem yöneticisi ile iletişime geçiniz.'
            );
        }

        $templateFile = basename(
            $doc->brand->template_file
        );

        $templatePath = storage_path(
            'app/templates/' . $templateFile
        );

        if (!file_exists($templatePath)) {

            foreach ($files as $file) {
                if (file_exists($file['path'])) {
                    unlink($file['path']);
                }
            }

            return back()->with(
                'error',
                $doc->brand->name .
                ' markasına ait "' .
                $templateFile .
                '" TDS taslağı sistemde bulunamadı. Sistem yöneticisi ile iletişime geçiniz.'
            );
        }

        $templateProcessor =
            new \PhpOffice\PhpWord\TemplateProcessor(
                $templatePath
            );

        $templateProcessor->setValue(
            'urunadi',
            $doc->product_name ?? ''
        );

        $templateProcessor->setValue(
            'urunkodu',
            $doc->product_code ?? ''
        );

        $templateProcessor->setValue(
            'urunaciklamasi',
            $doc->description ?? ''
        );

        $templateProcessor->setValue(
            'urungroup',
            $doc->product_group ?? ''
        );

        $featuresText = '';

        foreach ($doc->features ?? [] as $f) {

            $title = $f['title'] ?? '';
            $value = $f['value'] ?? '';

            if ($title !== '' && $value !== '') {

                $featuresText .=
                    "• {$title}: {$value}\n";

            } elseif ($value !== '') {

                $featuresText .=
                    "• {$value}\n";

            } elseif ($title !== '') {

                $featuresText .=
                    "• {$title}\n";
            }
        }

        $templateProcessor->setValue(
            'urunozellikleri',
            $featuresText
        );

        $templateProcessor->setValue(
            'urunonaylari',
            $doc->approvals ?? ''
        );

        $params = $doc->parameters ?? [];

        if (!empty($params)) {

            $templateProcessor->cloneRow(
                'parametre',
                count($params)
            );

            foreach ($params as $index => $p) {

                $i = $index + 1;

                $templateProcessor->setValue(
                    "parametre#{$i}",
                    $p['parameter'] ?? ''
                );

                $templateProcessor->setValue(
                    "birim#{$i}",
                    $p['unit'] ?? ''
                );

                $templateProcessor->setValue(
                    "metot#{$i}",
                    $p['method_tr'] ?? ''
                );

                $templateProcessor->setValue(
                    "deger#{$i}",
                    $p['value'] ?? ''
                );
            }
        }

        $safeBrandName = preg_replace(
            '/[\\\\\/:*?"<>|]+/',
            '-',
            $doc->brand->name
        );

        $safeProductName = preg_replace(
            '/[\\\\\/:*?"<>|]+/',
            '-',
            $doc->product_name ?? 'TDS'
        );

        $fileName =
            $safeBrandName .
            '_TDS_' .
            $safeProductName .
            '.docx';

        $tempFile =
            tempnam(
                sys_get_temp_dir(),
                'tds_word_'
            );

        $templateProcessor->saveAs(
            $tempFile
        );

        $files[] = [
            'path' => $tempFile,
            'name' => $fileName
        ];
    }

    $zipFile =
        tempnam(
            sys_get_temp_dir(),
            'tds_zip_'
        );

    $zip = new \ZipArchive();

    $result = $zip->open(
        $zipFile,
        \ZipArchive::CREATE |
        \ZipArchive::OVERWRITE
    );

    if ($result !== true) {

        foreach ($files as $file) {
            if (file_exists($file['path'])) {
                unlink($file['path']);
            }
        }

        return back()->with(
            'error',
            'TDS ZIP dosyası oluşturulamadı. Sistem yöneticisi ile iletişime geçiniz.'
        );
    }

    foreach ($files as $file) {

        $zip->addFile(
            $file['path'],
            $file['name']
        );
    }

    $zip->close();

    $response = response()
        ->download(
            $zipFile,
            'TDS_Dokumanlari.zip'
        )
        ->deleteFileAfterSend(true);

    register_shutdown_function(
        function () use ($files) {

            foreach ($files as $file) {

                if (file_exists($file['path'])) {
                    @unlink($file['path']);
                }
            }
        }
    );

    return $response;
}
    public function karamanAl()
    {
        $prompts = TdsPrompt::orderBy('created_at', 'desc')->get();

        return view('tds.karaman_al', compact('prompts'));
    }

    public function ask(Request $request)
    {
        $question = $request->input('question');

        preg_match('/\b(5W30|10W40|CI-4|SN)\b/i', $question, $matches);

        $productName = $matches[0] ?? $question;

        $doc = TdsDocument::where(
            'product_name',
            'like',
            "%{$productName}%"
        )->first();

        $rawAnswer = 'Cevap alınamadı.';
        $field = 'all';

        if ($doc) {
            try {
                $fieldResponse = Http::timeout(30)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . env('HUGGINGFACE_API_KEY'),
                    ])
                    ->post(
                        'https://router.huggingface.co/models/google/flan-t5-base',
                        [
                            'inputs' =>
                                "Soru: {$question}\n" .
                                "Alan seçenekleri: approvals, features, parameters, description.\n" .
                                "Bu soruda kullanıcı hangi alanı istiyor? Tek kelimeyle cevap ver.",

                            'parameters' => [
                                'max_length' => 10,
                                'temperature' => 0.0
                            ]
                        ]
                    );

                if ($fieldResponse->successful()) {
                    $fieldJson = $fieldResponse->json();

                    $field = strtolower(
                        trim(
                            $fieldJson[0]['generated_text']
                            ?? 'all'
                        )
                    );
                }
            } catch (\Exception $e) {
                if (
                    stripos($question, 'onay') !== false
                    || stripos($question, 'API') !== false
                ) {
                    $field = 'approvals';
                } elseif (
                    stripos($question, 'özellik') !== false
                    || stripos($question, 'görünüm') !== false
                ) {
                    $field = 'features';
                } elseif (
                    stripos($question, 'parametre') !== false
                    || stripos($question, 'viskozite') !== false
                ) {
                    $field = 'parameters';
                } elseif (
                    stripos($question, 'açıklama') !== false
                ) {
                    $field = 'description';
                } else {
                    $field = 'all';
                }
            }

            switch ($field) {
                case 'approvals':
                    $rawAnswer =
                        "Ürün: {$doc->product_name}\n" .
                        "Onaylar: {$doc->approvals}";
                    break;

                case 'features':
                    $rawAnswer =
                        "Ürün: {$doc->product_name}\n" .
                        "Özellikler:\n";

                    foreach ($doc->features ?? [] as $f) {
                        $rawAnswer .= "- {$f['value']}\n";
                    }

                    break;

                case 'parameters':
                    $rawAnswer =
                        "Ürün: {$doc->product_name}\n" .
                        "Parametreler:\n";

                    foreach ($doc->parameters ?? [] as $p) {
                        $rawAnswer .=
                            "{$p['parameter']} ({$p['unit']}): {$p['value']}\n";
                    }

                    break;

                case 'description':
                    $rawAnswer =
                        "Ürün: {$doc->product_name}\n" .
                        "Açıklama: {$doc->description}";
                    break;

                default:
                    $rawAnswer =
                        "Ürün: {$doc->product_name}\n";

                    $rawAnswer .=
                        "Açıklama: {$doc->description}\n";

                    $rawAnswer .=
                        "Onaylar: {$doc->approvals}\n";

                    $rawAnswer .= "Özellikler:\n";

                    foreach ($doc->features ?? [] as $f) {
                        $rawAnswer .= "- {$f['value']}\n";
                    }

                    $rawAnswer .= "Parametreler:\n";

                    foreach ($doc->parameters ?? [] as $p) {
                        $rawAnswer .=
                            "{$p['parameter']} ({$p['unit']}): {$p['value']}\n";
                    }
            }
        }

        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . env('HUGGINGFACE_API_KEY'),
                ])
                ->post(
                    'https://router.huggingface.co/models/google/flan-t5-base',
                    [
                        'inputs' =>
                            "Soru: {$question}\n" .
                            "Veritabanı cevabı: {$rawAnswer}\n" .
                            "Bu cevabı kullanıcıya doğal dilde açıkla.",

                        'parameters' => [
                            'max_length' => 200,
                            'temperature' => 0.7
                        ]
                    ]
                );

            if ($response->successful()) {
                $json = $response->json();

                $answer =
                    $json[0]['generated_text']
                    ?? ($json['generated_text'] ?? $rawAnswer);
            } else {
                $answer = $rawAnswer;
            }
        } catch (\Exception $e) {
            $answer = $rawAnswer;
        }

        TdsPrompt::create([
            'question' => $question,
            'answer' => $answer,
            'user_id' => auth()->id() ?? null,
        ]);

        $prompts = TdsPrompt::orderBy('created_at', 'desc')->get();

        return view(
            'tds.karaman_al',
            compact('prompts', 'answer')
        );
    }
}