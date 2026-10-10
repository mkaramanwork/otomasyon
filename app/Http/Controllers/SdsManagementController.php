<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\{SdsCompany,SdsTemplate,SdsProductFamily,SdsPreparer};
class SdsManagementController extends Controller {
 private function models(): array {return ['companies'=>SdsCompany::class,'families'=>SdsProductFamily::class,'templates'=>SdsTemplate::class,'preparers'=>SdsPreparer::class];}
 private function fields(string $type): array {
  return match($type){
   'companies'=>['name'=>'Firma Adı','address'=>'Adres','phone'=>'Telefon','fax'=>'Faks','email'=>'E-posta','emergency_phone'=>'Acil Telefon','logo_upload'=>'Firma Logosu (PNG / JPG / WebP)'],
   'families'=>['name'=>'Ürün Ailesi Adı','code'=>'Kısa Kod',],
   'templates'=>['company_id'=>'Firma ID','name'=>'Şablon Adı','code'=>'Şablon Kodu','header_color'=>'Başlık rengi','accent_color'=>'Vurgu rengi','footer_text'=>'Altbilgi','logo_upload'=>'Şablona Özel Logo (opsiyonel)'],
   'preparers'=>['name'=>'Ad Soyad','title'=>'Unvan','certificate_number'=>'Belge / Sertifika No','issuer'=>'Belgeyi Veren Kuruluş','issued_at'=>'Belge Düzenleme Tarihi (YYYY-MM-DD)','expires_at'=>'Geçerlilik Tarihi (YYYY-MM-DD)','email'=>'E-posta'],
  };
 }
 public function index(string $type) {
  abort_unless(isset($this->models()[$type]),404);
  $rows=$this->models()[$type]::orderByDesc('id')->get();$fields=$this->fields($type);
  $companies=SdsCompany::orderBy('name')->get();
  return view('sds.management.index',compact('type','rows','fields','companies'));
 }
 public function save(Request $request,string $type) {
  abort_unless(isset($this->models()[$type]),404);
  $rules=match($type){
   'companies'=>['name'=>'required|string|max:255','address'=>'nullable|string|max:2000','phone'=>'nullable|string|max:100','fax'=>'nullable|string|max:100','email'=>'nullable|email|max:255','emergency_phone'=>'nullable|string|max:100','logo_upload'=>'nullable|image|mimes:png,jpg,jpeg,webp|max:2048'],
   'families'=>['name'=>'required|string|max:255','code'=>'required|alpha_dash|max:80',],
   'templates'=>['company_id'=>'required|exists:sds_companies,id','name'=>'required|string|max:255','code'=>'required|alpha_dash|max:80','header_color'=>'nullable|regex:/^#[0-9a-fA-F]{6}$/','accent_color'=>'nullable|regex:/^#[0-9a-fA-F]{6}$/','footer_text'=>'nullable|string|max:255','logo_upload'=>'nullable|image|mimes:png,jpg,jpeg,webp|max:2048'],
   'preparers'=>['name'=>'required|string|max:255','title'=>'nullable|string|max:255','certificate_number'=>'nullable|string|max:255','issuer'=>'nullable|string|max:255','issued_at'=>'nullable|date','expires_at'=>'nullable|date|after_or_equal:issued_at','email'=>'nullable|email|max:255'],
  };
  $data=$request->validate($rules);
  // Firma logosu veya şablon logosu; mevcut logo değiştirilmedikçe korunur.
  // SVG güvenlik nedeniyle kabul edilmez. Dosyalar public disk içine yüklenir.
  if (in_array($type, ['companies','templates'], true)) {
    if ($request->hasFile('logo_upload')) {
      // Symlink gerektirmez: public/sds/logos dizininde doğrudan yayınla.
      $folder = public_path('sds/logos');
      if (!is_dir($folder) && !mkdir($folder, 0755, true) && !is_dir($folder)) {
        return back()->withErrors(['logo_upload' => 'public/sds/logos klasörü oluşturulamadı. Dizin izinlerini kontrol edin.'])->withInput();
      }
      $file = $request->file('logo_upload');
      // Her dosya için rastgele isim; kullanıcı dosya adı URL yoluna girmez.
      $filename = bin2hex(random_bytes(16)) . '.' . strtolower($file->extension());
      $file->move($folder, $filename);
      $data['logo_path'] = 'sds/logos/' . $filename;
    } elseif ($request->boolean('remove_logo')) {
      $data['logo_path'] = null;
    }
    unset($data['logo_upload']);
  }
  $model=$this->models()[$type];
  if($type==='templates'){$dup=$model::where('company_id',$data['company_id'])->where('code',$data['code'])->first();if($dup && $dup->id!=$request->input('id'))return back()->withErrors(['code'=>'Bu firmada şablon kodu mevcut.'])->withInput();}
  if($type==='families'){$dup=$model::where('code',$data['code'])->first();if($dup && $dup->id!=$request->input('id'))return back()->withErrors(['code'=>'Ürün ailesi kodu mevcut.'])->withInput();}
  $id=$request->integer('id');$record=$id?$model::findOrFail($id):new $model();
  $record->fill($data);$record->save();
  return redirect()->route('sds.management.index',$type)->with('success','Kayıt kaydedildi.');
 }
}
