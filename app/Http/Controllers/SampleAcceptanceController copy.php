<?php

namespace App\Http\Controllers;

use App\Models\SampleAcceptance;
use Illuminate\Http\Request;
use App\Models\LabParameter;
use App\Models\BusinessLog;

class SampleAcceptanceController extends Controller
{
    /**
     * Bekleyen numuneleri listele (status = pending)
     */
    public function index()
    {
        $samples = SampleAcceptance::where('status', 'pending')
                    ->with('product')
                    ->get();

        return view('sample_acceptances.index', compact('samples'));
    }

    /**
     * Yeni numune kaydı oluştur (Üretim tamamlandı tetiklemesi)
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

    // ✅ Log kaydı
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

    // ✅ Log kaydı
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

    // ✅ Log kaydı
    BusinessLog::create([
        'action' => 'Numune Red',
        'description' => "Batch No {$sample->batch_no} numunesi reddedildi.",
        'user_id' => auth()->id(),
        'sample_acceptance_id' => $sample->id,
    ]);

    return redirect()->route('samples.index')->with('error', 'Numune reddedildi.');
}

public function acceptForm(SampleAcceptance $sample)
{
    $parameters = LabParameter::where('sample_type', $sample->sample_type)->get();
    return view('sample_acceptances.accept_form', compact('sample','parameters'));
}

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
        'parameters'     => 'required|array', // seçilen parametreler
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

    // Pivot tabloya parametreleri kaydet
    $sample->parameters()->sync($validated['parameters']);

    return redirect()->route('samples.analysisPending')
                     ->with('success','Numune kabul edildi ve Analiz Bekleyenlere gönderildi.');
}

//public function analysisPending()
//{
    //$samples = \App\Models\SampleAcceptance::where('status','accepted')
      //          ->with(['product','parameters']) // ✅ parametreleri yükle
        //        ->get();

  //  return view('sample_acceptances.analysis_pending', compact('samples'));
//}//


public function storeResults(Request $request, SampleAcceptance $sample)
    {
        // ✅ Validasyon
        $validated = $request->validate([
            'results' => 'required|array',
            'results.*.value' => 'required|string',
            'results.*.date' => 'required|date',
            'results.*.description' => 'nullable|string',
        ]);

        // ✅ Pivot tabloya kaydet
        foreach ($validated['results'] as $paramId => $data) {
            $sample->parameters()->updateExistingPivot($paramId, [
                'result' => $data['value'],
                'result_date' => $data['date'],
                'description' => $data['description'],
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('samples.analysisPending')
                         ->with('success','Sonuçlar başarıyla kaydedildi.');
    }

    public function editResults(SampleAcceptance $sample)
        {
            $sample->load('parameters');
            return view('sample_acceptances.results_edit', compact('sample'));
        }

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

            return back()->with('success','Parametre sonucu kaydedildi.');
        }
        
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



}