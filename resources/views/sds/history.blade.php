@extends('layouts.admin')
@section('title','Hammadde SDS Revizyon Geçmişi')
@section('content')
<div class="container-fluid">
 <div class="d-flex justify-content-between align-items-center mb-3">
 <div><h4>🕘 SDS Revizyon Geçmişi</h4><div class="text-muted">{{ $rawMaterial->name }}</div></div>
 <a class="btn btn-outline-secondary" href="{{ route('sds.raw-materials.index') }}">← Hammadde Listesi</a>
 </div>
 <div class="alert alert-info">Eski SDS kayıtları silinmez veya düzenlenmez. Güncel belgeyi değiştirmek yeni profil/versiyon oluşturur. Yeni tedarikçi revizyon numarası ayrıca girilir.</div>
 <div class="card"><div class="table-responsive"><table class="table table-striped table-bordered mb-0"><thead class="table-dark"><tr><th>Kayıt ID</th><th>Tedarikçi</th><th>SDS Tarihi</th><th>Revizyon</th><th>Versiyon</th><th>Kontrol</th><th>Durum</th><th>İşlem</th></tr></thead><tbody>
 @forelse($profiles as $p)
 <tr><td>{{ $p->id }}</td><td>{{ $p->supplier_name ?: '-' }}</td><td>{{ $p->supplier_sds_date ? \Illuminate\Support\Carbon::parse($p->supplier_sds_date)->format('d.m.Y') : '-' }}</td><td>{{ $p->supplier_sds_revision ?: '-' }}</td><td>{{ $p->supplier_sds_version ?: '-' }}</td><td>{{ $p->is_verified ? 'Doğrulandı' : 'Kontrol Bekliyor' }}</td><td>{{ $p->is_current ? 'Güncel' : 'Arşiv' }}</td>
 <td><a class="btn btn-info btn-sm" href="{{ route('sds.raw-materials.show',[$rawMaterial->id,$p->id]) }}">SDS Detay</a>
 @if($p->is_current)<a class="btn btn-warning btn-sm" href="{{ route('sds.raw-materials.edit',[$rawMaterial->id,$p->id]) }}">Yönet</a>@endif</td></tr>
 @empty<tr><td colspan="8">Kayıt bulunamadı.</td></tr>@endforelse
 </tbody></table></div></div>
</div>
@endsection
