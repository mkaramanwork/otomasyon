<?php

namespace App\Http\Controllers;

use App\Models\SampleAcceptance;
use App\Models\LabParameter;
use App\Models\BusinessLog;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Models\Customer;




class SampleAcceptanceController extends Controller
{
    /**
     * Bekleyen numuneleri listele (status = pending)
     */
    public function index()
    {
        // Yeni gelen (pending) numuneler
        $pendingSamples = SampleAcceptance::where('status', 'pending')
                            ->with('product')
                            ->latest() // en yeni en üstte
                            ->get();

        // Kabul edilen numuneler
        $acceptedSamples = SampleAcceptance::where('status', 'accepted')
                            ->with('product')
                            ->latest()
                            ->get();

    return view('sample_acceptances.index', compact('pendingSamples','acceptedSamples'));
}

    /**
     * Yeni numune kaydı oluştur
     */
    public function store(Request $request)
    {
        $lastRequestNo = SampleAcceptance::max('request_no') ?? 0;

        $sample = SampleAcceptance::create([
            'product_id'   => $request->product_id,
            'company_name' => $request->company_name,
            'batch_no'     => $request->batch_no,
            'country'      => $request->country,
            'uncertainty'  => $request->uncertainty ?? false,
            'report_request' => $request->report_request ?? false,
            'report_no'    => $request->report_no,
            'request_no'   => $lastRequestNo + 1,
            'sample_type'  => $request->sample_type,
            'description'  => $request->description,
            'taken_place'  => $request->taken_place,
            'taken_by'     => $request->taken_by,
            'taken_at'     => $request->taken_at,
            'amount'       => $request->amount ?? 300,
            'protection'   => $request->protection,
            'status'       => 'pending',
        ]);

        // ✅ Log
        BusinessLog::create([
            'action' => 'Numune Kaydı',
            'description' => "Batch No {$sample->batch_no} için yeni numune kaydı oluşturuldu.",
            'user_id' => auth()->id(),
            'sample_acceptance_id' => $sample->id,
        ]);

        return redirect()->route('samples.index')->with('success', 'Numune kaydı oluşturuldu.');
    }

    /**
     * Numune kabul et
     */
    public function accept(SampleAcceptance $sample)
    {
        $sample->update([
            'status' => 'accepted',
            'accepted_by' => auth()->id(),
        ]);

        BusinessLog::create([
            'action' => 'Numune Kabul',
            'description' => "Batch No {$sample->batch_no} numunesi kabul edildi.",
            'user_id' => auth()->id(),
            'sample_acceptance_id' => $sample->id,
        ]);

        return redirect()->route('samples.index')->with('success', 'Numune kabul edildi.');
    }

    /**
     * Numune reddet
     */
    public function reject(SampleAcceptance $sample)
    {
        $sample->update([
            'status' => 'rejected',
            'accepted_by' => auth()->id(),
        ]);

        BusinessLog::create([
            'action' => 'Numune Red',
            'description' => "Batch No {$sample->batch_no} numunesi reddedildi.",
            'user_id' => auth()->id(),
            'sample_acceptance_id' => $sample->id,
        ]);

        return redirect()->route('samples.index')->with('error', 'Numune reddedildi.');
    }

    /**
     * Numune kabul formu
     */
    public function acceptForm(SampleAcceptance $sample)
    {
        $parameters = LabParameter::where('sample_type', $sample->sample_type)->get();
        return view('sample_acceptances.accept_form', compact('sample','parameters'));
    }

    /**
     * Numune kabul kaydet
     */
    public function acceptStore(Request $request, SampleAcceptance $sample)
    {
        $validated = $request->validate([
            'country'        => 'required|string',
            'uncertainty'    => 'required|boolean',
            'report_request' => 'required|boolean',
            'report_no'      => 'nullable|string',
            'description'    => 'nullable|string',
            'amount'         => 'nullable|integer|min:1',
            'protection'     => 'required|string',
            'parameters'     => 'required|array',
        ]);

        $sample->update([
            'country'        => $validated['country'],
            'uncertainty'    => $validated['uncertainty'],
            'report_request' => $validated['report_request'],
            'report_no'      => $validated['report_no'],
            'description'    => $validated['description'],
            'amount'         => $validated['amount'] ?? 300,
            'protection'     => $validated['protection'],
            'status'         => 'accepted',
            'accepted_by'    => auth()->id(),
        ]);

        $sample->parameters()->sync($validated['parameters']);

        BusinessLog::create([
            'action' => 'Numune Kabul + Parametre Atama',
            'description' => "Batch No {$sample->batch_no} için parametreler atandı ve kabul edildi.",
            'user_id' => auth()->id(),
            'sample_acceptance_id' => $sample->id,
        ]);

        return redirect()->route('samples.analysis-pending')
                         ->with('success','Numune kabul edildi ve Analiz Bekleyenlere gönderildi.');

                         
    }

    /**
     * Analiz sonuçlarını toplu kaydet
     */
    public function storeResults(Request $request, SampleAcceptance $sample)
    {
        $validated = $request->validate([
            'results' => 'required|array',
            'results.*.value' => 'required|string',
            'results.*.date' => 'required|date',
            'results.*.description' => 'nullable|string',
        ]);

        foreach ($validated['results'] as $paramId => $data) {
            $sample->parameters()->updateExistingPivot($paramId, [
                'result' => $data['value'],
                'result_date' => $data['date'],
                'description' => $data['description'],
                'updated_at' => now(),
            ]);

            BusinessLog::create([
                'action' => 'Analiz Sonucu',
                'description' => "Batch No {$sample->batch_no} için {$sample->parameters()->find($paramId)->parameter} sonucu girildi.",
                'user_id' => auth()->id(),
                'sample_acceptance_id' => $sample->id,
            ]);
        }

        return redirect()->route('samples.analysisPending')
                         ->with('success','Sonuçlar başarıyla kaydedildi.');
    }

    /**
     * Analiz sonuçlarını düzenleme sayfası
     */
    public function editResults(SampleAcceptance $sample)
    {
        $sample->load('parameters');
        return view('sample_acceptances.results_edit', compact('sample'));
    }

    /**
     * Tek parametre sonucu kaydet
     */
    public function storeSingle(Request $request, SampleAcceptance $sample, $paramId)
    {
        $validated = $request->validate([
            'value' => 'required|string',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $sample->parameters()->updateExistingPivot($paramId, [
            'result' => $validated['value'],
            'result_date' => $validated['date'],
            'description' => $validated['description'],
            'updated_at' => now(),
        ]);

        BusinessLog::create([
            'action' => 'Analiz Sonucu',
            'description' => "Batch No {$sample->batch_no} için {$sample->parameters()->find($paramId)->parameter} sonucu girildi.",
            'user_id' => auth()->id(),
            'sample_acceptance_id' => $sample->id,
        ]);

        return back()->with('success','Parametre sonucu kaydedildi.');
    }
    

    /**
     * Analiz bekleyen numuneler
     */
        public function analysisPending(Request $request)
        {
            $query = SampleAcceptance::with(['parameters','product'])
                        ->where('status','accepted');

            // ✅ Filtreler
            if ($request->filled('batch_no')) {
                $query->where('batch_no','like','%'.$request->batch_no.'%');
            }
            if ($request->filled('company_name')) {
                $query->where('company_name','like','%'.$request->company_name.'%');
            }
            if ($request->filled('report_no')) {
                $query->where('report_no','like','%'.$request->report_no.'%');
            }

            $samples = $query->get();

            // ✅ Analiz tamamlanma yüzdesine göre sıralama
            $samples = $samples->sortBy(function($sample) {
                $total = $sample->parameters->count();
                $done  = $sample->parameters->whereNotNull('pivot.result')->count();
                $percent = $total > 0 ? round(($done / $total) * 100) : 0;
                return $percent; // küçükten büyüğe → tamamlanmayanlar önce
            });

            return view('sample_acceptances.analysis_pending', compact('samples'));
        }
        public function show(SampleAcceptance $sample)
            {
                return view('sample_acceptances.show', compact('sample'));
            }

public function edit(SampleAcceptance $sample)
{
    // Tüm parametreleri getiriyoruz
    $parameters = LabParameter::all();

    return view('sample_acceptances.edit', compact('sample','parameters'));
}



public function update(Request $request, SampleAcceptance $sample)
{
    $request->validate([
        'selected_parameters' => 'array'
    ]);

    // Sadece parametre ilişkisini güncelle
    $sample->parameters()->sync($request->selected_parameters ?? []);

    return redirect()->route('samples.index')
                     ->with('success','Parametreler başarıyla güncellendi.');
}

    // ✅ Rapor sayfası
    public function reportPage()
    {
        $samples = SampleAcceptance::with('parameters')
            ->where('status','accepted')
            ->orderBy('created_at', 'desc') // ✅ En yeni en üstte
            ->get();

        return view('sample_acceptances.report', compact('samples'));
    }

public function reportStore(Request $request)
{
    $validated = $request->validate([
        'sample_id'   => 'required|exists:sample_acceptances,id',
        'report_date' => 'nullable|date',
        'description' => 'nullable|string',
        'images.*'    => 'nullable|image|max:2048',
    ]);

    $sample = SampleAcceptance::findOrFail($validated['sample_id']);

    // Mevcut resimleri array olarak al
    $images = $sample->report_images ?? [];

    // Yeni resimleri ekle
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $img) {
            $path = $img->store('reports', 'public');
            $images[] = basename($path);
        }
    }

    // ✅ Array olarak kaydet
    $sample->update([
        'report_date'        => $validated['report_date'],
        'report_description' => $validated['description'],
        'report_images'      => array_values($images), // burada array garanti edilir
    ]);

    return redirect()->route('samples.show',$sample->id)
                     ->with('success','Rapor bilgileri kaydedildi. Artık rapor yazabilirsiniz.');
} 



public function generateReport(SampleAcceptance $sample, string $lang = 'TR')
{
    // ✅ Parametrelerin scope durumunu kontrol et
    $allOutOfScope = $sample->parameters->every(function($param) {
        return $param->scope === 'Kapsam Dışı';
    });

    // ✅ Template seçimi
    $templatePath = $allOutOfScope
        ? storage_path("app/templates/lab_report_out_of_scope.docx")
        : storage_path("app/templates/lab_report_standard.docx");

    $template = new TemplateProcessor($templatePath);

    // ✅ Rapor numarası veritabanından
    $reportNo = $sample->report_no ?? '-';

    // ✅ Rapor dönemi (YY-MM formatı)
    $reportPeriod = $sample->report_date
        ? Carbon::parse($sample->report_date)->format('y-m')
        : Carbon::now()->format('y-m');

    // ✅ Genel alanlar
    $template->setValue('company_name', $sample->company_name ?? '-');
    $template->setValue('customer_address', $sample->customer->address ?? $sample->customer_address ?? '-'); // müşteri ilişkisi varsa
    $template->setValue('product_name', $sample->product->name ?? '-');
    $template->setValue('batch_no', $sample->batch_no ?? '-');
    $template->setValue('report_no', $reportNo);
    $template->setValue('report_period', $reportPeriod);
    $template->setValue('accepted_at', $sample->taken_at ? Carbon::parse($sample->taken_at)->format('d.m.Y H:i') : '-');
    $template->setValue('sample_amount', $sample->amount ?? '-');
    $template->setValue('sampling_person', $sample->taken_by ?? '-');
    $template->setValue('report_date', $sample->report_date ? Carbon::parse($sample->report_date)->format('d.m.Y') : '-');
    $template->setValue('report_description', $sample->report_description ?? '-');
    $template->setValue('total_pages', '${NUMPAGES}');

    // ✅ Parametreler (scope kontrolü: TÜRKAK → 1, Kapsam Dışı → 2)
    $rows = [];
    foreach ($sample->parameters as $param) {
        $methodField = match ($lang) {
            'TR' => $param->method_tr,
            'EN' => $param->method_en,
            'RU' => $param->method_ru,
            default => $param->method_tr,
        };

        $paramName = $param->parameter ?? '-';

        if ($param->scope === 'TÜRKAK') {
            $methodField = '1 ' . $methodField;
        } elseif ($param->scope === 'Kapsam Dışı') {
            $methodField = '2 ' . $methodField;
        }

        $rows[] = [
            'method_name'     => $paramName,
            'analysis_method' => $methodField ?? '-',
            'unit'            => $param->unit ?? '-',
            'result'          => $param->pivot->result ?? '-',
        ];
    }

    if (empty($rows)) {
        $rows[] = [
            'method_name'     => '-',
            'analysis_method' => '-',
            'unit'            => '-',
            'result'          => '-',
        ];
    }

    $template->cloneRowAndSetValues('method_name', $rows);

    // ✅ Dosya kaydetme ve indirme (ürün adı da eklendi)
    $productSlug = Str::slug($sample->product->name ?? 'urun');
    $fileName = "Rapor-{$reportNo}-{$productSlug}-{$lang}.docx";
    $savePath = storage_path("app/reports/{$fileName}");
    $template->saveAs($savePath);

    return response()->download($savePath)->deleteFileAfterSend(true);
}



public function reportImageDelete(Request $request, SampleAcceptance $sample)
{
    $request->validate([
        'image' => 'required|string',
    ]);

    $images = $sample->report_images ?? [];

    // İlgili resmi array’den çıkar
    $images = array_filter($images, fn($img) => $img !== $request->image);

    // DB’ye güncelle
    $sample->update([
        'report_images' => array_values($images),
    ]);

    // Dosyayı storage’dan da silmek istersen:
    \Storage::disk('public')->delete('reports/'.$request->image);

    return back()->with('success','Resim silindi.');
}

    public function sendMail(SampleAcceptance $sample)
    {
        Mail::send('sample_acceptances.mail', ['sample' => $sample], function($message) use ($sample) {
            $message->to('laboratuvar@aripet.com') // ana alıcı
                    ->cc(['ucuncu.kullanici@example.com','ucuncu.kullanici@example.com']) // cc
                    ->subject("Numune Raporu - {$sample->report_no}");
        });

        return back()->with('success','Mail başarıyla gönderildi ✅');
    }

public function sendWhatsapp(SampleAcceptance $sample)
    {
        $token = env('WHATSAPP_TOKEN');
        $phoneNumberId = env('WHATSAPP_PHONE_ID');

        $message = "Merhaba ".($sample->customer->contact_person ?? 'Yetkili').",
        Numune raporunuz hazır:
        Firma: {$sample->company_name}
        Ürün: {$sample->product->name}
        Batch No: {$sample->batch_no}
        Rapor No: {$sample->report_no}
        Tarih: ".($sample->report_date?->format('d.m.Y') ?? '-');

                Http::withToken($token)->post("https://graph.facebook.com/v17.0/{$phoneNumberId}/messages", [
                    "messaging_product" => "whatsapp",
                    "to" => "905456502255", // kendi WhatsApp numaran
                    "type" => "text",
                    "text" => ["body" => $message],
                ]);

        return back()->with('success','WhatsApp mesajı gönderildi ✅');
    }

public function createManual()
{
    $parameters = LabParameter::all();
    return view('sample_acceptances.accept_manual', compact('parameters'));
}

public function storeManual(Request $request)
{
    $validated = $request->validate([
        'product_id'     => 'required|integer',
        'customer_id'    => 'required|integer',
        'batch_no'       => 'required|string',
        'country'        => 'required|string',
        'uncertainty'    => 'required|boolean',
        'report_request' => 'required|boolean',
        'report_no'      => 'nullable|string',
        'description'    => 'nullable|string',
        'amount'         => 'nullable|integer|min:1',
        'sample_type'    => 'required|string',
        'taken_place'    => 'required|string',
        'sampling_person'=> 'nullable|string',
        'taken_by'       => 'required|string',
        'taken_at'       => 'required|date',
        'protection'     => 'required|string',
        'parameters'     => 'required|array',
    ]);

    $lastRequestNo = SampleAcceptance::max('request_no') ?? 0;
    $customer = Customer::findOrFail($validated['customer_id']);

    $sample = SampleAcceptance::create([
        'product_id'     => $validated['product_id'],
        'company_name'   => $customer->name, // Customer tablosundan name alınıyor
        'batch_no'       => $validated['batch_no'],
        'country'        => $validated['country'],
        'uncertainty'    => $validated['uncertainty'],
        'report_request' => $validated['report_request'],
        'report_no'      => $validated['report_no'],
        'request_no'     => $lastRequestNo + 1,
        'sample_type'    => $validated['sample_type'],
        'description'    => $validated['description'],
        'taken_place'    => $validated['taken_place'],
        'sampling_person'=> $validated['sampling_person'],
        'taken_by'       => $validated['taken_by'],
        'taken_at'       => $validated['taken_at'],
        'amount'         => $validated['amount'] ?? 300,
        'protection'     => $validated['protection'],
        'status'         => 'accepted',
        'accepted_by'    => auth()->id(),
    ]);

    $sample->parameters()->sync($validated['parameters']);

    BusinessLog::create([
        'action' => 'Manuel Numune Kabul',
        'description' => "Batch No {$sample->batch_no} için manuel numune kaydı oluşturuldu ve kabul edildi.",
        'user_id' => auth()->id(),
        'sample_acceptance_id' => $sample->id,
    ]);

    // ✅ Otomatik mail gönderme
    Mail::raw("Batch No {$sample->batch_no} manuel olarak kabul edilmiştir.", function($message) use ($sample) {
        $message->to('laboratuvar@aripet.com')
                ->subject("Manuel Numune Kabul - Batch {$sample->batch_no}");
    });

    return redirect()->route('samples.index')
                     ->with('success','Manuel numune kaydı oluşturuldu ve mail gönderildi.');
}




public function batchNumbers()
{
    $batches = SampleAcceptance::select('batch_no')
                ->orderBy('id','desc')
                ->get();

    $duplicates = $batches->groupBy('batch_no')
                          ->filter(fn($group) => $group->count() > 1)
                          ->keys();

    return view('sample_acceptances.batch_numbers', compact('batches','duplicates'));
}

public function destroy($id)
{
    $sample = SampleAcceptance::findOrFail($id);
    $sample->delete();

    return redirect()->route('samples.index')
                     ->with('success', 'Numune silindi.');
}



    



}