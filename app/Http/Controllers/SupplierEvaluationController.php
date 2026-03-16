<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierEvaluation;
use Illuminate\Http\Request;

class SupplierEvaluationController extends Controller
{
    public function create(Supplier $supplier)
    {
        return view('satinalma.tedarikci_evaluate', compact('supplier'));
    }

    public function store(Request $request, Supplier $supplier)
    {
        $score = 0;

        // 1. Sertifikasyon
        if($request->certification == 'Var') $score += 25;
        elseif($request->certification == 'Aşamada') $score += 5;

        // 2. İzlenebilirlik
        if($request->traceability == 'Evet') $score += 25;

        // 3. Şikayet dönüş hızı
        if($request->complaint_response == 'Üç gün altında') $score += 20;

        // 4. Fiyat/Ödeme politikası
        if($request->pricing_policy == 'Evet') $score += 15;

        // 5. Teknik destek
        if($request->technical_support == 'Evet') $score += 15;

        // Durum belirleme
        if($score >= 80) {
            $status = 'Uzun dönem çalışılır (Listeye eklenir)';
        } elseif($score >= 60) {
            $status = 'Üç yıl içinde iyileştirme istenir (Listeye eklenir)';
        } else {
            $status = 'Çalışılmaz (İyileştirme istenir)';
        }

        SupplierEvaluation::create([
            'supplier_id' => $supplier->id,
            'user_id' => auth()->id(),
            'evaluation_date' => now(),
            'title' => auth()->user()->title ?? null,
            'certification' => $request->certification,
            'certification_detail' => $request->certification_detail,
            'traceability' => $request->traceability,
            'complaint_response' => $request->complaint_response,
            'pricing_policy' => $request->pricing_policy,
            'technical_support' => $request->technical_support,
            'score' => $score,
            'status' => $status,
        ]);

        return redirect()->route('suppliers.index')->with('success',"Değerlendirme kaydedildi. Puan: $score, Durum: $status");
    }
}