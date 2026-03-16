<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TetkikPlan;
use App\Models\TetkikSoru;
use App\Models\TetkikSoruListesi;
use Illuminate\Support\Facades\Mail;
use App\Mail\TetkikMail;
use PhpOffice\PhpWord\TemplateProcessor;


class TetkikController extends Controller
{
    // Planlanan tetkikleri listele
    public function index()
    {
        $planlar = TetkikPlan::orderByDesc('id')->get();
        return view('tetkik.index', compact('planlar'));
    }

    // Yeni plan oluşturma formu
    public function create()
    {
        $soruListesi = TetkikSoruListesi::all();
        return view('tetkik.create', compact('soruListesi'));
    }

    // Plan kaydetme (PLANLANDI)
    public function store(Request $request)
    {
        $plan = TetkikPlan::create($request->only([
            'tetkik_no','tetkik_tarih','denetci','bolum','amac','kapsam'
        ]));

        // Seçilen soruları tetkik_sorulari tablosuna kopyala
        if ($request->has('sorular')) {
            $soruListesi = TetkikSoruListesi::whereIn('id',$request->sorular)->get();
            foreach ($soruListesi as $soru) {
                TetkikSoru::create([
                    'tetkik_plan_id' => $plan->id,
                    'standart_maddesi' => $soru->standart_maddesi,
                    'soru' => $soru->soru,
                ]);
            }
        }

        // 📧 Planlama bildirimi
        Mail::to(['laboratuvar@aripet.com','mrt.krmn.55@gmail.com'])
            ->send(new TetkikMail($plan,'tetkik_planlandi'));

        return redirect()->route('tetkik.index')->with('success','Tetkik planı oluşturuldu.');
    }

    // Plan detayını göster
    public function show($id)
    {
        $plan = TetkikPlan::with('sorular')->findOrFail($id);
        return view('tetkik.show', compact('plan'));
    }

    // Tetkik başlat (BAŞLATILDI)
    public function start($id)
    {
        $plan = TetkikPlan::with('sorular')->findOrFail($id);

        // 📧 Başlatma bildirimi
        Mail::to(['laboratuvar@aripet.com','mrt.krmn.55@gmail.com'])
            ->send(new TetkikMail($plan,'tetkik_baslatildi'));

        return view('tetkik.start', compact('plan'));
    }

    // Tetkik kayıtlarını kaydet (TAMAMLANDI)
    public function save(Request $request, $id)
    {
        $plan = TetkikPlan::with('sorular')->findOrFail($id);

        foreach ($plan->sorular as $soru) {
            $soru->update([
                'delil' => $request->delil[$soru->id] ?? null,
                'uygunsuzluk_var' => $request->uygunsuzluk_var[$soru->id] ?? 0,
                'uygunsuzluk_aciklama' => $request->uygunsuzluk_aciklama[$soru->id] ?? null,
            ]);
        }

        $plan->update(['durum' => 'Tamamlandı']);

        // 📧 Tamamlama bildirimi
        Mail::to(['laboratuvar@aripet.com','mrt.krmn.55@gmail.com'])
            ->send(new TetkikMail($plan,'tetkik_tamamlandi'));

        return redirect()->route('tetkik.index')->with('success','Tetkik kaydedildi.');
    }

    public function rapor($id)
    {
        $plan = TetkikPlan::with('sorular')->findOrFail($id);

        $template = new TemplateProcessor(storage_path('app/templates/tetkik_taslak.docx'));

        // Tekil alanlar
        $template->setValue('tetkik_no', $plan->tetkik_no);
        $template->setValue('tetkik_tarih', $plan->tetkik_tarih);
        $template->setValue('denetci', $plan->denetci);
        $template->setValue('bolum', $plan->bolum);
        $template->setValue('durum', $plan->durum);
        $template->setValue('amac', $plan->amac);
        $template->setValue('kapsam', $plan->kapsam);

        // Sorular için cloneRow
        $template->cloneRow('soru_standart', count($plan->sorular));

        foreach ($plan->sorular as $i => $soru) {
            $index = $i + 1;
            $template->setValue("index#{$index}", $index);
            $template->setValue("soru_standart#{$index}", $soru->standart_maddesi);
            $template->setValue("soru_metin#{$index}", $soru->soru);
            $template->setValue("soru_delil#{$index}", $soru->delil ?? '-');
            $template->setValue("soru_durum#{$index}", $soru->uygunsuzluk_var == 0 ? 'Yok' : ($soru->uygunsuzluk_var == 1 ? 'Var' : 'Gözlem'));
            $template->setValue("soru_aciklama#{$index}", $soru->uygunsuzluk_aciklama ?? '-');
        }

        $fileName = "tetkik_rapor_{$plan->tetkik_no}.docx";
        $path = storage_path("app/public/{$fileName}");
        $template->saveAs($path);

        return response()->download($path)->deleteFileAfterSend(true);
    }

}