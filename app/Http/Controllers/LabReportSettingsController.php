<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\BusinessLog;

class LabReportSettingsController extends Controller
{
    public function index()
    {
        Gate::authorize('manage-lab-report-settings');
        return view('sample_acceptances.report_settings', [
            'settings' => DB::table('lab_report_settings')->first(),
            'users' => User::orderBy('name')->get(['id','name','email']),
            'signatures' => DB::table('lab_report_signatures')->get()->keyBy('user_id'),
            'images' => $this->previews(),
        ]);
    }

    public function save(Request $request)
    {
        Gate::authorize('manage-lab-report-settings');
        $data = $request->validate([
            'laboratory_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:2000',
            'phone' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'accreditation_number' => 'nullable|string|max:50',
            'accreditation_mark_enabled' => 'nullable|boolean',
            'laboratory_manager_user_id' => 'nullable|exists:users,id',
            'document_code' => 'required|string|max:100',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'accreditation_mark' => 'nullable|image|mimes:png|max:2048',
            'stamp' => 'nullable|image|mimes:png|max:2048',
            'logo_width_mm' => 'required|numeric|between:10,60',
            'mark_width_mm' => 'required|numeric|between:10,30',
            'stamp_width_mm' => 'required|numeric|between:10,55',
            'signature_width_mm' => 'required|numeric|between:10,50',
            'logo_height_mm' => 'required|numeric|between:5,35',
            'mark_height_mm' => 'required|numeric|between:10,40',
            'stamp_height_mm' => 'required|numeric|between:5,35',
            'signature_height_mm' => 'required|numeric|between:5,30',
        ]);
        $old = DB::table('lab_report_settings')->first();
        $newLogo = null;
        $newMark = null;
        $newStamp = null;
        if ($request->hasFile('logo')) {
            $newLogo = $request->file('logo')->store('lab-report/branding', 'local');
        }
        if ($request->hasFile('accreditation_mark')) {
            $newMark = $request->file('accreditation_mark')->store('lab-report/branding', 'local');
        }
        if ($request->hasFile('stamp')) {
            $newStamp = $request->file('stamp')->store('lab-report/branding', 'local');
        }
        $markEnabled = $request->boolean('accreditation_mark_enabled');
        $markPath = $newMark ?: ($old->mark_path ?? null);
        if ($markEnabled && (!$markPath || empty($data['accreditation_number']))) {
            return back()->withErrors(['accreditation_mark_enabled' => 'Marka kullanımı için dosya ve doğrulanmış akreditasyon numarası gereklidir.'])->withInput();
        }
        $fields = collect($data)->except(['logo', 'accreditation_mark', 'stamp', 'accreditation_mark_enabled'])->all();
        $fields['accreditation_mark_enabled'] = $markEnabled;
        $fields['logo_path'] = $newLogo ?: ($old->logo_path ?? null);
        $fields['mark_path'] = $markPath;
        $fields['stamp_path'] = $newStamp ?: ($old->stamp_path ?? null);
        $fields['updated_at'] = now();
        if ($old) DB::table('lab_report_settings')->where('id', $old->id)->update($fields);
        else DB::table('lab_report_settings')->insert($fields + ['created_at' => now()]);
        // Daha onceki onaylar, ayarlarin degismesiyle icerik ozeti uzerinden gecersiz kalir.
        if ($newLogo && $old?->logo_path) Storage::disk('local')->delete($old->logo_path);
        if ($newMark && $old?->mark_path) Storage::disk('local')->delete($old->mark_path);
        if ($newStamp && $old?->stamp_path) Storage::disk('local')->delete($old->stamp_path);
        return back()->with('success','Rapor ayarları kaydedildi. Değişen raporlar yeniden onay gerektirebilir.');
    }

    private function dataUri(?string $path): ?string
    {
        if (!$path || !Storage::disk('local')->exists($path)) return null;
        $bytes = Storage::disk('local')->get($path);
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($bytes);
        if (!in_array($mime, ['image/png','image/jpeg'], true)) return null;
        return 'data:' . $mime . ';base64,' . base64_encode($bytes);
    }

    private function previews(): array
    {
        $settings = DB::table('lab_report_settings')->first();
        return [
            'logo' => $this->dataUri($settings->logo_path ?? null),
            'accreditation_mark' => $this->dataUri($settings->mark_path ?? null),
            'stamp' => $this->dataUri($settings->stamp_path ?? null),
            'signatures' => DB::table('lab_report_signatures')->get()->mapWithKeys(
                fn($row) => [(int)$row->user_id => $this->dataUri($row->file_path)]
            )->all(),
        ];
    }

    public function uploadMySignature(Request $request)
    {
        $data = $request->validate(['signature' => 'required|file|mimes:png|max:1024']);
        $uid = auth()->id();
        $old = DB::table('lab_report_signatures')->where('user_id', $uid)->first();
        $path = $request->file('signature')->store('lab-report/signatures', 'local');
        DB::table('lab_report_signatures')->updateOrInsert(['user_id'=>$uid], [
            'file_path'=>$path,'created_at'=>$old?->created_at ?? now(),'updated_at'=>now()
        ]);
        if ($old) Storage::disk('local')->delete($old->file_path);
        return back()->with('success','Kendi imza görseliniz güncellendi; daha önceki raporlar için yeniden onay gerekir.');
    }
}
