@extends('layouts.admin')
@section('title','MKWeP Menü Yönetimi')
@section('content')
<div class="container-fluid pb-4">
<h4>MKWeP Menü Yönetimi</h4>
<p class="text-muted">Mevcut menü sıralaması korunur. Yeni bağlantı, üst grup veya başlık ekleyebilirsiniz.</p>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="card mb-4"><div class="card-header fw-semibold">+ Yeni Menü Ekle</div><div class="card-body">
<form method="POST" action="{{ route('mkwep.menus.store') }}" class="row g-2 align-items-end">@csrf
<div class="col-md-3"><label class="form-label">Menü adı</label><input name="label" class="form-control" required placeholder="Yeni menü" value="{{ old('label') }}"></div>
<div class="col-md-2"><label class="form-label">Tür</label><select name="item_type" class="form-select"><option value="link">Bağlantı</option><option value="parent">Açılır grup</option><option value="header">Başlık</option></select></div>
<div class="col-md-3"><label class="form-label">Üst Grup</label><select name="parent_key" class="form-select"><option value="">Ana menü / bağımsız</option>@foreach($parents as $p)<option value="{{ $p->item_key }}">{{ $p->label }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label">Açılacak Sayfa</label><select name="route_name" class="form-select"><option value="">Sayfa seçiniz</option>@foreach($routes as $r)<option value="{{ $r }}" @selected(old('route_name') === $r)>{{ $r }}</option>@endforeach</select><small class="text-muted">Bağlantı ve sabit parametreler otomatik oluşturulur.</small></div>
<div class="col-md-3"><label class="form-label">İkon (Bootstrap Icons)</label><div class="input-group"><span class="input-group-text"><i class="bi bi-circle mkwep-icon-preview"></i></span><input name="icon" class="form-control mkwep-icon-input" value="bi-circle" placeholder="bi-file-text"></div></div>
<div class="col-md-2"><label class="form-label">Sıra</label><input type="number" name="sort_order" class="form-control" min="0" value="{{ ($menus->max('sort_order') ?? 0)+10 }}" required></div>
<div class="col-md-2"><div class="form-check mb-2"><input type="checkbox" name="is_active" value="1" checked class="form-check-input" id="new-menu-active"><label class="form-check-label" for="new-menu-active">Aktif</label></div></div>
<div class="col-md-1"><button class="btn btn-success">Ekle</button></div>
</form></div></div>

<div class="table-responsive"><table class="table table-striped table-bordered align-middle table-sm">
<thead><tr><th>Menü</th><th>Tip</th><th>Üst Grup</th><th>Açılacak Sayfa</th><th>İkon</th><th>Sıra</th><th>Aktif</th><th>İşlem</th></tr></thead>
<tbody>@foreach($menus as $m)
<tr>
<td colspan="8" class="p-0">
<form id="mkwep-edit-{{ $m->id }}" method="POST" action="{{ route('mkwep.menus.update',$m->id) }}">@csrf @method('PUT')</form>
<div class="d-flex flex-wrap align-items-center gap-2 p-2">
<input form="mkwep-edit-{{ $m->id }}" name="label" class="form-control form-control-sm" style="width:185px" value="{{ $m->label }}" required aria-label="Menü adı">
<select form="mkwep-edit-{{ $m->id }}" name="item_type" class="form-select form-select-sm" style="width:105px"><option value="link" @selected($m->item_type==='link')>link</option><option value="parent" @selected($m->item_type==='parent')>parent</option><option value="header" @selected($m->item_type==='header')>header</option></select>
<select form="mkwep-edit-{{ $m->id }}" name="parent_key" class="form-select form-select-sm" style="width:170px"><option value="">Ana menü</option>@foreach($parents as $p)<option value="{{ $p->item_key }}" @selected($m->parent_key===$p->item_key) @disabled($m->item_key===$p->item_key)>{{ $p->label }}</option>@endforeach</select>
<div style="width:230px"><select form="mkwep-edit-{{ $m->id }}" name="route_name" class="form-select form-select-sm"><option value="">Sayfa seçiniz</option>@if($m->route_name && !in_array($m->route_name, $routes, true))<option value="{{ $m->route_name }}" selected disabled>{{ $m->route_name }} (genel menüye uygun değil)</option>@endif @foreach($routes as $r)<option value="{{ $r }}" @selected($m->route_name === $r)>{{ $r }}</option>@endforeach</select></div>
<div class="input-group input-group-sm" style="width:170px"><span class="input-group-text"><i class="bi {{ $m->icon }} mkwep-icon-preview"></i></span><input form="mkwep-edit-{{ $m->id }}" name="icon" value="{{ $m->icon }}" class="form-control mkwep-icon-input"></div>
<input form="mkwep-edit-{{ $m->id }}" type="number" name="sort_order" min="0" value="{{ $m->sort_order }}" class="form-control form-control-sm" style="width:75px" required>
<input form="mkwep-edit-{{ $m->id }}" type="checkbox" name="is_active" value="1" @checked($m->is_active) title="Aktif">
<button form="mkwep-edit-{{ $m->id }}" class="btn btn-sm btn-primary">Kaydet</button>
<form method="POST" action="{{ route('mkwep.menus.move',$m->id) }}" class="d-inline">@csrf <input type="hidden" name="direction" value="up"><button class="btn btn-sm btn-outline-secondary" title="Yukarı">↑</button></form>
<form method="POST" action="{{ route('mkwep.menus.move',$m->id) }}" class="d-inline">@csrf <input type="hidden" name="direction" value="down"><button class="btn btn-sm btn-outline-secondary" title="Aşağı">↓</button></form>
<form method="POST" action="{{ route('mkwep.menus.destroy',$m->id) }}" class="d-inline" onsubmit="return confirm('Bu menü silinsin mi?');">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Sil</button></form>
</div></td></tr>
@endforeach</tbody></table></div>
</div>
<script>
document.addEventListener('input',function(e){if(!e.target.classList.contains('mkwep-icon-input'))return;const group=e.target.closest('.input-group');const icon=group&&group.querySelector('.mkwep-icon-preview');if(icon)icon.className='bi '+(e.target.value||'bi-circle')+' mkwep-icon-preview';});
</script>
@endsection
