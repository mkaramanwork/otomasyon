<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use App\Models\OrientationPlan;
use App\Models\OrientationActivity;
use App\Models\Competency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PersonnelController extends Controller
{
    // 📋 Personel Listeleme
    public function index()
    {
        $personnel = Personnel::with('orientationPlans.activities','competencies')->get();
        return view('personnel.index', compact('personnel'));
    }

    // ➕ Personel Ekleme Formu
    public function create()
    {
        return view('personnel.create');
    }

    // ➕ Personel Kaydet
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'=>'required',
            'title'=>'required',
            'duty'=>'required',
            'start_date'=>'required|date',
            'deputy'=>'nullable'
        ]);

        $personnel = Personnel::create($validated);

        // ✅ Mail gönder
        Mail::send('personnel.mail', [
            'type'=>'new',
            'personnel'=>$personnel
        ], function($message) use ($personnel) {
            $message->to('laborauvar@aripet.com')
                    ->cc(['kalite@firma.com'])
                    ->subject("Yeni Personel - {$personnel->name}");
        });

        return redirect()->route('personnel.index')->with('success','Personel eklendi ve mail gönderildi ✅');
    }

    // 📄 Personel Detay
    public function show(Personnel $personnel)
    {
        $personnel->load('orientationPlans.activities','competencies');
        return view('personnel.show', compact('personnel'));
    }

    // ✏️ Personel Güncelleme Formu
    public function edit(Personnel $personnel)
    {
        return view('personnel.edit', compact('personnel'));
    }

    // ✏️ Personel Güncelle
    public function update(Request $request, Personnel $personnel)
    {
        $validated = $request->validate([
            'name'=>'required',
            'title'=>'required',
            'duty'=>'required',
            'start_date'=>'required|date',
            'deputy'=>'nullable'
        ]);

        $personnel->update($validated);

        Mail::send('personnel.mail', [
            'type'=>'update',
            'personnel'=>$personnel
        ], function($message) use ($personnel) {
            $message->to('laborauvar@aripet.com')
                    ->subject("Personel Güncellendi - {$personnel->name}");
        });

        return redirect()->route('personnel.index')->with('success','Personel güncellendi ve mail gönderildi ✅');
    }

    // ➕ Oryantasyon Planı
    public function storeOrientationPlan(Request $request, Personnel $personnel)
    {
        $validated = $request->validate([
            'department'=>'required',
            'purpose'=>'required'
        ]);

        $plan = $personnel->orientationPlans()->create($validated);

        Mail::send('personnel.mail', [
            'type'=>'orientation',
            'personnel'=>$personnel,
            'plan'=>$plan
        ], function($message) use ($personnel) {
            $message->to('laborauvar@aripet.com')
                    ->subject("Oryantasyon Planı - {$personnel->name}");
        });

        return back()->with('success','Oryantasyon planı eklendi ve mail gönderildi ✅');
    }

    // ➕ Faaliyet Ekle
    public function storeActivity(Request $request, OrientationPlan $plan)
    {
        $validated = $request->validate([
            'activity'=>'required',
            'trainer'=>'required',
            'start_date'=>'required|date',
            'end_date'=>'required|date',
            'result'=>'nullable'
        ]);

        $activity = $plan->activities()->create($validated);

        Mail::send('personnel.mail', [
            'type'=>'activity',
            'plan'=>$plan,
            'activity'=>$activity
        ], function($message) use ($plan) {
            $message->to('laborauvar@aripet.com')
                    ->subject("Oryantasyon Faaliyeti - {$plan->personnel->name}");
        });

        return back()->with('success','Faaliyet eklendi ve mail gönderildi ✅');
    }

    // ✔️ Oryantasyon Tamamlama
    public function completeOrientation(Request $request, OrientationPlan $plan)
    {
        $validated = $request->validate([
            'evaluation_date'=>'required|date',
            'approval_date'=>'required|date'
        ]);

        $plan->update([
            'evaluation_date'=>$validated['evaluation_date'],
            'approval_date'=>$validated['approval_date'],
            'is_completed'=>true
        ]);

        Mail::send('personnel.mail', [
            'type'=>'complete',
            'plan'=>$plan
        ], function($message) use ($plan) {
            $message->to('laborauvar@aripet.com')
                    ->subject("Oryantasyon Tamamlandı - {$plan->personnel->name}");
        });

        return back()->with('success','Oryantasyon tamamlandı ve mail gönderildi ✅');
    }

    // ➕ Yetkinlik Atama
    public function assignCompetency(Request $request, Personnel $personnel)
    {
        $validated = $request->validate([
            'competency_id'=>'required|exists:competencies,id',
            'granted_at'=>'required|date',
            'level'=>'nullable'
        ]);

        $personnel->competencies()->attach($validated['competency_id'], [
            'granted_at'=>$validated['granted_at'],
            'level'=>$validated['level']
        ]);

        Mail::send('personnel.mail', [
            'type'=>'competency',
            'personnel'=>$personnel
        ], function($message) use ($personnel) {
            $message->to('laborauvar@aripet.com')
                    ->subject("Yetkinlik Ataması - {$personnel->name}");
        });

        return back()->with('success','Yetkinlik atandı ve mail gönderildi ✅');
    }
}