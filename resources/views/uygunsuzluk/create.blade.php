@extends('layouts.admin')

@section('title', 'Yeni Uygunsuzluk')

@section('content')
<div class="container">
  <h4 class="mb-4">➕ Yeni Uygunsuzluk Kaydı</h4>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('uygunsuzluk.store') }}" method="POST">
    @csrf

    <div class="mb-3">
      <label class="form-label">Bölüm</label>
      <input type="text" name="bolum" class="form-control" value="{{ old('bolum') }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Tanım</label>
      <input type="text" name="tanim" class="form-control" value="{{ old('tanim') }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Bildirim Tarihi</label>
      <input type="date" name="bildirim_tarihi" class="form-control" value="{{ old('bildirim_tarihi') }}">
    </div>

    <div class="mb-3">
      <label class="form-label">Uygunsuzluk Sınıfı</label>
      <input type="text" name="uy_sinif" class="form-control" value="{{ old('uy_sinif') }}">
    </div>

    <div class="mb-3">
      <label class="form-label">Durum</label>
      <select name="durum" class="form-select">
        <option value="open">Open</option>
        <option value="in_progress">In Progress</option>
        <option value="closed">Closed</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Kaynak</label>
      <input type="text" name="kaynak" class="form-control" value="{{ old('kaynak') }}">
    </div>

    <div class="mb-3">
      <label class="form-label">Açıklama</label>
      <textarea name="uy_aciklama" class="form-control">{{ old('uy_aciklama') }}</textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Yapılacak Faaliyet</label>
      <input type="text" name="yapilacak_faaliyet" class="form-control" value="{{ old('yapilacak_faaliyet') }}">
    </div>

    <div class="mb-3">
      <label class="form-label">Kök Neden</label>
      <input type="text" name="kaplam_kok" class="form-control" value="{{ old('kaplam_kok') }}">
    </div>

    <div class="mb-3">
      <label class="form-label">Düzeltici Faaliyet</label>
      <input type="text" name="duzeltici_faaliyet" class="form-control" value="{{ old('duzeltici_faaliyet') }}">
    </div>

    <div class="mb-3">
      <label class="form-label">Tespit Eden</label>
      <input type="text" name="tespit_eden" class="form-control" value="{{ old('tespit_eden') }}">
    </div>

    <div class="mb-3">
      <label class="form-label">Faaliyet Sorumlu</label>
      <!-- DİKKAT: migration’da falliyet_sorumlu yazıyor, formda da aynı olmalı -->
      <input type="text" name="falliyet_sorumlu" class="form-control" value="{{ old('falliyet_sorumlu') }}">
    </div>

    <div class="mb-3">
      <label class="form-label">Planlanan Tarih</label>
      <input type="date" name="planlanan_tarih" class="form-control" value="{{ old('planlanan_tarih') }}">
    </div>

    <button type="submit" class="btn btn-success">Kaydet</button>
    <a href="{{ route('uygunsuzluk.index') }}" class="btn btn-secondary">İptal</a>
  </form>
</div>
@endsection