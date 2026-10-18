<?php

namespace App\Http\Controllers;
use Intervention\Image\ImageManager;
use App\Models\SampleAcceptance;
use App\Models\LabParameter;
use App\Models\BusinessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\IOFactory;
use App\Models\SampleAcceptanceAddition;
use App\Exports\SampleAcceptanceExport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory as ExcelIOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as ExcelWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet as ExcelSpreadsheet;
use SimpleSoftwareIO\QrCode\Facades\QrCode;



class SampleAcceptanceController extends Controller
{
    /**
     * Bekleyen numuneleri listele 
     */
    public function index()
    {
        $pendingSamples = SampleAcceptance::where('status','pending')
            ->with('product')
            ->latest()
            ->get();

        // accepted + in_analysis + reported hepsini tek koleksiyonda topla
        $acceptedSamples = SampleAcceptance::whereIn('status', ['accepted','in_analysis','reported'])
            ->with('product')
            ->latest()
            ->get();

        // Detay modallarindaki islem gecmisini tek sorguda yukle (N+1 sorguyu onler).
        $sampleLogsById = BusinessLog::with('user')
            ->whereIn('sample_acceptance_id', $acceptedSamples->pluck('id'))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->groupBy('sample_acceptance_id');

        return view('sample_acceptances.index', compact('pendingSamples', 'acceptedSamples', 'sampleLogsById'));
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
            'offer_id'     => $request->offer_id ?? null, // ✅ teklif bağlama
        ]);

        if(!empty($request->offer_id)){
            $count = \App\Models\OfferSample::where('offer_id',$request->offer_id)->count() + 1;
            \App\Models\OfferSample::create([
                'offer_id'  => $request->offer_id,
                'sample_id' => $sample->id,
                'sequence'  => $count,
            ]);
            \App\Models\Offer::find($request->offer_id)->decrement('sample_count');
        }

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
            'offer_id'       => 'nullable|exists:offers,id', 
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
            'offer_id'       => $validated['offer_id'] ?? null, 
        ]);

        $sample->parameters()->sync($validated['parameters']);

        if(!empty($validated['offer_id'])){
            $count = \App\Models\OfferSample::where('offer_id',$validated['offer_id'])->count() + 1;

            \App\Models\OfferSample::create([
                'offer_id'  => $validated['offer_id'],
                'sample_id' => $sample->id,
                'sequence'  => $count,
            ]);

            \App\Models\Offer::find($validated['offer_id'])->decrement('sample_count');
        }

        BusinessLog::create([
            'action' => 'Numune Kabul + Parametre Atama',
            'description' => "Batch No {$sample->batch_no} için parametreler atandı ve teklif ile ilişkilendirildi.",
            'user_id' => auth()->id(),
            'sample_acceptance_id' => $sample->id,
        ]);

        return redirect()->route('samples.analysis-pending')
                        ->with('success','Numune kabul edildi ve Analiz Bekleyenlere gönderildi.');
    }

    /**
     * Analiz sonucu kayıt ve revizyonlarını tek yerden izler.
     * İlk kayıt ve gerçek revizyon farklı işlem adlarıyla loglanır.
     */
    private function saveTrackedAnalysisResult(SampleAcceptance $sample, int $paramId, array $data, ?string $reason): array
    {
        $parameter = $sample->parameters()->where('lab_parameters.id', $paramId)->firstOrFail();
        $pivot = $parameter->pivot;

        $oldValue = trim((string) ($pivot->result ?? ''));
        $newValue = trim((string) $data['value']);
        $oldDate = $pivot->result_date ? Carbon::parse($pivot->result_date)->format('Y-m-d H:i') : null;
        $newDate = Carbon::parse($data['date'])->format('Y-m-d H:i');
        $oldDescription = trim((string) ($pivot->description ?? ''));
        $newDescription = trim((string) ($data['description'] ?? ''));

        $changes = [];
        if ($oldValue !== $newValue) {
            $changes[] = 'Sonuç: ' . ($oldValue === '' ? '-' : $oldValue) . ' -> ' . $newValue;
        }
        if ($oldDate !== $newDate) {
            $changes[] = 'Analiz tarihi: ' . ($oldDate ?? '-') . ' -> ' . $newDate;
        }
        if ($oldDescription !== $newDescription) {
            $changes[] = 'Açıklama: ' . ($oldDescription === '' ? '-' : $oldDescription) . ' -> ' . ($newDescription === '' ? '-' : $newDescription);
        }
        if (!$changes) {
            return ['changed' => false, 'revision' => false];
        }

        $revision = $oldValue !== '';
        if ($revision && trim((string) $reason) === '') {
            throw ValidationException::withMessages([
                'revision_reason' => 'Daha önce kaydedilmiş analiz sonucunu revize etmek için gerekçe giriniz.',
            ]);
        }

        $username = auth()->user()?->name ?? 'Bilinmeyen kullanıcı';
        $action = $revision ? 'Analiz Sonucu Revizyonu' : 'Analiz Sonucu Girişi';
        $description = "Batch No {$sample->batch_no} - {$parameter->parameter} analizi, {$username} tarafından " .
            ($revision ? 'revize edildi.' : 'kaydedildi.');
        if ($revision) {
            $description .= "\nRevizyon gerekçesi: " . trim((string) $reason);
        }
        $description .= "\n" . implode("\n", $changes);

        $sample->parameters()->updateExistingPivot($paramId, [
            'result' => $newValue,
            'result_date' => Carbon::parse($data['date'])->format('Y-m-d H:i:s'),
            'description' => $newDescription === '' ? null : $newDescription,
            'updated_at' => now(),
        ]);
        BusinessLog::create([
            'action' => $action,
            'description' => $description,
            'user_id' => auth()->id(),
            'sample_acceptance_id' => $sample->id,
        ]);

        return ['changed' => true, 'revision' => $revision, 'message' => $description];
    }

    private function sendAnalysisRevisionEmails(SampleAcceptance $sample, array $messages): bool
    {
        if (!$messages) return true;
        try {
            Mail::raw(implode("\n\n-----\n\n", $messages), function ($mail) use ($sample) {
                $mail->to('mrt.krmn.55@gmail.com')
                    ->subject('Analiz Sonucu Revizyon Bildirimi - ' . $sample->batch_no);
            });
            return true;
        } catch (\Throwable $e) {
            Log::error('Analiz sonucu revizyon e-postası gönderilemedi', [
                'sample_id' => $sample->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /** Sonuçlar tamamen girildiyse kayıtları kilitle. */
    private function analysisIsComplete(SampleAcceptance $sample): bool
    {
        $sample->load('parameters');
        return $sample->parameters->isNotEmpty()
            && $sample->parameters->every(fn ($parameter) =>
                $parameter->pivot->result !== null
                && trim((string) $parameter->pivot->result) !== ''
            );
    }

    public function storeResults(Request $request, SampleAcceptance $sample)
    {
        $validated = $request->validate([
            'results' => 'required|array',
            'results.*.value' => 'required|string',
            'results.*.date' => 'required|date',
            'results.*.description' => 'nullable|string',
            'revision_reason' => 'nullable|string|max:1000',
        ]);
        $revisionEmails = [];
        $finishedNow = DB::transaction(function () use ($sample, $validated, &$revisionEmails) {
            $sample = SampleAcceptance::whereKey($sample->id)->lockForUpdate()->firstOrFail();
            if ($this->analysisIsComplete($sample)) {
                throw ValidationException::withMessages(['results' => 'Analiz tamamlandı ve kilitlendi. Sonuçlar bu ekrandan değiştirilemez.']);
            }
            foreach ($validated['results'] as $paramId => $data) {
                $result = $this->saveTrackedAnalysisResult(
                    $sample, (int) $paramId, $data, $validated['revision_reason'] ?? null
                );
                if ($result['revision']) $revisionEmails[] = $result['message'];
            }
            if ($sample->status === 'accepted') $sample->update(['status' => 'in_analysis']);
            if ($this->analysisIsComplete($sample)) {
                BusinessLog::create([
                    'action' => 'Analiz Tamamlandı',
                    'description' => "Batch No {$sample->batch_no} için tüm analiz sonuçları tamamlandı ve kilitlendi. İşlemi yapan: " . (auth()->user()?->name ?? 'Sistem'),
                    'user_id' => auth()->id(),
                    'sample_acceptance_id' => $sample->id,
                ]);
                return true;
            }
            return false;
        });
        $mailOk = $this->sendAnalysisRevisionEmails($sample, $revisionEmails);
        if ($finishedNow) {
            return redirect()->route('samples.results.edit', $sample->id)
                ->with('analysis_completed_redirect', true)
                ->with('success', 'Tüm analizler tamamlandı ve kilitlendi. 3 saniye sonra Raporlar sayfasına geçilecek.');
        }
        return redirect()->route('samples.analysis-pending')
            ->with('success', $mailOk ? 'Analiz sonuçları ve işlem geçmişi kaydedildi.' : 'Sonuçlar ve loglar kaydedildi; revizyon e-postası gönderilemedi.');
    }

    public function editResults(SampleAcceptance $sample)
    {
        $sample->load('parameters');
        $analysisLocked = $this->analysisIsComplete($sample);
        $analysisLogs = BusinessLog::with('user')
            ->where('sample_acceptance_id', $sample->id)
            ->whereIn('action', ['Analiz Sonucu', 'Analiz Sonucu Girişi', 'Analiz Sonucu Revizyonu', 'Analiz Tamamlandı'])
            ->orderByDesc('created_at')->orderByDesc('id')->get();
        return view('sample_acceptances.results_edit', compact('sample', 'analysisLogs', 'analysisLocked'));
    }

    public function storeSingle(Request $request, SampleAcceptance $sample, $paramId)
    {
        $validated = $request->validate([
            'value' => 'required|string',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'revision_reason' => 'nullable|string|max:1000',
        ]);
        $result = DB::transaction(function () use ($sample, $paramId, $validated) {
            $sample = SampleAcceptance::whereKey($sample->id)->lockForUpdate()->firstOrFail();
            if ($this->analysisIsComplete($sample)) {
                throw ValidationException::withMessages(['value' => 'Analiz tamamlandı ve kilitlendi. Sonuçlar bu ekrandan değiştirilemez.']);
            }
            $result = $this->saveTrackedAnalysisResult($sample, (int) $paramId, $validated, $validated['revision_reason'] ?? null);
            if ($result['changed'] && $sample->status === 'accepted') {
                $sample->update(['status' => 'in_analysis']);
            }
            $result['finished_now'] = false;
            if ($result['changed'] && $this->analysisIsComplete($sample)) {
                BusinessLog::create([
                    'action' => 'Analiz Tamamlandı',
                    'description' => "Batch No {$sample->batch_no} için tüm analiz sonuçları tamamlandı ve kilitlendi. İşlemi yapan: " . (auth()->user()?->name ?? 'Sistem'),
                    'user_id' => auth()->id(),
                    'sample_acceptance_id' => $sample->id,
                ]);
                $result['finished_now'] = true;
            }
            return $result;
        });
        if (!$result['changed']) {
            return back()->with('success', 'Sonuç değişmedi; yeni log oluşturulmadı.');
        }
        $mailOk = !$result['revision'] || $this->sendAnalysisRevisionEmails($sample, [$result['message']]);
        if ($result['finished_now']) {
            return redirect()->route('samples.results.edit', $sample->id)
                ->with('analysis_completed_redirect', true)
                ->with('success', 'Tüm analizler tamamlandı ve kilitlendi. 3 saniye sonra Raporlar sayfasına geçilecek.' . ($mailOk ? '' : ' Revizyon maili gönderilemedi.'));
        }
        return back()->with('success', $mailOk
            ? ($result['revision'] ? 'Analiz revizyonu kaydedildi, log ve bildirim oluşturuldu.' : 'İlk analiz sonucu ve log kaydedildi.')
            : 'Analiz revizyonu ve log kaydedildi; bildirim e-postası gönderilemedi.');
    }

    /**
     * Analiz bekleyen numuneler
     */
        public function analysisPending(Request $request)
        {
            $query = SampleAcceptance::with(['parameters','labProduct']) 
                        ->whereIn('status',['accepted','in_analysis','reported']); 

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
                $sample->load(['parameters', 'labProduct.parameters']);
                $limitIssues = \App\Http\Controllers\LabReportApprovalController::limitIssues($sample);
                $limitApproved = \App\Http\Controllers\LabReportApprovalController::limitApproved($sample);
                $canApproveLimit = auth()->check() && \Illuminate\Support\Facades\Gate::allows('approve-lab-reports')
                    && (int) DB::table('lab_report_settings')->value('laboratory_manager_user_id') === (int) auth()->id();
                $analysisParameters = $sample->parameters;
                $analysisComplete = $analysisParameters->isNotEmpty()
                    && $analysisParameters->every(fn ($p) => $p->pivot->result !== null
                        && trim((string) $p->pivot->result) !== ''
                        && $p->pivot->result_date !== null);
                $lastAnalysisAt = $analysisComplete
                    ? $analysisParameters->max(fn ($p) => Carbon::parse($p->pivot->result_date)->timestamp)
                    : null;
                $lastAnalysisAt = $lastAnalysisAt !== null ? Carbon::createFromTimestamp($lastAnalysisAt, config('app.timezone')) : null;
                // Rapor tarihi gün hassasiyetinde: son analiz günü de seçilebilir.
                // Numune girişinden daha eski bir gün de kabul edilmez.
                $firstReportDate = $lastAnalysisAt
                    ? max(
                        $lastAnalysisAt->toDateString(),
                        $sample->taken_at ? Carbon::parse($sample->taken_at)->toDateString() : $lastAnalysisAt->toDateString()
                    )
                    : null;
                $reportLogs = BusinessLog::with('user')
                    ->where('sample_acceptance_id', $sample->id)
                    ->whereIn('action', ['Rapor Kaydı', 'Rapor Tarihi Revizyonu', 'Rapor Bilgi Revizyonu'])
                    ->orderByDesc('created_at')->orderByDesc('id')->get();
                return view('sample_acceptances.show', compact('sample', 'analysisComplete', 'lastAnalysisAt', 'firstReportDate', 'reportLogs', 'limitIssues', 'limitApproved', 'canApproveLimit'));
            }
            
               public function labTvShow()
                {
                    $samples = SampleAcceptance::orderByDesc('taken_at')
                        ->take(50) 
                        ->get();
            
                    return view('labtv.show', compact('samples'));
                }


public function edit(SampleAcceptance $sample)
{
    $sample->load('parameters');
    $parameters = LabParameter::all();
    $products = \App\Models\LabProduct::select('id', 'product_code', 'product_name')->get();
    $customers = Customer::all();
    $sampleLogs = BusinessLog::with('user')
        ->where('sample_acceptance_id', $sample->id)
        ->orderByDesc('created_at')->orderByDesc('id')->get();

    return view('sample_acceptances.edit', compact('sample', 'parameters', 'products', 'customers', 'sampleLogs'));
}

public function update(Request $request, SampleAcceptance $sample)
{
    $validated = $request->validate([
        'product_id' => 'required|exists:lab_products,id',
        'company_name' => 'required|string|max:255',
        'batch_no' => 'required|string|max:255',
        'request_no' => 'nullable|string|max:255',
        'sample_type' => 'required|string|max:255',
        'taken_place' => 'required|string|max:255',
        'sampling_person' => 'nullable|string|max:255',
        'taken_by' => 'required|string|max:255',
        'taken_at' => 'required|date',
        'country' => 'nullable|string|max:255',
        'report_no' => 'nullable|string|max:255',
        'amount' => 'nullable|integer|min:1',
        'protection' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'selected_parameters' => 'nullable|array',
        'selected_parameters.*' => 'integer|exists:lab_parameters,id',
        'offer_id' => 'nullable|exists:offers,id',
        'change_reason' => 'nullable|string|max:1000',
    ]);

    $names = [
        'product_id' => 'Ürün', 'company_name' => 'Firma', 'batch_no' => 'Batch No',
        'request_no' => 'Talep No', 'sample_type' => 'Numune Türü',
        'taken_place' => 'Alındığı Yer', 'sampling_person' => 'Alan Kişi',
        'taken_by' => 'Alan Kişi Tipi', 'taken_at' => 'Numune Kabul Tarihi',
        'country' => 'Ülke', 'report_no' => 'Rapor No', 'amount' => 'Miktar',
        'protection' => 'Koruma', 'description' => 'Açıklama', 'offer_id' => 'Teklif',
    ];

    // Formdaki tüm düzenlenebilir alanların eski ve yeni değerlerini karşılaştır.
    $updates = [];
    foreach ($names as $field => $label) {
        $updates[$field] = $validated[$field] ?? ($field === 'amount' ? 300 : null);
    }
    // HTML datetime-local dakika hassasiyetindedir; aynı dakikayı farklılık sayma.
    $oldDate = $sample->taken_at ? Carbon::parse($sample->taken_at)->format('Y-m-d H:i') : null;
    $newDate = Carbon::parse($updates['taken_at'])->format('Y-m-d H:i');
    $updates['taken_at'] = $newDate;

    $changes = [];
    foreach ($updates as $field => $value) {
        if ($field === 'taken_at') {
            if ($oldDate !== $newDate) {
                $changes[] = "{$names[$field]}: " . ($oldDate ?? '-') . " -> " . $newDate;
            }
            continue;
        }
        $before = $sample->getAttribute($field);
        $oldValue = $before === null ? '' : trim((string) $before);
        $newValue = $value === null ? '' : trim((string) $value);
        if ($oldValue !== $newValue) {
            $changes[] = "{$names[$field]}: " . ($oldValue === '' ? '-' : $oldValue) .
                " -> " . ($newValue === '' ? '-' : $newValue);
        }
    }

    $previousParams = $sample->parameters()->pluck('lab_parameters.id')->map(fn ($id) => (int) $id)->all();
    $selectedParams = array_values(array_unique(array_map('intval', $validated['selected_parameters'] ?? [])));
    $addedIds = array_values(array_diff($selectedParams, $previousParams));
    $removedIds = array_values(array_diff($previousParams, $selectedParams));
    if ($addedIds || $removedIds) {
        $paramNames = LabParameter::whereIn('id', array_merge($addedIds, $removedIds))->pluck('parameter', 'id');
        if ($addedIds) $changes[] = 'Eklenen parametreler: ' . collect($addedIds)->map(fn ($id) => $paramNames[$id] ?? (string)$id)->implode(', ');
        if ($removedIds) $changes[] = 'Çıkarılan parametreler: ' . collect($removedIds)->map(fn ($id) => $paramNames[$id] ?? (string)$id)->implode(', ');
    }

    if (!$changes) {
        return redirect()->route('samples.edit', $sample->id)->with('success', 'Değişiklik bulunmadı; kayıt korunmuştur.');
    }
    $reason = trim($validated['change_reason'] ?? '');
    if ($reason === '') {
        throw ValidationException::withMessages(['change_reason' => 'Her numune güncellemesi için değişiklik gerekçesi zorunludur.']);
    }

    $username = auth()->user()?->name ?? 'Bilinmeyen kullanıcı';
    $batch = $sample->batch_no;
    $description = "{$batch} numaralı numune, {$reason} gerekçesi ile {$username} tarafından revize edildi." . "\n" . implode("\n", $changes);
    $oldOffer = $sample->offer_id;
    $newOffer = $updates['offer_id'];

    DB::transaction(function () use ($sample, $updates, $selectedParams, $oldOffer, $newOffer, $description) {
        $sample->update($updates);
        // Eşleşen mevcut pivot satırlarının analiz sonuçlarını korur.
        $sample->parameters()->sync($selectedParams);
        if ((string)($oldOffer ?? '') !== (string)($newOffer ?? '')) {
            if ($oldOffer) {
                \App\Models\OfferSample::where('offer_id', $oldOffer)->where('sample_id', $sample->id)->delete();
                \App\Models\Offer::whereKey($oldOffer)->increment('sample_count');
            }
            if ($newOffer) {
                $sequence = \App\Models\OfferSample::where('offer_id', $newOffer)->max('sequence') + 1;
                \App\Models\OfferSample::firstOrCreate(
                    ['offer_id' => $newOffer, 'sample_id' => $sample->id],
                    ['sequence' => $sequence]
                );
                \App\Models\Offer::whereKey($newOffer)->decrement('sample_count');
            }
        }
        BusinessLog::create([
            'action' => 'Numune Bilgi Revizyonu',
            'description' => $description,
            'user_id' => auth()->id(),
            'sample_acceptance_id' => $sample->id,
        ]);
    });

    // Bildirim hatası, başarılı güncellemeyi geri almamalı.
    try {
        Mail::raw($description, function ($message) use ($batch) {
            $message->to('mrt.krmn.55@gmail.com')->subject('Numune Güncelleme Bildirimi - ' . $batch);
        });
        $feedback = 'Numune güncellendi, işlem geçmişi kaydedildi ve e-posta gönderildi.';
    } catch (\Throwable $e) {
        Log::error('Numune revizyon bildirimi gönderilemedi', ['sample_id' => $sample->id, 'error' => $e->getMessage()]);
        $feedback = 'Numune ve işlem geçmişi kaydedildi; e-posta gönderilemedi. Mail ayarlarını kontrol edin.';
    }
    return redirect()->route('samples.edit', $sample->id)->with('success', $feedback);
}

public function reportStore(Request $request)
{
    $validated = $request->validate([
        'sample_id'   => 'required|exists:sample_acceptances,id',
        'report_date' => 'required|date_format:Y-m-d',
        'report_change_reason' => 'nullable|string|max:1000',
        'description' => 'nullable|string',
        'images.*'    => 'nullable|image|max:5120',
    ]);

    $sample = SampleAcceptance::with('labProduct')
        ->findOrFail($validated['sample_id']);
    if (!\App\Http\Controllers\LabReportApprovalController::limitApproved($sample)) {
        return back()->withInput()->with('error', 'Yonetici Onayi Bekliyor: Uygunsuz analiz icin laboratuvar muduru izni gereklidir.');
    }

    // Rapor tarihi sadece gun tutar; son analiz gunu dahil, onceki gunler engelli.
    // Kontrol hem formda hem sunucuda uygulanir.
    $sample->load('parameters');
    if ($sample->parameters->isEmpty()
        || $sample->parameters->contains(fn ($p) => $p->pivot->result === null
            || trim((string) $p->pivot->result) === ''
            || $p->pivot->result_date === null)) {
        throw ValidationException::withMessages([
            'report_date' => 'Tüm analiz sonuçları ve analiz tarih-saatleri kaydedilmeden rapor oluşturulamaz.',
        ]);
    }

    $lastAnalysisTimestamp = $sample->parameters
        ->max(fn ($p) => Carbon::parse($p->pivot->result_date)->timestamp);
    $lastAnalysisAt = Carbon::createFromTimestamp($lastAnalysisTimestamp, config('app.timezone'));
    $firstAllowedDate = max(
        $lastAnalysisAt->toDateString(),
        $sample->taken_at ? Carbon::parse($sample->taken_at)->toDateString() : $lastAnalysisAt->toDateString()
    );
    $newReportDate = Carbon::createFromFormat('Y-m-d', $validated['report_date'])->toDateString();
    if ($newReportDate < $firstAllowedDate) {
        throw ValidationException::withMessages([
            'report_date' => 'Son analiz ' . $lastAnalysisAt->format('d.m.Y H:i')
                . ' tarihinde tamamlandı. En erken rapor tarihi: '
                . Carbon::parse($firstAllowedDate)->format('d.m.Y') . '.',
        ]);
    }
    $oldReportDate = $sample->report_date ? Carbon::parse($sample->report_date)->toDateString() : null;
    $dateChanged = $oldReportDate !== $newReportDate;
    $oldDescription = $sample->report_description;
    $newDescription = $validated['description'] ?? null;
    $descriptionChanged = (string) ($oldDescription ?? '') !== (string) ($newDescription ?? '');
    $reason = trim((string) ($validated['report_change_reason'] ?? ''));
    if ($oldReportDate !== null && $dateChanged && $reason === '') {
        throw ValidationException::withMessages([
            'report_change_reason' => 'Rapor tarihi revizyonu için gerekçe zorunludur.',
        ]);
    }

    $images = $sample->report_images ?? [];

    if ($request->hasFile('images')) {

        $picturePath = public_path('reports/picture');
        $thumbPath = public_path('reports/picture/thumbs');

        if (!file_exists($picturePath)) {
            mkdir($picturePath, 0777, true);
        }

        if (!file_exists($thumbPath)) {
            mkdir($thumbPath, 0777, true);
        }

        $manager = \Intervention\Image\ImageManager::usingDriver(
            \Intervention\Image\Drivers\Gd\Driver::class
        );

        foreach ($request->file('images') as $img) {

            if (!$img || !$img->isValid()) {
                continue;
            }

            $fileName = uniqid('report_', true) . '.jpg';

            $image = $manager->decodeSplFileInfo($img);

            $image->scale(width: 1200);

            $image->encode(
                new \Intervention\Image\Encoders\JpegEncoder(
                    quality: 85
                )
            )->save(
                $picturePath . '/' . $fileName
            );

            $thumb = $manager->decodeSplFileInfo($img);

            $thumb->scale(width: 300);

            $thumb->encode(
                new \Intervention\Image\Encoders\JpegEncoder(
                    quality: 80
                )
            )->save(
                $thumbPath . '/' . $fileName
            );

            $images[] = $fileName;
        }
    }

    $sample->update([
        'report_date'        => $newReportDate,
        'report_description' => $validated['description'] ?? null,
        'report_images'      => array_values($images),
        'status'             => 'reported',
    ]);

    // Rapor tarihinin ilk kaydı ve her revizyonu izlenebilir şekilde saklanır.
    if ($dateChanged || $descriptionChanged) {
        $changes = [];
        if ($dateChanged) {
            $changes[] = 'Rapor tarihi: ' . ($oldReportDate ? Carbon::parse($oldReportDate)->format('d.m.Y') : 'Boş')
                . ' -> ' . Carbon::parse($newReportDate)->format('d.m.Y');
        }
        if ($descriptionChanged) {
            $changes[] = 'Rapor açıklaması: ' . ($oldDescription ?: 'Boş')
                . ' -> ' . ($newDescription ?: 'Boş');
        }
        BusinessLog::create([
            'action' => $dateChanged
                ? ($oldReportDate === null ? 'Rapor Kaydı' : 'Rapor Tarihi Revizyonu')
                : 'Rapor Bilgi Revizyonu',
            'description' => 'Batch No ' . $sample->batch_no . ' numaralı numunenin rapor bilgileri '
                . (auth()->user()?->name ?? 'Sistem') . ' tarafından kaydedildi/revize edildi. '
                . implode(' | ', $changes)
                . ($reason !== '' ? ' | Gerekçe: ' . $reason : '')
                . ' | Son analiz: ' . $lastAnalysisAt->format('d.m.Y H:i'),
            'user_id' => auth()->id(),
            'sample_acceptance_id' => $sample->id,
        ]);
    }

    $url = route('samples.verify', [
        'sample' => $sample->id
    ]);

    $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(300)
        ->encoding('UTF-8')
        ->generate($url);

    $reportsPath = public_path('reports');

    if (!file_exists($reportsPath)) {
        mkdir($reportsPath, 0777, true);
    }

    $safeReportNo = preg_replace(
        '/[^A-Za-z0-9\-_]/',
        '-',
        $sample->report_no ?? $sample->id
    );

    $qrFileName = "raporno-{$safeReportNo}.svg";

    file_put_contents(
        $reportsPath . '/' . $qrFileName,
        $qrSvg
    );

    return redirect()
        ->route('samples.show', $sample->id)
        ->with(
            'success',
            '📌 Raporunuz kaydedildi. Doğrulama QR kodu hazırlandı. İstediğiniz dilde raporunuzu indirebilirsiniz.'
        )
        ->with('qr_file', $qrFileName);
}



public function generateReport(SampleAcceptance $sample, Request $request, string $lang = 'TR')
{
    $sample->load('parameters');
    if (!\App\Http\Controllers\LabReportApprovalController::limitApproved($sample)) {
        return redirect()->route('samples.show', $sample->id)->with('error', 'Yonetici Onayi Bekliyor: Rapor indirilemez.');
    }
    if (!$sample->report_date || $sample->parameters->isEmpty()
        || $sample->parameters->contains(fn ($p) => $p->pivot->result === null
            || trim((string) $p->pivot->result) === '' || $p->pivot->result_date === null)) {
        return redirect()->route('samples.show', $sample->id)
            ->with('error', 'Analiz tamamlanmadan ve rapor tarihi kaydedilmeden rapor oluşturulamaz.');
    }
    $latestAt = $sample->parameters->max(fn ($p) => Carbon::parse($p->pivot->result_date)->timestamp);
    $analysisDay = Carbon::createFromTimestamp($latestAt, config('app.timezone'))->toDateString();
    $minimum = max(
        $analysisDay,
        $sample->taken_at ? Carbon::parse($sample->taken_at)->toDateString() : $analysisDay
    );
    if (Carbon::parse($sample->report_date)->toDateString() < $minimum) {
        return redirect()->route('samples.show', $sample->id)
            ->with('error', 'Rapor tarihi son analiz tarihinden önce olamaz. Tarihi düzeltin.');
    }
    $allOutOfScope = $sample->parameters->every(fn($param) => $param->scope === 'Kapsam Dışı');

    $templatePath = $allOutOfScope
        ? storage_path("app/templates/lab_report_out_of_scope.docx")
        : storage_path("app/templates/lab_report_standard.docx");

    $template = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

    $reportNo = $sample->report_no ?? '-';
    $reportPeriod = $sample->report_date
        ? \Carbon\Carbon::parse($sample->report_date)->format('y-m')
        : \Carbon\Carbon::now()->format('y-m');

    // ✅ Genel alanlar
    $template->setValue('company_name', $sample->company_name ?? '-');
    $template->setValue('customer_address', $this->resolveReportCustomerAddress($sample) ?: '-');
    $template->setValue('product_name', $sample->labProduct->product_name ?? '-');
    $template->setValue('batch_no', $sample->batch_no ?? '-');
    $template->setValue('report_no', $reportNo);
    $template->setValue('report_period', $reportPeriod);
    $template->setValue('accepted_at', $sample->taken_at ? \Carbon\Carbon::parse($sample->taken_at)->format('d.m.Y H:i') : '-');
    $template->setValue('sample_amount', $sample->amount ?? '-');
    $template->setValue('sampling_person', $sample->taken_by ?? '-');
    $template->setValue('report_date', $sample->report_date ? \Carbon\Carbon::parse($sample->report_date)->format('d.m.Y') : '-');
    $template->setValue('report_description', $sample->report_description ?? '-');
    $template->setValue('total_pages', '${NUMPAGES}');

    // ✅ Parametreler
    $rows = [];
    foreach ($sample->parameters as $param) {
        $methodField = match ($lang) {
            'TR' => $param->method_tr,
            'EN' => $param->method_en,
            'RU' => $param->method_ru,
            default => $param->method_tr,
        };

        $paramName = $param->parameter ?? '-';

        // Yalnizca akredite metodun yaninda * gosterecegiz.
        $methodField = ($methodField ?: '-') . ($param->scope === 'TÜRKAK' ? ' *' : '');

        $limits = $sample->labProduct->parameters
                    ->firstWhere('parameter_id', $param->id);

        $rows[] = [
            'method_name'     => $methodField,
            'analysis_method' => $paramName,
            'unit'            => $param->unit ?? '-',
            'result'          => $param->pivot->result ?? '-',
            'min_value'       => $limits->min_value ?? '-',
            'max_value'       => $limits->max_value ?? '-',
            'net_value'       => $limits->net_value ?? '-',
        ];
    }

    if (empty($rows)) {
        $rows[] = [
            'method_name'     => '-',
            'analysis_method' => '-',
            'unit'            => '-',
            'result'          => '-',
            'min_value'       => '-',
            'max_value'       => '-',
            'net_value'       => '-',
        ];
    }

    $template->cloneRowAndSetValues('analysis_method', $rows);

    // ✅ Word kaydet
    $productSlug = \Illuminate\Support\Str::slug($sample->labProduct->product_name ?? 'urun');
    $fileNameDocx = "Rapor-{$reportNo}-{$productSlug}-{$lang}.docx";
    $savePathDocx = storage_path("app/reports/{$fileNameDocx}");
    $template->saveAs($savePathDocx);

    // ✅ PDF oluştur
    $phpWord = \PhpOffice\PhpWord\IOFactory::load($savePathDocx);
    $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
    $htmlPath = storage_path("app/reports/temp-{$reportNo}.html");
    $htmlWriter->save($htmlPath);

    $htmlContent = file_get_contents($htmlPath);
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($htmlContent);

    $fileNamePdf = "Rapor-{$reportNo}-{$productSlug}-{$lang}.pdf";
    $savePathPdf = storage_path("app/reports/{$fileNamePdf}");
    $pdf->save($savePathPdf);

    // ✅ Kullanıcıya format seçeneği sun
    $format = $request->get('format', 'word'); // default word
    if ($format === 'pdf') {
        return response()->download($savePathPdf)->deleteFileAfterSend(false);
    }
    return response()->download($savePathDocx)->deleteFileAfterSend(false);
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
            $message->to('mrt.krmn.55@gmail.com') // ana alıcı
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
        'offer_id'       => 'nullable|exists:offers,id', 
    ]);

    $lastRequestNo = SampleAcceptance::max('request_no') ?? 0;
    $customer      = Customer::findOrFail($validated['customer_id']);
    $labProduct    = \App\Models\LabProduct::findOrFail($validated['product_id']);

    $sample = SampleAcceptance::create([
        'product_id'     => $labProduct->id,
        'company_name'   => $customer->name,
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
        // ✅ Teklif ID’sini SampleAcceptance’a da yaz
        'offer_id'       => $validated['offer_id'] ?? null,
    ]);

    $sample->parameters()->sync($validated['parameters']);

    // ✅ Pivot tabloya da bağla
    if(!empty($validated['offer_id'])){
        $count = \App\Models\OfferSample::where('offer_id',$validated['offer_id'])->count() + 1;

        \App\Models\OfferSample::create([
            'offer_id'  => $validated['offer_id'],
            'sample_id' => $sample->id,
            'sequence'  => $count,
        ]);

        \App\Models\Offer::find($validated['offer_id'])->decrement('sample_count');
    }

    BusinessLog::create([
        'action' => 'Manuel Numune Kabul',
        'description' => "Batch No {$sample->batch_no} için manuel numune kaydı oluşturuldu ve kabul edildi.",
        'user_id' => auth()->id(),
        'sample_acceptance_id' => $sample->id,
    ]);

    // Mail gönderimi
    $productName = $labProduct->product_name ?? '-';
    $country     = $sample->country ?? '-';
    $takenAt     = $sample->taken_at ? $sample->taken_at->format('d.m.Y H:i') : '-';

    Mail::raw("
    Batch No: {$sample->batch_no}
    Numune Adı: {$productName}
    Ülke: {$country}
    Kabul Tarihi: {$takenAt}
    ", function($message) use ($sample) {
        $message->to('mrt.krmn.55@gmail.com')
                ->cc(['labmanager@alkim.com', 'quality@alkim.com'])
                ->subject("Manuel Numune Kabul - Batch {$sample->batch_no}");
    });

    return redirect()->route('samples.index')
        ->with('success','Manuel numune kaydı oluşturuldu, teklif ile ilişkilendirildi ve mail gönderildi.')
        ->with('open_labels', route('samples.labels', $sample->id));
}

// ✅ Manuel numune kabul alanı tetiklemesi (etiket PDF)
public function labels(SampleAcceptance $sample)
{
    $pdf = Pdf::loadView('sample_acceptances.label', compact('sample'));
    return $pdf->stream("etiket-{$sample->batch_no}.pdf"); 
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

    if($sample->offer_id){
        \App\Models\OfferSample::where('offer_id',$sample->offer_id)
            ->where('sample_id',$sample->id)
            ->delete();

        \App\Models\Offer::find($sample->offer_id)->increment('sample_count');
    }

    $sample->delete();

    return redirect()->route('samples.index')
                     ->with('success', 'Numune silindi.');
}

public function storeAddition(Request $request, SampleAcceptance $sample)
{
    $validated = $request->validate([
        'reason' => 'required|string|max:255',
        'reason_date' => 'required|date',
        'tank_no' => 'nullable|string|max:50',
        'parameters' => 'nullable|array',
        'nonconformity_result' => 'nullable|string',
        'nonconformity_fix' => 'nullable|string',
    ]);

    SampleAcceptanceAddition::create([
        'sample_acceptance_id' => $sample->id,
        'sample_name' => $sample->product->name ?? '-',
        'batch_no' => $sample->batch_no,
        'tank_no' => $validated['tank_no'],
        'reason' => $validated['reason'],
        'user_id' => auth()->id(),
        'parameters' => $validated['parameters'],
        'nonconformity_result' => $validated['nonconformity_result'],
        'nonconformity_fix' => $validated['nonconformity_fix'],
        'reason_date' => $validated['reason_date'],
    ]);

    return back()->with('success','İlave neden başarıyla kaydedildi ✅');
}

public function indexAdditions()
{
    $additions = SampleAcceptanceAddition::with(['sample','user'])->latest()->paginate(20);
    return view('ilaveler.index', compact('additions'));
}

public function destroyAddition($id)
{
    $addition = SampleAcceptanceAddition::findOrFail($id);
    $addition->delete();

    return redirect()->route('additions.index')->with('success', 'İlave kaydı başarıyla silindi.');
}



public function exportExcel()
{
    
    session()->flash('status', 'Dosyanız hazırlanıyor, hazır olduğunda indirilecektir.');

    return Excel::download(new SampleAcceptanceExport, 'Numune-Kabul-Listesi.xlsx');
}



public function exportExcelTemplate()
{
    $samples = SampleAcceptance::with(['labProduct','parameters'])->get();

    $templatePath = storage_path("app/templates/samples_template.xlsx");
    $spreadsheet = ExcelIOFactory::load($templatePath);
    $sheet = $spreadsheet->getActiveSheet();

    $row = 5; // Başlık satırından sonra veri başlar
    foreach ($samples as $sample) {
        $params = $sample->parameters->map(fn($p) => $p->method_tr)->implode(', ');

        $sheet->setCellValue("A{$row}", $sample->report_no ?? '-'); // Rapor No
        $sheet->setCellValue("B{$row}", $sample->batch_no ?? '-'); // Batch No
        $sheet->setCellValue("C{$row}", $sample->taken_at 
            ? Carbon::parse($sample->taken_at)->format('d.m.Y H:i') 
            : '-'); // Numune Alma Tarihi & Saati
        $sheet->setCellValue("D{$row}", $sample->company_name ?? '-'); // Firma
        $sheet->setCellValue("E{$row}", $sample->labProduct->product_name ?? '-'); // Ürün
        $sheet->setCellValue("F{$row}", $params ?: '-'); // Parametre (Metot TR)

        $row++;
    }

    $fileName = "Numune-Kabul-Listesi.xlsx";
    $savePath = storage_path("app/reports/{$fileName}");

    $writer = new ExcelWriter($spreadsheet);
    $writer->save($savePath);

    return response()->download($savePath)->deleteFileAfterSend(true);
}

public function datetoexcel(Request $request)
{
    $start = $request->input('start_date');
    $end   = $request->input('end_date');

    $query = SampleAcceptance::with(['labProduct','parameters']);

    if ($start && $end) {
        $query->whereBetween('taken_at', [$start, $end]);
    }

    $samples = $query->get();

    $rows = [];
    foreach ($samples as $sample) {
        // ✅ Parametre adı + sonuç
        $params = $sample->parameters
            ->map(fn($p) => ($p->parameter ?? $p->name) . ': ' . ($p->pivot->result ?? '-'))
            ->implode(', ');

        $rows[] = [
            $sample->report_no ?? '-',                                        // Rapor No
            $sample->batch_no ?? '-',                                        // Batch No
            $sample->taken_at ? Carbon::parse($sample->taken_at)->format('d.m.Y H:i') : '-', // Numune Alma Tarihi & Saati
            $sample->company_name ?? '-',                                    // Firma
            $sample->country ?? '-',                                         // Ülke
            $sample->labProduct->product_name ?? '-',                        // Numune Adı
            $sample->sampling_person ?? '-',                                 // Numuneyi Alan
            $params ?: '-',                                                  // Analiz Sonuçları (parametre adı + sonuç)
        ];
    }

    $headings = [
        'Rapor No',
        'Batch No',
        'Numune Alma Tarihi & Saati',
        'Firma',
        'Ülke',
        'Numune Adı',
        'Numuneyi Alan',
        'Analiz Sonuçları',
    ];

    $spreadsheet = new ExcelSpreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->fromArray($headings, null, 'A1');
    $sheet->fromArray($rows, null, 'A2');

    $fileName = "Numune-Raporlari.xlsx";
    $savePath = storage_path("app/reports/{$fileName}");

    $writer = new ExcelWriter($spreadsheet);
    $writer->save($savePath);

    return response()->download($savePath)->deleteFileAfterSend(true);
}

    public function topluMailPage(Request $request)
        {
            $query = SampleAcceptance::with(['parameters','labProduct'])
                        ->where('status','reported');

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('report_date', [$request->start_date, $request->end_date]);
            }
            if ($request->filled('product_id')) {
                $query->where('product_id', $request->product_id);
            }

            $samples = $query->orderByDesc('report_date')->get();
            $products = \App\Models\LabProduct::select('id','product_name')->get();

            // Sabit mailleri controller’dan gönder
            $defaultEmails = ['mrt.krmn.55@gmail.com','quality@alkim.com'];

            return view('sample_acceptances.toplumail', compact('samples','products','defaultEmails'));
        }


  public function sendBulk(Request $request)
    {
        $validated = $request->validate([
            'sample_ids' => 'array',
            'emails'     => 'nullable|string',
            'mode'       => 'required|string', // selected veya all
        ]);

        // Kullanıcı mail adresleri (virgülle ayrılmış)
        $userEmails = !empty($validated['emails'])
            ? array_map('trim', explode(',', $validated['emails']))
            : [];

        // Sabit default mailler
        $defaultEmails = ['mrt.krmn.55@gmail.com', 'quality@alkim.com'];

        // İkisini birleştir → hem sabit hem kullanıcı
        $allEmails = array_unique(array_merge($defaultEmails, $userEmails));

        // Rapor seçimi
        if ($validated['mode'] === 'all') {
            $samples = SampleAcceptance::with(['parameters','labProduct','acceptedBy'])
                        ->where('status','reported')
                        ->get();
        } else {
            $samples = SampleAcceptance::with(['parameters','labProduct','acceptedBy'])
                        ->whereIn('id', $validated['sample_ids'] ?? [])
                        ->get();
        }

        $count = $samples->count();

        try {
            // Mail gönderimi
            Mail::send('sample_acceptances.mailbulk', ['samples' => $samples], function($message) use ($allEmails) {
                $message->to($allEmails)
                        ->subject("Toplu Numune Raporları");
            });

            return back()->with('success', $count.' rapor gönderildi ✅');
        } catch (\Exception $e) {
            // Hata olsa bile rapor sayısını bildir
            return back()->with('error', 'Mail gönderiminde hata oluştu ❌ ('.$count.' rapor gönderilmeye çalışıldı)');
        }
    }

    public function verify($sample)
    {
        $sample = SampleAcceptance::with([
            'parameters',
            'labProduct'
        ])->find($sample);
    
        if (!$sample) {
            return view('sample_acceptances.verify_result')
                ->with(
                    'error',
                    'Doğrulanacak laboratuvar raporu bulunamadı.'
                );
        }
    
        return view(
            'sample_acceptances.verify_result',
            compact('sample')
        );
    }

    /** Analizi bitmis, rapor tarihi bos tum numunelere son analiz gununu ata. */
    public function bulkFinalizeReports(Request $request)
    {
        $updated = 0;
        $skipped = 0;

        SampleAcceptance::whereIn('status', ['accepted', 'in_analysis', 'reported'])
            ->whereNull('report_date')
            ->orderBy('id')
            ->chunkById(200, function ($batch) use (&$updated, &$skipped) {
                foreach ($batch as $item) {
                    DB::transaction(function () use ($item, &$updated, &$skipped) {
                        $sample = SampleAcceptance::whereKey($item->id)
                            ->lockForUpdate()->first();

                        if (!$sample || $sample->report_date !== null) return;
                        $sample->load('parameters');

                        if ($sample->parameters->isEmpty() || $sample->parameters->contains(fn ($p) =>
                            $p->pivot->result === null || trim((string) $p->pivot->result) === ''
                            || !$p->pivot->result_date
                        )) {
                            $skipped++;
                            return;
                        }

                        $lastAt = $sample->parameters->max(fn ($p) =>
                            Carbon::parse($p->pivot->result_date)->timestamp
                        );
                        $lastAnalysis = Carbon::createFromTimestamp($lastAt, config('app.timezone'));
                        $reportDay = $lastAnalysis->toDateString();
                        $sampleDay = $sample->taken_at
                            ? Carbon::parse($sample->taken_at)->toDateString()
                            : $reportDay;

                        if ($reportDay < $sampleDay) {
                            $skipped++;
                            return;
                        }

                        if (!\App\Http\Controllers\LabReportApprovalController::limitApproved($sample)) {
                            $skipped++;
                            return;
                        }

                        $sample->update([
                            'report_date' => $reportDay,
                            'status' => 'reported',
                        ]);
                        BusinessLog::create([
                            'action' => 'Rapor Kaydı',
                            'description' => 'Batch No ' . $sample->batch_no
                                . ' icin toplu islem ile rapor tarihi '
                                . $lastAnalysis->format('d.m.Y')
                                . ' olarak atandi. Son analiz: '
                                . $lastAnalysis->format('d.m.Y H:i')
                                . '. Islemi yapan: '
                                . (auth()->user()?->name ?? 'Sistem'),
                            'user_id' => auth()->id(),
                            'sample_acceptance_id' => $sample->id,
                        ]);
                        $updated++;
                    });
                }
            });

        return redirect()->back()
            ->with('success', $updated . ' numune raporlandi. '
                . ($skipped ? $skipped . ' numune eksik/gecersiz analiz nedeniyle atlandi.' : ''));
    }

    /**
     * TS EN ISO/IEC 17025 rapor taslağı: tarayıcı önizlemesi ve PDF çıktısı.
     * Bu yöntem yetkilendirilmiş bir elektronik imza/onay akışı yerine geçmez.
     */
    /** Numuneye ait musterinin dogrulanabilir adresi (laboratuvar adresi kullanilmaz). */
    private function resolveReportCustomerAddress(SampleAcceptance $sample): string
    {
        $direct = trim((string) ($sample->customer_address ?? ''));
        if ($direct !== '') return $direct;

        if (!empty($sample->customer_id)) {
            $customer = Customer::find($sample->customer_id);
            if ($customer && filled($customer->address)) return trim((string) $customer->address);
        }

        $company = trim((string) ($sample->company_name ?? ''));
        if ($company === '') return '';
        // Benzersiz, buyuk-kucuk harf ve bosluk duyarsiz eslesme; belirsizlikte adres basma.
        $matches = Customer::query()
            ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($company, 'UTF-8')])
            ->limit(2)->get(['id', 'address']);
        if ($matches->count() === 1) return trim((string) ($matches->first()->address ?? ''));
        return '';
    }

    public function previewReport(SampleAcceptance $sample, Request $request)
    {
        $sample->load(['parameters','labProduct.parameters']);
        if (!\App\Http\Controllers\LabReportApprovalController::limitApproved($sample)) {
            return redirect()->route('samples.show', $sample->id)->with('error', 'Yonetici Onayi Bekliyor: PDF acilamaz.');
        }
        if (!\App\Http\Controllers\LabReportApprovalController::ready($sample)) {
            return redirect()->route('samples.show', $sample->id)
                ->with('error','Önizleme için analizler ve geçerli rapor tarihi tamamlanmalıdır.');
        }
        $settings = DB::table('lab_report_settings')->first();
        if (!$settings) {
            return redirect()->route('samples.reportPage')
                ->with('error','Önce deney raporu kurumsal ayarlarını kaydedin.');
        }
        $approval = DB::table('lab_report_approvals')
            ->where('sample_acceptance_id',$sample->id)->first();
        $digest = \App\Http\Controllers\LabReportApprovalController::fingerprint($sample,$settings);
        $approved = $approval && hash_equals($approval->content_hash,$digest);
        $manager = $settings->laboratory_manager_user_id
            ? \App\Models\User::find($settings->laboratory_manager_user_id) : null;
        $preparer = \App\Http\Controllers\LabReportApprovalController::primaryAnalyst($sample);
        $sig = $approved ? DB::table('lab_report_signatures')->where('user_id',$approval->approved_by)->first() : null;
        $preparerSig = $approved && $preparer ? DB::table('lab_report_signatures')->where('user_id',$preparer->id)->first() : null;
        if (!$sig || !$preparerSig || !$manager || !$preparer || (int)$manager->id !== (int)($approval->approved_by ?? 0)) $approved = false;
        $toDataUri = function (?string $path): ?string {
            if (!$path || !\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) return null;
            $raw = \Illuminate\Support\Facades\Storage::disk('local')->get($path);
            $mime = str_ends_with(strtolower($path), '.png') ? 'image/png' : 'image/jpeg';
            return 'data:' . $mime . ';base64,' . base64_encode($raw);
        };
        $reportTemplate = \App\Http\Controllers\LabReportTemplateController::load();
        $total = $sample->parameters->count();
        $accreditedCount = $sample->parameters->filter(fn($p) => trim((string)$p->scope) === 'TÜRKAK')->count();
        $accreditedPercent = $total ? ($accreditedCount * 100 / $total) : 0;
        $hasOutOfScope = $accreditedCount !== $total;
        // Kapsam karma oldugunda yetkili temsil ve beyan kontrol edilene kadar marka otomatik basılmaz.
        $showMark = $approved && $accreditedPercent > 51 && (bool)$settings->accreditation_mark_enabled
            && filled($settings->accreditation_number);
        $customerAddress = $this->resolveReportCustomerAddress($sample);
        $viewData = [
            'customerAddress'=>$customerAddress,
            'reportTemplate'=>$reportTemplate,'accreditedPercent'=>$accreditedPercent,
            'sample'=>$sample,'settings'=>$settings,'manager'=>$manager,'preparer'=>$preparer,
            'approved'=>$approved,'approvedAt'=>$approved ? $approval->approved_at : null,
            'approvalSignatureDataUri'=>$approved ? $toDataUri($sig->file_path) : null,
            'preparerSignatureDataUri'=>$approved && $preparerSig ? $toDataUri($preparerSig->file_path) : null,
            'stampDataUri'=>$approved ? $toDataUri($settings->stamp_path ?? null) : null,
            'logoDataUri'=>$toDataUri($settings->logo_path),
            'markDataUri'=>$showMark ? $toDataUri($settings->mark_path) : null,
            'hasOutOfScope'=>$hasOutOfScope,
            'canApprove'=>auth()->check() && \Illuminate\Support\Facades\Gate::allows('approve-lab-reports')
                && $manager && auth()->id() === (int)$manager->id,
        ];
        if ($request->query('download') === 'pdf') {
            $pdf = Pdf::loadView('sample_acceptances.report_preview',$viewData + ['pdfMode'=>true])
                ->setPaper('a4','portrait');
        if ($request->query('download') === 'pdf') {

            $pdf = Pdf::loadView(
                'sample_acceptances.report_preview',
                $viewData + ['pdfMode' => true]
            )->setPaper('a4', 'portrait');
            $reportNo = trim((string) (
                $sample->report_no ?: $sample->id
            ));
            $productName = trim((string) (
                $sample->labProduct?->product_name ?: 'Deney Raporu'
            ));
            $fileName = $reportNo . ' - ' . $productName;
            $fileName = preg_replace(
                '/[\\\\\/:*?"<>|\x00-\x1F]+/u',
                '-',
                $fileName
            );

            $fileName = trim(
                preg_replace('/\s+/u', ' ', $fileName),
                ' .-'
            );

            return $pdf->download($fileName . '.pdf');
        }
        }
        return view('sample_acceptances.report_preview',$viewData + ['pdfMode'=>false]);
    }

    public function reportPage()
    {
        $samples = SampleAcceptance::with(['parameters','labProduct'])
            ->whereIn('status',['accepted','in_analysis','reported'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('sample_acceptances.report', compact('samples'));
    }

public function statistics(Request $request)
{
    $startDate = $request->filled('start_date')
        ? Carbon::parse($request->start_date)->startOfDay()
        : Carbon::now()->startOfYear();

    $endDate = $request->filled('end_date')
        ? Carbon::parse($request->end_date)->endOfDay()
        : Carbon::now()->endOfDay();

    $dailyMonth = $request->filled('daily_month')
        ? Carbon::parse($request->daily_month . '-01')
        : Carbon::now();

    $dailyStart = $dailyMonth->copy()->startOfMonth();
    $dailyEnd = $dailyMonth->copy()->endOfMonth();

    $query = SampleAcceptance::with([
        'labProduct',
        'parameters'
    ])->whereBetween('taken_at', [
        $startDate,
        $endDate
    ]);

    if ($request->filled('product_id')) {
        $query->where('product_id', $request->product_id);
    }

    if ($request->filled('company_name')) {
        $query->where('company_name', $request->company_name);
    }

    if ($request->filled('sample_type')) {
        $query->where('sample_type', $request->sample_type);
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $samples = $query
        ->orderBy('taken_at')
        ->get();

    $dailyQuery = SampleAcceptance::with([
        'labProduct',
        'parameters'
    ])->whereBetween('taken_at', [
        $dailyStart,
        $dailyEnd
    ]);

    if ($request->filled('product_id')) {
        $dailyQuery->where('product_id', $request->product_id);
    }

    if ($request->filled('company_name')) {
        $dailyQuery->where('company_name', $request->company_name);
    }

    if ($request->filled('sample_type')) {
        $dailyQuery->where('sample_type', $request->sample_type);
    }

    if ($request->filled('status')) {
        $dailyQuery->where('status', $request->status);
    }

    $dailySampleCollection = $dailyQuery
        ->orderBy('taken_at')
        ->get();

    $totalSamples = $samples->count();

    $reportedSamples = $samples
        ->where('status', 'reported')
        ->count();

    $pendingSamples = $samples
        ->where('status', 'pending')
        ->count();

    $acceptedSamples = $samples
        ->where('status', 'accepted')
        ->count();

    $inAnalysisSamples = $samples
        ->where('status', 'in_analysis')
        ->count();

    $rejectedSamples = $samples
        ->where('status', 'rejected')
        ->count();

    $totalAnalyses = 0;
    $completedAnalyses = 0;

    foreach ($samples as $sample) {
        $totalAnalyses += $sample->parameters->count();

        $completedAnalyses += $sample->parameters
            ->filter(function ($parameter) {
                return $parameter->pivot->result !== null
                    && $parameter->pivot->result !== '';
            })
            ->count();
    }

    $pendingAnalyses = max(
        $totalAnalyses - $completedAnalyses,
        0
    );

    $analysisCompletionRate = $totalAnalyses > 0
        ? round(
            ($completedAnalyses / $totalAnalyses) * 100,
            1
        )
        : 0;

    $activeCompanies = $samples
        ->pluck('company_name')
        ->filter()
        ->unique()
        ->count();

    $durationDays = $samples
        ->filter(function ($sample) {
            return $sample->taken_at
                && $sample->report_date;
        })
        ->map(function ($sample) {
            $start = Carbon::parse(
                $sample->taken_at
            )->startOfDay();

            $end = Carbon::parse(
                $sample->report_date
            )->startOfDay();

            return $start->diffInDays($end);
        });

    $averageReportDays = $durationDays->count() > 0
        ? round($durationDays->avg(), 1)
        : 0;

    $fastestReportDays = $durationDays->count() > 0
        ? $durationDays->min()
        : 0;

    $slowestReportDays = $durationDays->count() > 0
        ? $durationDays->max()
        : 0;

    $monthlyLabels = [];
    $monthlySamples = [];
    $monthlyAnalyses = [];
    $monthlyReported = [];
    $monthlyAverageDuration = [];

    $monthCursor = $startDate
        ->copy()
        ->startOfMonth();

    $lastMonth = $endDate
        ->copy()
        ->startOfMonth();

    while ($monthCursor->lte($lastMonth)) {
        $year = $monthCursor->year;
        $month = $monthCursor->month;

        $monthSamplesCollection = $samples
            ->filter(function ($sample) use ($year, $month) {
                if (!$sample->taken_at) {
                    return false;
                }

                $date = Carbon::parse(
                    $sample->taken_at
                );

                return $date->year === $year
                    && $date->month === $month;
            });

        $monthlyLabels[] =
            $monthCursor->translatedFormat('M Y');

        $monthlySamples[] =
            $monthSamplesCollection->count();

        $monthlyAnalyses[] =
            $monthSamplesCollection->sum(
                function ($sample) {
                    return $sample->parameters->count();
                }
            );

        $monthlyReported[] =
            $monthSamplesCollection
                ->where('status', 'reported')
                ->count();

        $monthDurations = $monthSamplesCollection
            ->filter(function ($sample) {
                return $sample->taken_at
                    && $sample->report_date;
            })
            ->map(function ($sample) {
                return Carbon::parse(
                    $sample->taken_at
                )
                    ->startOfDay()
                    ->diffInDays(
                        Carbon::parse(
                            $sample->report_date
                        )->startOfDay()
                    );
            });

        $monthlyAverageDuration[] =
            $monthDurations->count() > 0
                ? round(
                    $monthDurations->avg(),
                    1
                )
                : 0;

        $monthCursor->addMonth();
    }

    $dailyLabels = [];
    $dailySamples = [];

    $dayCursor = $dailyStart->copy();

    while ($dayCursor->lte($dailyEnd)) {
        $dateKey = $dayCursor->format('Y-m-d');

        $dailyLabels[] = $dayCursor->format('d.m');

        $dailySamples[] = $dailySampleCollection
            ->filter(function ($sample) use ($dateKey) {
                if (!$sample->taken_at) {
                    return false;
                }

                return Carbon::parse(
                    $sample->taken_at
                )->format('Y-m-d') === $dateKey;
            })
            ->count();

        $dayCursor->addDay();
    }

    $statusDistribution = [
        'Bekliyor' => $pendingSamples,
        'Kabul Edildi' => $acceptedSamples,
        'Analizde' => $inAnalysisSamples,
        'Raporlandı' => $reportedSamples,
        'Reddedildi' => $rejectedSamples,
    ];

    $topProducts = $samples
        ->filter(function ($sample) {
            return $sample->labProduct !== null;
        })
        ->groupBy('product_id')
        ->map(function ($group) {
            return [
                'name' =>
                    $group->first()
                        ->labProduct
                        ->product_name ?? '-',

                'count' =>
                    $group->count(),
            ];
        })
        ->sortByDesc('count')
        ->take(10)
        ->values();

    $parameterStatistics = [];

    foreach ($samples as $sample) {
        foreach ($sample->parameters as $parameter) {
            $id = $parameter->id;

            if (!isset($parameterStatistics[$id])) {
                $parameterStatistics[$id] = [
                    'id' => $id,
                    'name' => $parameter->parameter ?? '-',
                    'total' => 0,
                    'completed' => 0,
                ];
            }

            $parameterStatistics[$id]['total']++;

            if (
                $parameter->pivot->result !== null
                && $parameter->pivot->result !== ''
            ) {
                $parameterStatistics[$id]['completed']++;
            }
        }
    }

    $parameterStatistics = collect(
        $parameterStatistics
    )
        ->map(function ($parameter) {
            $parameter['pending'] = max(
                $parameter['total']
                - $parameter['completed'],
                0
            );

            $parameter['completion_rate'] =
                $parameter['total'] > 0
                    ? round(
                        (
                            $parameter['completed']
                            / $parameter['total']
                        ) * 100,
                        1
                    )
                    : 0;

            return $parameter;
        })
        ->sortByDesc('total')
        ->values();

    $topParameters = $parameterStatistics
        ->take(10);

    $topCompanies = $samples
        ->filter(function ($sample) {
            return !empty(
                $sample->company_name
            );
        })
        ->groupBy('company_name')
        ->map(function ($group, $companyName) {
            return [
                'name' => $companyName,
                'count' => $group->count(),
            ];
        })
        ->sortByDesc('count')
        ->take(10)
        ->values();

    $productPerformance = $samples
        ->filter(function ($sample) {
            return $sample->labProduct !== null;
        })
        ->groupBy('product_id')
        ->map(function ($group) {
            $first = $group->first();

            $analysisCount = $group->sum(
                function ($sample) {
                    return $sample
                        ->parameters
                        ->count();
                }
            );

            $completedCount = $group->sum(
                function ($sample) {
                    return $sample
                        ->parameters
                        ->filter(
                            function ($parameter) {
                                return
                                    $parameter
                                        ->pivot
                                        ->result !== null
                                    &&
                                    $parameter
                                        ->pivot
                                        ->result !== '';
                            }
                        )
                        ->count();
                }
            );

            $durations = $group
                ->filter(function ($sample) {
                    return
                        $sample->taken_at
                        &&
                        $sample->report_date;
                })
                ->map(function ($sample) {
                    return Carbon::parse(
                        $sample->taken_at
                    )
                        ->startOfDay()
                        ->diffInDays(
                            Carbon::parse(
                                $sample->report_date
                            )->startOfDay()
                        );
                });

            return [
                'product_name' =>
                    $first
                        ->labProduct
                        ->product_name ?? '-',

                'sample_count' =>
                    $group->count(),

                'analysis_count' =>
                    $analysisCount,

                'completed_count' =>
                    $completedCount,

                'pending_count' =>
                    max(
                        $analysisCount - $completedCount,
                        0
                    ),

                'reported_count' =>
                    $group
                        ->where(
                            'status',
                            'reported'
                        )
                        ->count(),

                'average_days' =>
                    $durations->count() > 0
                        ? round(
                            $durations->avg(),
                            1
                        )
                        : 0,
            ];
        })
        ->sortByDesc('sample_count')
        ->values();

    $products = \App\Models\LabProduct::select(
            'id',
            'product_name'
        )
        ->orderBy('product_name')
        ->get();

    $companies = SampleAcceptance::select(
            'company_name'
        )
        ->whereNotNull(
            'company_name'
        )
        ->where(
            'company_name',
            '!=',
            ''
        )
        ->distinct()
        ->orderBy(
            'company_name'
        )
        ->pluck(
            'company_name'
        );

    $sampleTypes = SampleAcceptance::select(
            'sample_type'
        )
        ->whereNotNull(
            'sample_type'
        )
        ->where(
            'sample_type',
            '!=',
            ''
        )
        ->distinct()
        ->orderBy(
            'sample_type'
        )
        ->pluck(
            'sample_type'
        );

    return view(
        'sample_acceptances.statistics',
        compact(
            'startDate',
            'endDate',
            'dailyMonth',
            'dailyStart',
            'dailyEnd',
            'products',
            'companies',
            'sampleTypes',
            'totalSamples',
            'totalAnalyses',
            'completedAnalyses',
            'pendingAnalyses',
            'analysisCompletionRate',
            'reportedSamples',
            'pendingSamples',
            'acceptedSamples',
            'inAnalysisSamples',
            'rejectedSamples',
            'activeCompanies',
            'averageReportDays',
            'fastestReportDays',
            'slowestReportDays',
            'monthlyLabels',
            'monthlySamples',
            'monthlyAnalyses',
            'monthlyReported',
            'monthlyAverageDuration',
            'dailyLabels',
            'dailySamples',
            'statusDistribution',
            'topProducts',
            'topParameters',
            'parameterStatistics',
            'topCompanies',
            'productPerformance'
        )
    );
}


}