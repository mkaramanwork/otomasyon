<?php
namespace App\Http\Controllers;

use App\Models\Equipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class EquipmentController extends Controller
{
    // 📋 Listeleme
    public function index()
    {
        $equipments = Equipment::with('calibrations')->get()->map(function($eq){
            $lastCalibration = $eq->calibrations->sortByDesc('performed_at')->first();

            if ($lastCalibration && $lastCalibration->valid_until) {
                $daysLeft = intval(Carbon::now()->diffInDays(Carbon::parse($lastCalibration->valid_until), false));
                $eq->days_left = $daysLeft;
                $eq->calibration_valid_until = $lastCalibration->valid_until;
            } else {
                $eq->days_left = null;
                $eq->calibration_valid_until = null;
            }

            return $eq;
        });

        $criticalEquipments = $equipments->filter(fn($eq) => $eq->days_left !== null && $eq->days_left <= 30);

        // ✅ Kritik cihazlar için mail gönder
        foreach ($criticalEquipments as $critical) {
            Mail::send('equipment.mail_alert', ['equipment'=>$critical], function($message) use ($critical) {
                $message->to('laboratuvar@aripet.com')
                        ->cc(['kalibrasyon@firma.com'])
                        ->subject("⚠️ Kritik Uyarı - {$critical->name} (Kalibrasyon süresi {$critical->days_left} gün kaldı)");
            });
        }

        return view('equipment.index', compact('equipments','criticalEquipments'));
    }
    // 📄 Detay
    public function show(Equipment $equipment)
    {
        $equipment->load(['calibrations','maintenances','iqcRecords','eqcRecords']);
        return view('equipment.show', compact('equipment'));
    }

    // ➕ Kalibrasyon Kaydı (PDF ile)
    public function storeCalibration(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'certificate_no'=>'required',
            'provider'=>'required',
            'performed_at'=>'required|date',
            'valid_until'=>'required|date',
            'result'=>'nullable',
            'certificate_file'=>'nullable|mimes:pdf|max:2048'
        ]);

        if ($request->hasFile('certificate_file')) {
            $path = $request->file('certificate_file')->store('certificates','public');
            $validated['certificate_file'] = $path;
        }

        $calibration = $equipment->calibrations()->create($validated);

        // ✅ Mail gönder
        Mail::send('equipment.mail_calibration', ['equipment'=>$equipment,'calibration'=>$calibration], function($message) use ($equipment,$calibration) {
            $message->to('laboratuvar@aripet.com')
                    ->cc(['yonetici@firma.com'])
                    ->subject("Yeni Kalibrasyon - {$equipment->name} ({$calibration->certificate_no})");
        });

        return back()->with('success','Kalibrasyon kaydı eklendi ve mail gönderildi ✅');
    }

    // ➕ Bakım Kaydı
    public function storeMaintenance(Request $request, Equipment $equipment)
    {
        $maintenance = $equipment->maintenances()->create($request->validate([
            'type'=>'required',
            'performed_at'=>'required|date',
            'checklist'=>'nullable',
            'result'=>'nullable'
        ]));

        // ✅ Mail gönder
        Mail::send('equipment.mail_maintenance', ['equipment'=>$equipment,'maintenance'=>$maintenance], function($message) use ($equipment,$maintenance) {
            $message->to('laboratuvar@aripet.com')
                    ->cc(['yonetici@firma.com'])
                    ->subject("Yeni Bakım Kaydı - {$equipment->name}");
        });

        return back()->with('success','Bakım kaydı eklendi ve mail gönderildi ✅');
    }

    // ➕ IQC Kaydı
    public function storeIqc(Request $request, Equipment $equipment)
    {
        $iqc = $equipment->iqcRecords()->create($request->validate([
            'date'=>'required|date',
            'material'=>'required',
            'value'=>'required',
            'rule_status'=>'required',
            'note'=>'nullable'
        ]));

        // ✅ Mail gönder
        Mail::send('equipment.mail_iqc', ['equipment'=>$equipment,'iqc'=>$iqc], function($message) use ($equipment,$iqc) {
            $message->to('laboratuvar@aripet.com')
                    ->subject("Yeni IQC Kaydı - {$equipment->name}");
        });

        return back()->with('success','IQC kaydı eklendi ve mail gönderildi ✅');
    }

    // ➕ EQC Kaydı
    public function storeEqc(Request $request, Equipment $equipment)
    {
        $eqc = $equipment->eqcRecords()->create($request->validate([
            'provider'=>'required',
            'round_no'=>'required',
            'result'=>'required',
            'z_score'=>'nullable',
            'action_plan'=>'nullable'
        ]));

        // ✅ Mail gönder
        Mail::send('equipment.mail_eqc', ['equipment'=>$equipment,'eqc'=>$eqc], function($message) use ($equipment,$eqc) {
            $message->to('laboratuvar@aripet.com')
                    ->subject("Yeni EQC Kaydı - {$equipment->name}");
        });

        return back()->with('success','EQC kaydı eklendi ve mail gönderildi ✅');
    }

    // ➕ Yeni Cihaz
    public function create()
    {
        return view('equipment.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tag' => 'required|unique:equipment',
            'name' => 'required',
            'model' => 'nullable',
            'serial_no' => 'nullable',
            'purchase_date' => 'nullable|date',
            'calibration_due_at' => 'nullable|date',
            'status' => 'required'
        ]);

        $equipment = Equipment::create($validated);

        // ✅ Mail gönder
        Mail::send('equipment.mail_new', ['equipment'=>$equipment], function($message) use ($equipment) {
            $message->to('laboratuvar@aripet.com')
                    ->subject("Yeni Cihaz Eklendi - {$equipment->name}");
        });

        return redirect()->route('equipment.index')->with('success','Cihaz başarıyla eklendi ve mail gönderildi ✅');
    }
    // ✏️ Cihaz Güncelleme Formu
    public function edit(Equipment $equipment)
    {
        return view('equipment.edit', compact('equipment'));
    }

    // ✏️ Cihaz Güncelle
    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'tag' => 'required|unique:equipment,tag,'.$equipment->id,
            'name' => 'required',
            'model' => 'nullable',
            'serial_no' => 'nullable',
            'purchase_date' => 'nullable|date',
            'calibration_due_at' => 'nullable|date',
            'status' => 'required'
        ]);

        $equipment->update($validated);

        // ✅ Mail gönder
        Mail::send('equipment.mail_update', ['equipment'=>$equipment], function($message) use ($equipment) {
            $message->to('laboratuvar@aripet.com')
                    ->cc(['bakim@firma.com','kalibrasyon@firma.com'])
                    ->subject("Cihaz Güncellendi - {$equipment->name} ({$equipment->tag})");
        });

        return redirect()->route('equipment.index')->with('success','Cihaz güncellendi ve mail gönderildi ✅');
    }

    // ❌ Cihaz Sil
    public function destroy(Equipment $equipment)
    {
        $equipment->delete();

        // ✅ Mail gönder
        \Mail::send('equipment.mail_destroy', ['equipment'=>$equipment], function($message) use ($equipment) {
            $message->to('laboratuvar@aripet.com')
                    ->cc(['bakim@firma.com','kalibrasyon@firma.com'])
                    ->subject("Cihaz Silindi - {$equipment->name} ({$equipment->tag})");
        });

        return redirect()->route('equipment.index')->with('success','Cihaz başarıyla silindi ve mail gönderildi ✅');
    }

}   