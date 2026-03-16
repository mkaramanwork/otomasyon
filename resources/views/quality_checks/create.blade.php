@extends('layouts.admin') {{-- Eğer layout dosyan farklıysa burayı güncelle --}}

@section('title', 'Kalite Kontrol Kaydı Oluştur')

@section('content')
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4>🧪 Yeni Kalite Kontrol Kaydı</h4>
    <a href="{{ route('quality-checks.index') }}" class="btn btn-secondary">← Geri Dön</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('quality-checks.store') }}" method="POST" class="card p-4 shadow-sm">
    @csrf

    <div class="row mb-3">
      <div class="col-md-6">
        <label class="form-label">Ürün (Ham Madde)</label>
        <select name="raw_material_id" class="form-select" required>
          @foreach($rawMaterials as $rm)
            <option value="{{ $rm->id }}">{{ $rm->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Kategori</label>
        <select name="kategori" class="form-select" required>
          <option value="katık">Katık</option>
          <option value="baz yağ">Baz Yağ</option>
          <option value="meg">MEG</option>
        </select>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-4">
        <label class="form-label">Batch No</label>
        <input type="text" name="batch_number" class="form-control">
      </div>
      <div class="col-md-4">
        <label class="form-label">İrsaliye No</label>
        <input type="text" name="delivery_note_number" class="form-control">
      </div>
      <div class="col-md-4">
        <label class="form-label">Geliş Tarihi</label>
        <input type="date" name="received_at" class="form-control" required>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label class="form-label">Tedarikçi</label>
        <input type="text" name="supplier_name" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label">Miktar</label>
        <input type="number" step="0.01" name="amount" class="form-control" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Birim</label>
        <input type="text" name="unit" value="kg" class="form-control" required>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Açıklama</label>
      <textarea name="notes" class="form-control" rows="2"></textarea>
    </div>

    <div class="mb-4">
      <label class="form-label">Durum</label>
      <select name="status" class="form-select" required>
        <option value="bekliyor">Bekliyor</option>
        <option value="onaylandı">Onaylandı</option>
        <option value="reddedildi">Reddedildi</option>
      </select>
    </div>

    <button type="submit" class="btn btn-primary w-100">💾 Kaydet</button>
  </form>
</div>
@endsection