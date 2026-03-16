@extends('layouts.admin')

@section('title', 'Kalite Kontrol Kayıtları')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
  <h4>🧪 Kalite Kontrol Kayıtları</h4>
  <a href="{{ route('quality-checks.create') }}" class="btn btn-primary">
    + Yeni Kayıt Ekle
  </a>
</div>


  <table class="table table-bordered table-sm">
    <thead class="table-light">
      <tr>
        <th>#</th>
        <th>Ürün</th>
        <th>Kategori</th>
        <th>Miktar</th>
        <th>Birim</th>
        <th>Tedarikçi</th>
        <th>İrsaliye No</th>
        <th>Geliş Tarihi</th>
        <th>Durum</th>
        <th>Kontrol Eden</th>
        <th>Not</th>
      </tr>
    </thead>
    <tbody>
      @forelse($checks as $check)
        <tr>
          <td>{{ $check->id }}</td>
          <td>{{ $check->rawMaterial->name ?? '-' }}</td>
          <td>{{ ucfirst($check->kategori) }}</td>
          <td>{{ $check->amount }}</td>
          <td>{{ $check->unit }}</td>
          <td>{{ $check->supplier_name ?? '-' }}</td>
          <td>{{ $check->delivery_note_number ?? '-' }}</td>
          <td>{{ \Carbon\Carbon::parse($check->received_at)->format('d.m.Y') }}</td>
          <td>
            <span class="badge bg-{{ $check->status == 'onaylandı' ? 'success' : ($check->status == 'reddedildi' ? 'danger' : 'secondary') }}">
              {{ ucfirst($check->status) }}
            </span>
          </td>
          <td>{{ $check->user->name ?? '-' }}</td>
          <td>{{ $check->notes }}</td>
        </tr>
      @empty
        <tr><td colspan="11">Kayıt bulunamadı.</td></tr>
      @endforelse
    </tbody>
  </table>

  <div class="mt-3">
    {{ $checks->links() }}
  </div>
</div>
@endsection