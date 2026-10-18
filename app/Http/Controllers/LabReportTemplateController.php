<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class LabReportTemplateController extends Controller
{
    public const FILE = 'lab-report/shared-template.json';
    public const BLOCKS = ['header','sample','remarks','fixed_text','results','signatures','footer'];

    public static function defaults(): array
    {
        return [
            'version'=>1,
            'order'=>self::BLOCKS,
            'font_size'=>8,
            'cell_padding_mm'=>1.1,
            'header_height_mm'=>32,
            'section_gap_mm'=>2,
            'columns'=>[28,28,8,7,7,7,15],
            'remarks'=>'* işaretli metotlar TÜRKAK kapsamında tanımlıdır. Kapsam dışı sonuçlar akreditasyon kapsamında değildir.',
            'fixed_text'=>'',
            'footer'=>'Bu rapor yazılı izin olmadan kısmen çoğaltılamaz. Onaysız rapor geçersizdir.',
            'mark_threshold'=>51,
        ];
    }

    public static function load(): array
    {
        if (!Storage::disk('local')->exists(self::FILE)) return self::defaults();
        $json = json_decode(Storage::disk('local')->get(self::FILE), true);
        if (!is_array($json)) return self::defaults();
        $data = array_replace(self::defaults(), $json);
        $order = array_values(array_intersect((array)($data['order'] ?? []), self::BLOCKS));
        foreach (self::BLOCKS as $block) {
            if (!in_array($block, $order, true)) {
                $index = array_search('remarks', $order, true);
                if ($block === 'fixed_text' && $index !== false) array_splice($order, $index + 1, 0, [$block]);
                else $order[] = $block;
            }
        }
        $data['order'] = $order;
        return $data;
    }

    public function edit()
    {
        Gate::authorize('manage-lab-report-settings');
        return view('sample_acceptances.report_template_editor', ['template'=>self::load()]);
    }

    public function save(Request $request)
    {
        Gate::authorize('manage-lab-report-settings');
        $data = $request->validate([
            'order'=>'required|array|size:7', 'order.*'=>'required|string',
            'font_size'=>'required|numeric|between:7,11',
            'cell_padding_mm'=>'required|numeric|between:0.6,3',
            'header_height_mm'=>'required|numeric|between:24,50',
            'section_gap_mm'=>'required|numeric|between:0,7',
            'columns'=>'required|array|size:7', 'columns.*'=>'required|integer|between:5,50',
            'remarks'=>'nullable|string|max:3000', 'fixed_text'=>'nullable|string|max:5000', 'footer'=>'required|string|max:1000',
        ]);
        if (count(array_unique($data['order'])) !== 7 || array_diff($data['order'], self::BLOCKS) || array_diff(self::BLOCKS, $data['order'])) {
            return back()->withErrors(['order'=>'Şablonun zorunlu bölümleri değiştirilemez.'])->withInput();
        }
        if (array_sum($data['columns']) !== 100) return back()->withErrors(['columns'=>'Sütun genişlikleri toplamı 100 olmalı.'])->withInput();
        $old = self::load();
        $data['mark_threshold'] = 51; // Laboratuvar tercih kuralı; akreditasyon izni değildir.
        $data['version'] = ((int)$old['version'])+1;
        Storage::disk('local')->put(self::FILE, json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        Log::info('FRM.044 ortak şablon revizyonu', ['version'=>$data['version'], 'user_id'=>auth()->id()]);
        return redirect()->route('lab-report.template.edit')->with('success','Ortak rapor şablonu kaydedildi. Sürüm: '.$data['version']);
    }
}
