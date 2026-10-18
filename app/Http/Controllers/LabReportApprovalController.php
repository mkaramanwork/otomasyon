<?php
namespace App\Http\Controllers;

use App\Models\SampleAcceptance;
use App\Models\BusinessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class LabReportApprovalController extends Controller
{
    public static function ready(SampleAcceptance $sample): bool
    {
        $sample->loadMissing('parameters');
        return (bool) $sample->report_date && $sample->parameters->isNotEmpty()
            && $sample->parameters->every(fn ($p) => filled($p->pivot->result) && filled($p->pivot->result_date))
            && Carbon::parse($sample->report_date)->toDateString() >= max(
                Carbon::parse($sample->taken_at ?: $sample->created_at)->toDateString(),
                $sample->parameters->max(fn ($p) => Carbon::parse($p->pivot->result_date)->toDateString())
            );
    }

    /** Ilk analiz kaydini yapan kullanicilari sayar; revizyonlari puana katmaz.
     *  Esitlikte veya eski/eksik loglarda personel otomatik secilmez.
     */
    public static function primaryAnalyst(SampleAcceptance $sample): ?\App\Models\User
    {
        $sample->loadMissing('parameters');
        $names = $sample->parameters->pluck('parameter')->filter()->unique()->values();
        $logs = BusinessLog::where('sample_acceptance_id', $sample->id)
            ->where('action', 'Analiz Sonucu Girişi')->whereNotNull('user_id')
            ->orderBy('created_at')->orderBy('id')->get();
        $credited = [];
        foreach ($logs as $log) {
            foreach ($names as $name) {
                // Log formati: "Batch No ... - PARAMETRE analizi, ..."
                if (str_contains((string)$log->description, ' - '.$name.' analizi,')) {
                    $key = mb_strtolower(trim($name));
                    if (!isset($credited[$key])) $credited[$key] = (int)$log->user_id;
                    break;
                }
            }
        }
        // Tam bir analist atamasi icin her parametrenin ilk kaydi izlenebilir olmali.
        if (count($credited) !== $names->count() || $names->isEmpty()) return null;
        $counts = array_count_values(array_values($credited));
        arsort($counts);
        $max = max($counts);
        $winners = array_keys(array_filter($counts, fn($n) => $n === $max));
        return count($winners) === 1 ? \App\Models\User::find($winners[0]) : null;
    }

    public static function fingerprint(SampleAcceptance $sample, object $settings): string
    {
        $sample->loadMissing('parameters');
        $parameters = $sample->parameters->sortBy('id')->map(fn ($p) => [
            $p->id,$p->parameter,$p->method_tr,$p->scope,$p->unit,
            $p->pivot->result,$p->pivot->result_date,
        ])->values()->all();
        return hash('sha256', json_encode([
            'sample' => [$sample->id,$sample->batch_no,$sample->company_name,$sample->report_no,
                (string)$sample->report_date,$sample->report_description,$sample->taken_at,$sample->amount,$sample->customer_address ?? null, $sample->customer_id ?? null],
            'parameters'=>$parameters,
            'common_template'=>\App\Http\Controllers\LabReportTemplateController::load(),
            'preparer_user_id' => self::primaryAnalyst($sample)?->id,
            'preparer_signature_revision' => DB::table('lab_report_signatures')->where('user_id', self::primaryAnalyst($sample)?->id)->value('updated_at'),
            'manager_signature_revision' => DB::table('lab_report_signatures')->where('user_id', $settings->laboratory_manager_user_id)->value('updated_at'),
            'settings'=>[$settings->laboratory_name,$settings->address,$settings->accreditation_number,
                $settings->accreditation_mark_enabled,$settings->logo_path,$settings->mark_path,
                $settings->laboratory_manager_user_id,$settings->document_code,
                $settings->stamp_path,$settings->logo_width_mm,$settings->mark_width_mm,
                $settings->stamp_width_mm,$settings->signature_width_mm,
                $settings->logo_height_mm ?? null,$settings->mark_height_mm ?? null,
                $settings->stamp_height_mm ?? null,$settings->signature_height_mm ?? null],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }


    /** Ekrandaki mevcut Altinda / Uzerinde / Uygun Degil / Kontrol Ediniz kontrolu. */
    public static function limitIssues(SampleAcceptance $sample): array
    {
        $sample->load(['parameters', 'labProduct.parameters']);
        $issues = [];
        foreach ($sample->parameters as $param) {
            $limit = $sample->labProduct?->parameters?->firstWhere('parameter_id', $param->id);
            $result = trim((string) ($param->pivot->result ?? ''));
            $min = trim((string) ($limit->min_value ?? ''));
            $max = trim((string) ($limit->max_value ?? ''));
            $net = trim((string) ($limit->net_value ?? ''));
            $min = $min === '-' ? '' : $min;
            $max = $max === '-' ? '' : $max;
            $net = $net === '-' ? '' : $net;
            if ($result === '' || $result === '-') continue; // eksik analiz zaten ayri engelleniyor
            $status = null;
            if ($min !== '' && $max !== '') {
                if (!is_numeric(str_replace(',', '.', $result)) ||
                    !is_numeric(str_replace(',', '.', $min)) ||
                    !is_numeric(str_replace(',', '.', $max))) {
                    $status = 'Kontrol Ediniz';
                } elseif ((float) str_replace(',', '.', $result) < (float) str_replace(',', '.', $min)) {
                    $status = 'Altinda';
                } elseif ((float) str_replace(',', '.', $result) > (float) str_replace(',', '.', $max)) {
                    $status = 'Uzerinde';
                }
            } elseif ($net !== '') {
                if (is_numeric(str_replace(',', '.', $result)) && is_numeric(str_replace(',', '.', $net))) {
                    if ((float) str_replace(',', '.', $result) != (float) str_replace(',', '.', $net)) $status = 'Uygun Degil';
                } elseif ($result !== $net) {
                    $status = 'Uygun Degil';
                }
            } else {
                $status = 'Kontrol Ediniz';
            }
            // Kontrol Ediniz: limit yok veya karsilastirma yapilamiyor; tek basina mudur onayi gerektirmez.
            // Gercek limit asimi ya da tanimli net deger uyusmazligi engel olusturur.
            if ($status !== null && $status !== 'Kontrol Ediniz') {
                $issues[] = ($param->parameter ?? ('#'.$param->id)) . ': ' . $status;
            }
        }
        return $issues;
    }

    /** Sonuc ve urun limitlerinin degismesi onayi otomatik gecersiz kilar. */
    public static function limitFingerprint(SampleAcceptance $sample): string
    {
        $sample->load(['parameters', 'labProduct.parameters']);
        $items = $sample->parameters->sortBy('id')->map(function ($p) use ($sample) {
            $limit = $sample->labProduct?->parameters?->firstWhere('parameter_id', $p->id);
            return [$p->id, $p->pivot->result, $p->pivot->result_date,
                $limit->min_value ?? null, $limit->max_value ?? null, $limit->net_value ?? null];
        })->values()->all();
        return hash('sha256', json_encode([$sample->id, $sample->product_id, $items], JSON_UNESCAPED_UNICODE));
    }

    public static function limitApproved(SampleAcceptance $sample): bool
    {
        if (!self::limitIssues($sample)) return true;
        $settings = DB::table('lab_report_settings')->first();
        if (!$settings || !$settings->laboratory_manager_user_id) return false;
        $hash = self::limitFingerprint($sample);
        $log = BusinessLog::where('sample_acceptance_id', $sample->id)
            ->where('action', 'Limit Disi Rapor Izni')->orderByDesc('id')->first();
        return $log && (int)$log->user_id === (int)$settings->laboratory_manager_user_id
            && str_contains((string)$log->description, 'SHA256: ' . $hash);
    }

    public function approveLimit(SampleAcceptance $sample, Request $request)
    {
        Gate::authorize('approve-lab-reports');
        $settings = DB::table('lab_report_settings')->first();
        if (!$settings || (int)$settings->laboratory_manager_user_id !== (int)auth()->id()) abort(403);
        $sample->load(['parameters', 'labProduct.parameters']);
        if (!$sample->parameters->count() || $sample->parameters->contains(fn($p) =>
            trim((string)($p->pivot->result ?? '')) === '' || !$p->pivot->result_date)) {
            throw ValidationException::withMessages(['report'=>'Analizler tamamlanmadan onay verilemez.']);
        }
        $issues = self::limitIssues($sample);
        if (!$issues) return back()->with('success', 'Uygunsuzluk bulunmuyor. Onay gerekmiyor.');
        $request->validate(['confirm' => 'accepted', 'reason' => 'required|string|min:5|max:1000']);
        $hash = self::limitFingerprint($sample);
        BusinessLog::create([
            'action' => 'Limit Disi Rapor Izni',
            'description' => "Laboratuvar muduru tarafindan uygunsuz sonuc ile raporlama izni verildi."
                . "\nGerekce: " . trim($request->input('reason'))
                . "\nUygunsuzluklar: " . implode('; ', $issues)
                . "\nSHA256: " . $hash,
            'user_id' => auth()->id(), 'sample_acceptance_id' => $sample->id,
        ]);
        return back()->with('success', 'Laboratuvar muduru izni kaydedildi. Rapor yazilabilir.');
    }

    public function approve(SampleAcceptance $sample, Request $request)
    {
        Gate::authorize('approve-lab-reports');
        $settings = DB::table('lab_report_settings')->first();
        if (!$settings || (int)$settings->laboratory_manager_user_id !== (int)auth()->id()) {
            abort(403, 'Bu raporu yalnızca tanımlı laboratuvar yöneticisi onaylayabilir.');
        }
        if (!self::ready($sample)) {
            throw ValidationException::withMessages(['report'=>'Rapor tarihi veya analizler eksik/geçersiz.']);
        }
        if (!DB::table('lab_report_signatures')->where('user_id', auth()->id())->exists()) {
            throw ValidationException::withMessages(['report'=>'Onaylayanın kendi PNG imza görselini yüklemesi gerekir.']);
        }
        if (!self::primaryAnalyst($sample)) {
            throw ValidationException::withMessages(['report'=>'Analiz giris loglarinda tek bir sorumlu personel belirlenemedi. Eksik log/esitlik durumunu kalite yetkilisi incelemelidir.']);
        }
        if (!DB::table('lab_report_signatures')->where('user_id', self::primaryAnalyst($sample)->id)->exists()) {
            throw ValidationException::withMessages(['report'=>'Analizi en cok giren personelin kendi PNG imzasi bulunmuyor.']);
        }
        $request->validate(['confirm'=>'accepted']);
        $hash = self::fingerprint($sample, $settings);
        DB::transaction(function () use ($sample,$hash) {
            DB::table('lab_report_approvals')->updateOrInsert(
                ['sample_acceptance_id'=>$sample->id],
                ['approved_by'=>auth()->id(),'content_hash'=>$hash,'approved_at'=>now(),
                 'updated_at'=>now()]
            );
            BusinessLog::create([
                'action'=>'Deney Raporu Onayı',
                'description'=>"Batch No {$sample->batch_no}: rapor içeriği kimlik doğrulanmış kullanıcı tarafından onaylandı. SHA256: {$hash}",
                'user_id'=>auth()->id(),'sample_acceptance_id'=>$sample->id,
            ]);
        });
        return back()->with('success','Rapor içeriği onaylandı. PNG imza görseli sadece geçerli onaylı önizleme/PDF üzerinde görünür.');
    }
}
