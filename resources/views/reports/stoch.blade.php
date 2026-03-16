@extends('layouts.admin')

@section('title', 'Stok Hareketleri')

@section('content')
<div class="container mt-4">

  <h4 class="mb-4">📦 Stok Hareketleri (Giriş & Çıkış)</h4>

  {{-- ✅ Filtre Formu --}}
  <form method="GET" class="row g-3 mb-4 p-3 border rounded bg-light">
    <div class="col-md-3">
      <label class="form-label fw-bold">Başlangıç Tarihi</label>
      <input type="date" name="start_date" value="{{ $request->start_date }}" class="form-control">
    </div>

    <div class="col-md-3">
      <label class="form-label fw-bold">Bitiş Tarihi</label>
      <input type="date" name="end_date" value="{{ $request->end_date }}" class="form-control">
    </div>

    <div class="col-md-4">
      <label class="form-label fw-bold">Ham Madde Adı</label>
      <input type="text" name="material" value="{{ $request->material }}" class="form-control" placeholder="örn. MEG, Katık">
    </div>

    <div class="col-md-2 d-flex align-items-end gap-2">
      <button type="submit" class="btn btn-primary w-100">Filtrele</button>
      <a href="{{ route('reports.stoch') }}" class="btn btn-outline-secondary w-100">Temizle</a>
    </div>
  </form>

  {{-- ✅ Tablo --}}
  <table class="table table-bordered table-striped table-hover align-middle">
    <thead class="table-dark">
      <tr>
        <th>#</th>
        <th>Ham Madde</th>
        <th>Tip</th>
        <th>Miktar</th>
        <th>Ürün / İş Emri</th>
        <th>Kullanıcı</th>
        <th>Tarih</th>
      </tr>
    </thead>

    <tbody>
      @forelse($movements as $m)
        <tr>
          <td>{{ $m->id }}</td>

          {{-- ✅ Ham madde adı --}}
          <td class="fw-bold">
            {{ $m->material->name ?? '-' }}
          </td>

          {{-- ✅ Hareket tipi --}}
          <td>
            @if($m->type === 'in')
              <span class="badge bg-success">⬆️ Ürün Girişi</span>
            @elseif($m->type === 'out' && $m->productionOrder)
              <span class="badge bg-warning text-dark">🏭 Üretim Çıkışı</span>
            @elseif($m->type === 'out')
              <span class="badge bg-danger">⬇️ Stok Çıkışı</span>
            @else
              <span class="badge bg-secondary">{{ ucfirst($m->type) }}</span>
            @endif
          </td>

          {{-- ✅ Miktar --}}
          <td class="fw-bold">{{ number_format($m->amount, 2) }} kg</td>

          {{-- ✅ Ürün + İş Emri --}}
          <td>
            @if($m->productionOrder)
              <strong>{{ $m->productionOrder->product->name }}</strong><br>
              <small class="text-muted">Batch: {{ $m->productionOrder->batch_number }}</small>
            @else
              <span class="text-muted">-</span>
            @endif
          </td>

          {{-- ✅ Kullanıcı --}}
          <td>{{ $m->triggeredBy->name ?? '-' }}</td>

          {{-- ✅ Tarih --}}
          <td>{{ $m->created_at->format('d.m.Y H:i') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="text-center text-muted py-3">
            Kayıt bulunamadı.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>

  {{-- ✅ Toplamlar --}}
  @php
    $totalIn = $movements->where('type', 'in')->sum('amount');
    $totalOut = $movements->where('type', 'out')->sum('amount');
  @endphp

  <div class="row mt-4">
    <div class="col-md-6">
      <div class="p-3 bg-success text-white rounded shadow-sm">
        <h5 class="mb-1">Toplam Giriş</h5>
        <h3>{{ number_format($totalIn, 2) }} kg</h3>
      </div>
    </div>

    <div class="col-md-6">
      <div class="p-3 bg-danger text-white rounded shadow-sm">
        <h5 class="mb-1">Toplam Çıkış</h5>
        <h3>{{ number_format($totalOut, 2) }} kg</h3>
      </div>
    </div>
  </div>

</div>
@endsection