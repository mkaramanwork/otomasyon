@extends('layouts.admin')
@section('title','Deney Raporu Ayarları')
@section('content')
<div class="container py-3">
 <h4>Deney Raporu – Kurumsal Ayarlar</h4>
 <p class="text-muted">Kalite sisteminde tanımlı FRM.044 biçimi korunur. Akreditasyon markasını yalnızca kullanım yetkiniz varsa etkinleştirin.</p>
 @if(session('success'))<div class="alert alert-success">{{session('success')}}</div>@endif
 @if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>@endif
 <form action="{{route('lab-report.settings.save')}}" method="POST" enctype="multipart/form-data" class="card p-3 mb-3">@csrf
 <h5>Kurumsal Bilgiler ve Belgeler</h5>
 <div class="row g-3">
 @foreach(['laboratory_name'=>'Laboratuvar Adı','address'=>'Adres','phone'=>'Telefon','email'=>'E-posta','accreditation_number'=>'Doğrulanmış Akreditasyon No','document_code'=>'Doküman Kod/Revizyon'] as $k=>$label)
 <div class="col-md-6"><label class="form-label">{{$label}}</label><input class="form-control" name="{{$k}}" value="{{old($k,$settings->$k ?? ($k==='document_code'?'FRM.044/22.09.2025-11':''))}}" {{$k==='laboratory_name'||$k==='document_code'?'required':''}}></div>
 @endforeach
 <div class="col-md-6"><label class="form-label">Kurumsal logo (PNG/JPG)</label><input type="file" class="form-control js-preview-file" data-preview="preview-logo" name="logo" accept="image/png,image/jpeg"><div class="small text-muted">Oranı bozulmadan şablondaki alana sığdırılır.</div></div>
 <div class="col-md-6"><label class="form-label">TÜRKAK akreditasyon markası (resmî PNG)</label><input type="file" class="form-control js-preview-file" data-preview="preview-accreditation_mark" name="accreditation_mark" accept="image/png"><div class="small text-muted">Sadece yetkili, değiştirilmeyen resmî görsel.</div></div>

 <div class="col-md-6"><label class="form-label">Mühür (şeffaf PNG)</label><input type="file" class="form-control js-preview-file" data-preview="preview-stamp" name="stamp" accept="image/png"><small class="text-muted">Mühür yalnızca geçerli onaylı rapora basılır.</small></div>
 @foreach(['logo_width_mm'=>'Logo genişliği (mm)','mark_width_mm'=>'Akreditasyon markası genişliği (mm)','stamp_width_mm'=>'Mühür genişliği (mm)','signature_width_mm'=>'Personel imza genişliği (mm)'] as $field=>$label)
 <div class="col-md-3"><label class="form-label">{{$label}}</label><input type="number" step="1" min="10" max="{{ $field==='logo_width_mm'?60:($field==='mark_width_mm'?30:($field==='stamp_width_mm'?55:50)) }}" class="form-control" name="{{$field}}" value="{{old($field,$settings->$field ?? ['logo_width_mm'=>38,'mark_width_mm'=>23,'stamp_width_mm'=>32,'signature_width_mm'=>30][$field])}}" required></div>
 @endforeach

 <div class="col-12"><h5 class="mt-2">Kayıtlı Görseller ve Canlı Önizleme</h5><p class="text-muted small">Yeni dosya seçildiğinde önizleme hemen değişir; kalıcı kayıt için Ayarları Kaydet'e basmanız gerekir.</p><div class="row g-3">
 @foreach(['logo'=>'Kurumsal Logo','accreditation_mark'=>'TÜRKAK Akreditasyon Markası','stamp'=>'Laboratuvar Mührü'] as $key=>$caption)
 <div class="col-md-4"><div class="border rounded p-3 text-center h-100"><strong>{{ $caption }}</strong>
 <div class="d-flex align-items-center justify-content-center bg-light rounded mt-2" style="min-height:125px;overflow:hidden">
 <img id="preview-{{ $key }}" src="{{ $images[$key] ?? '' }}" alt="{{ $caption }}" style="max-width:100%;max-height:120px;object-fit:contain;{{ empty($images[$key]) ? 'display:none;' : '' }}">
 <span id="empty-{{ $key }}" class="text-muted small" @if(!empty($images[$key])) style="display:none" @endif>Henüz yüklenmedi</span>
 </div><small class="text-muted d-block mt-2">Kaydedilmiş dosya mevcutsa görüntülenir.</small></div></div>
 @endforeach
 </div></div>
 @foreach(['logo_height_mm'=>'Logo yüksekliği (mm)','mark_height_mm'=>'Akreditasyon markası yüksekliği (mm)','stamp_height_mm'=>'Mühür yüksekliği (mm)','signature_height_mm'=>'İmza yüksekliği (mm)'] as $field=>$label)
 <div class="col-md-3"><label class="form-label">{{ $label }}</label><input type="number" step="1" min="5" max="{{ $field==='mark_height_mm' ? 40 : ($field==='signature_height_mm' ? 30 : 35) }}" class="form-control" name="{{ $field }}" value="{{ old($field, $settings->$field ?? ['logo_height_mm'=>24,'mark_height_mm'=>25,'stamp_height_mm'=>20,'signature_height_mm'=>13][$field]) }}" required></div>
 @endforeach
 <div class="col-12"><small class="text-muted">Genişlik/yükseklik kutuları, görselin yerleşeceği alanın ölçüleridir. Görselin özgün en-boy oranı korunur; özellikle TÜRKAK markası esnetilmez.</small></div>
 <div class="col-12 alert alert-info">Laboratuvar personeli manuel seçilmez: Numunenin ilk analiz sonucu giriş kayıtları sayılır. En fazla giriş yapan kişinin adı ve kendi yüklediği PNG imzası, yönetici onayından sonra gösterilir. Eşitlik veya eksik log varsa otomatik atama yapılmaz.</div>
 <div class="col-md-6"><label class="form-label">Onaylayan / Laboratuvar Müdürü</label><select class="form-select" name="laboratory_manager_user_id"><option value="">Seçiniz</option>@foreach($users as $person)<option value="{{$person->id}}" @selected(old('laboratory_manager_user_id',$settings->laboratory_manager_user_id??null)==$person->id)>{{$person->name}} ({{$person->email}})</option>@endforeach</select></div>
 <div class="col-12"><label><input type="checkbox" name="accreditation_mark_enabled" value="1" @checked(old('accreditation_mark_enabled',$settings->accreditation_mark_enabled??false))> Yetkili raporlarda akreditasyon markası kullanımı</label></div>
 </div><button class="btn btn-primary mt-3">Ayarları Kaydet</button></form>
 <div class="card p-3"><h5>Personel / İmza Envanteri</h5><p class="small">Yüklenen imza tek başına onay oluşturmaz. Her kullanıcı yalnızca kendi imza dosyasını yükler; onay için ayrıca yetkili işlem gerekir.</p>
 <table class="table table-sm table-striped"><thead><tr><th>Personel</th><th>E-posta</th><th>İmza Önizlemesi</th></tr></thead><tbody>@foreach($users as $person)<tr><td>{{$person->name}}</td><td>{{$person->email}}</td><td>@if(!empty($images['signatures'][$person->id]))<img src="{{ $images['signatures'][$person->id] }}" style="max-width:150px;max-height:65px;object-fit:contain" alt="Kayıtlı imza">@else <span class="text-muted">Yüklenmedi</span> @endif</td></tr>@endforeach</tbody></table>
 <form action="{{route('lab-report.signature.mine')}}" method="POST" enctype="multipart/form-data">@csrf<label class="form-label">Kendi imzanızı yükleyin (PNG)</label><input type="file" name="signature" required accept="image/png" class="js-preview-file" data-preview="preview-my-signature" class="form-control mb-2"><div class="border rounded p-2 mb-2" style="max-width:260px"><div class="small text-muted">Yeni seçilen imza önizlemesi</div><img id="preview-my-signature" style="display:none;max-width:100%;max-height:90px;object-fit:contain" alt="Seçilen imza"></div><button class="btn btn-outline-primary">İmzamı Kaydet</button></form></div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.js-preview-file').forEach(function (field) {
        field.addEventListener('change', function () {
            const target = document.getElementById(field.dataset.preview);
            if (!target) return;
            const file = field.files && field.files[0];
            if (!file || !file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = function (event) {
                target.src = event.target.result;
                target.style.display = 'block';
                const empty = document.getElementById(field.dataset.preview.replace('preview-', 'empty-'));
                if (empty) empty.style.display = 'none';
            };
            reader.readAsDataURL(file);
        });
    });
});
</script>
@endsection
