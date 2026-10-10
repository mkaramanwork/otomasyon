@extends('layouts.admin')
@section('title','SDS Yönetimi')
@section('content')
@php $editing = request('edit') ? $rows->firstWhere('id',(int)request('edit')) : null; @endphp
<div class="container-fluid">
 <h3>SDS Yönetimi: {{ ['companies'=>'Şirketler','families'=>'Ürün Aileleri','templates'=>'Şablonlar','preparers'=>'Hazırlayıcılar'][$type] }}</h3>
 <div class="d-flex gap-2 flex-wrap my-3">
 @foreach(['companies'=>'Şirketler','families'=>'Ürün Aileleri','templates'=>'Şablonlar','preparers'=>'Hazırlayıcılar'] as $code=>$label)
 <a class="btn btn-{{ $type===$code?'primary':'outline-secondary' }} btn-sm" href="{{ route('sds.management.index',$code) }}">{{ $label }}</a>
 @endforeach
 </div>
 @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
 @if($errors->any())<div class="alert alert-danger">{{ implode(' | ',$errors->all()) }}</div>@endif
 <div class="card mb-4"><div class="card-header"><strong>{{ $editing?'Kaydı Düzenle':'Yeni Kayıt' }}</strong></div><div class="card-body">
 <form method="POST" enctype="multipart/form-data" action="{{ route('sds.management.save',$type) }}">@csrf
 @if($editing)<input type="hidden" name="id" value="{{ $editing->id }}">@endif
 <div class="row g-3">
 @foreach($fields as $key=>$label)
 <div class="col-md-{{ $key==='address'?12:6 }}"><label class="form-label">{{ $label }}</label>
 @if($key==='company_id')
 <select name="company_id" class="form-select" required><option value="">Firma seç</option>@foreach($companies as $c)<option value="{{ $c->id }}" @selected((string)old($key,$editing->{$key}??'')===(string)$c->id)>{{ $c->name }}</option>@endforeach</select>
 @elseif($key==='logo_upload')
  @php
    $currentLogo = $editing?->logo_path;
    $logoUrl = $currentLogo ? (preg_match('~^https?://~i', $currentLogo) ? $currentLogo : (str_starts_with($currentLogo, 'sds/logos/') ? asset($currentLogo) : asset('storage/' . ltrim($currentLogo, '/')))) : null;
  @endphp
  <input type="file" accept="image/png,image/jpeg,image/webp" name="logo_upload" class="form-control" id="sds-logo-file">
  <small class="text-muted">PNG, JPG veya WebP · En fazla 2 MB. Yeni dosya seçmezsen mevcut logo kalır. Baskıda en-boy oranı korunarak sığdırılır.</small>
  <div class="mt-2 border rounded bg-light p-2" style="height:108px;display:flex;align-items:center;justify-content:center;">
    <img id="sds-logo-preview" src="{{ $logoUrl ?: '' }}" alt="Logo önizleme" style="max-width:100%;max-height:88px;object-fit:contain;{{ $logoUrl ? '' : 'display:none;' }}">
    <span id="sds-logo-placeholder" class="text-muted small" style="{{ $logoUrl ? 'display:none;' : '' }}">Logo seçilmedi</span>
  </div>
  @if($currentLogo)
    <label class="d-block mt-2"><input type="checkbox" name="remove_logo" value="1"> Mevcut logoyu kaldır</label>
  @endif
 @elseif(in_array($key,['issued_at','expires_at'],true))
  @php $dateValue = old($key, $editing && $editing->{$key} ? $editing->{$key}->format('Y-m-d') : ''); @endphp
  <input type="date" class="form-control" name="{{ $key }}" value="{{ $dateValue }}" lang="tr-TR">
  <small class="text-muted">Takvim simgesinden tarihi seçebilirsin.</small>
 @elseif($key==='address')<textarea class="form-control" rows="3" name="{{ $key }}">{{ old($key, $editing->{$key} ?? '') }}</textarea>
 @elseif(in_array($key,['header_color','accent_color'],true))
  @php $colorValue = old($key, $editing->{$key} ?? ($key==='header_color'?'#222222':'#e9ecef')); @endphp
  <div class="d-flex align-items-center gap-2">
   <input type="color" class="form-control form-control-color" id="sds-color-{{ $key }}" name="{{ $key }}" value="{{ $colorValue }}" title="Renk seç" style="width:72px;height:43px;cursor:pointer;">
   <span class="small text-muted" id="sds-color-text-{{ $key }}">{{ $colorValue }}</span>
  </div>
 @else <input class="form-control" name="{{ $key }}" value="{{ old($key,$editing->{$key}??'') }}" {{ $key==='name'?'required':'' }}>@endif
 </div>
 @endforeach
 </div><button class="btn btn-success mt-3">Kaydet</button> @if($editing)<a class="btn btn-outline-secondary mt-3" href="{{ route('sds.management.index',$type) }}">Vazgeç</a>@endif
 </form></div></div>
 <div class="card"><div class="card-header">Kayıtlar</div><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>ID</th><th>Ad</th><th>Diğer</th><th>İşlem</th></tr></thead><tbody>
 @foreach($rows as $row)<tr><td>{{ $row->id }}</td><td>{{ $row->name }}</td><td>{{ $row->code ?? $row->certificate_number ?? $row->email ?? '' }}</td><td><a class="btn btn-outline-primary btn-sm" href="{{ route('sds.management.index',['type'=>$type,'edit'=>$row->id]) }}">Düzenle</a></td></tr>@endforeach
 </tbody></table></div></div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
 for (const field of ['header_color','accent_color']) {
   const picker = document.getElementById('sds-color-' + field);
   const label = document.getElementById('sds-color-text-' + field);
   if (picker && label) picker.addEventListener('input', function () { label.textContent = picker.value; });
 }
 const file = document.getElementById('sds-logo-file');
 const preview = document.getElementById('sds-logo-preview');
 const placeholder = document.getElementById('sds-logo-placeholder');
 if (!file || !preview) return;
 file.addEventListener('change', function () {
   const selected = file.files && file.files[0];
   if (!selected) return;
   if (!['image/png','image/jpeg','image/webp'].includes(selected.type) || selected.size > 2*1024*1024) {
     alert('PNG, JPG veya WebP formatında en fazla 2 MB logo seçiniz.');
     file.value=''; return;
   }
   const objectUrl = URL.createObjectURL(selected);
   preview.onload = function () { URL.revokeObjectURL(objectUrl); };
   preview.src = objectUrl; preview.style.display='block';
   if (placeholder) placeholder.style.display='none';
 });
});
</script>
@endsection
