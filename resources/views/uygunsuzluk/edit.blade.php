@extends('layouts.admin')

@section('title', 'Uygunsuzluk Düzenle')

@section('content')
<div class="container">
  <h4 class="mb-4">✏️ Uygunsuzluk Düzenle</h4>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('uygunsuzluk.update', $uygunsuzluk->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <!-- Temel Alanlar -->
    <div class="mb-3">
      <label class="form-label">Bölüm</label>
      <input type="text" name="bolum" class="form-control" value="{{ old('bolum', $uygunsuzluk->bolum) }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Tanım</label>
      <input type="text" name="tanim" class="form-control" value="{{ old('tanim', $uygunsuzluk->tanim) }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Bildirim Tarihi</label>
      <input type="date" name="bildirim_tarihi" class="form-control" value="{{ old('bildirim_tarihi', $uygunsuzluk->bildirim_tarihi) }}">
    </div>

    <div class="mb-3">
      <label class="form-label">Uygunsuzluk Sınıfı</label>
      <input type="text" name="uy_sinif" class="form-control" value="{{ old('uy_sinif', $uygunsuzluk->uy_sinif) }}">
    </div>

    <div class="mb-3">
      <label class="form-label">Durum</label>
      <select name="durum" class="form-select">
        <option value="open" @if($uygunsuzluk->durum=='open') selected @endif>Open</option>
        <option value="in_progress" @if($uygunsuzluk->durum=='in_progress') selected @endif>In Progress</option>
        <option value="closed" @if($uygunsuzluk->durum=='closed') selected @endif>Closed</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Kaynak</label>
      <input type="text" name="kaynak" class="form-control" value="{{ old('kaynak', $uygunsuzluk->kaynak) }}">
    </div>

    <div class="mb-3">
      <label class="form-label">Açıklama</label>
      <textarea name="uy_aciklama" class="form-control">{{ old('uy_aciklama', $uygunsuzluk->uy_aciklama) }}</textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Yapılacak Faaliyet</label>
      <input type="text" name="yapilacak_faaliyet" class="form-control" value="{{ old('yapilacak_faaliyet', $uygunsuzluk->yapilacak_faaliyet) }}">
    </div>

    <div class="mb-3">
      <label class="form-label">Kök Neden</label>
      <input type="text" name="kaplam_kok" class="form-control" value="{{ old('kaplam_kok', $uygunsuzluk->kaplam_kok) }}">
    </div>

    <div class="mb-3">
      <label class="form-label">Düzeltici Faaliyet</label>
      <input type="text" name="duzeltici_faaliyet" class="form-control" value="{{ old('duzeltici_faaliyet', $uygunsuzluk->duzeltici_faaliyet) }}">
    </div>

    <div class="mb-3">
      <label class="form-label">Tespit Eden</label>
      <input type="text" name="tespit_eden" class="form-control" value="{{ old('tespit_eden', $uygunsuzluk->tespit_eden) }}">
    </div>

    <div class="mb-3">
      <label class="form-label">Faaliyet Sorumlu</label>
      <input type="text" name="falliyet_sorumlu" class="form-control" value="{{ old('falliyet_sorumlu', $uygunsuzluk->falliyet_sorumlu) }}">
    </div>

    <div class="mb-3">
      <label class="form-label">Planlanan Tarih</label>
      <input type="date" name="planlanan_tarih" class="form-control" value="{{ old('planlanan_tarih', $uygunsuzluk->planlanan_tarih) }}">
    </div>

    <!-- Accordion Yapısı -->
    <div class="accordion mt-4" id="uygunsuzlukAccordion">

      <!-- Faaliyet Değerlendirme -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="headingFaaliyet">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFaaliyet" aria-expanded="true" aria-controls="collapseFaaliyet">
            📌 Faaliyet Değerlendirme
          </button>
        </h2>
        <div id="collapseFaaliyet" class="accordion-collapse collapse show" aria-labelledby="headingFaaliyet" data-bs-parent="#uygunsuzlukAccordion">
          <div class="accordion-body">
            <div class="mb-3">
              <label class="form-label">Faaliyet Değerlendirme</label>
              <input type="text" name="faaliyet_degerlendirme" class="form-control" value="{{ old('faaliyet_degerlendirme', $uygunsuzluk->faaliyet_degerlendirme) }}">
            </div>
            <div class="mb-3">
              <label class="form-label">Faaliyet Açıklama</label>
              <textarea name="faaliyet_aciklama" class="form-control">{{ old('faaliyet_aciklama', $uygunsuzluk->faaliyet_aciklama) }}</textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Faaliyet Kapatan</label>
              <input type="text" name="faaliyet_kapatan" class="form-control" value="{{ old('faaliyet_kapatan', $uygunsuzluk->faaliyet_kapatan) }}">
            </div>
            <div class="mb-3">
              <label class="form-label">Faaliyet Tarih</label>
              <input type="date" name="faaliyet_tarih" class="form-control" value="{{ old('faaliyet_tarih', $uygunsuzluk->faaliyet_tarih) }}">
            </div>
          </div>
        </div>
      </div>

      <!-- Etkinlik Değerlendirme -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="headingEtkinlik">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEtkinlik" aria-expanded="false" aria-controls="collapseEtkinlik">
            📌 Etkinlik Değerlendirme
          </button>
        </h2>
        <div id="collapseEtkinlik" class="accordion-collapse collapse" aria-labelledby="headingEtkinlik" data-bs-parent="#uygunsuzlukAccordion">
          <div class="accordion-body">
            <div class="mb-3">
              <label class="form-label">Etkinlik Değerlendirme</label>
              <input type="text" name="etkinlik_degerlendirme" class="form-control" value="{{ old('etkinlik_degerlendirme', $uygunsuzluk->etkinlik_degerlendirme) }}">
            </div>
            <div class="mb-3">
              <label class="form-label">Etkinlik Açıklama</label>
              <textarea name="etkinlik_aciklama" class="form-control">{{ old('etkinlik_aciklama', $uygunsuzluk->etkinlik_aciklama) }}</textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Etkinlik Kapatan</label>
              <input type="text" name="etkinlik_kapatan" class="form-control" value="{{ old('etkinlik_kapatan', $uygunsuzluk->etkinlik_kapatan) }}">
            </div>
            <div class="mb-3">
              <label class="form-label">Etkinlik Tarih</label>
              <input type="date" name="etkinlik_tarih" class="form-control" 
                     value="{{ old('etkinlik_tarih', $uygunsuzluk->etkinlik_tarih) }}">
            </div>
          </div>
        </div>
      </div> <!-- accordion-item -->
    </div> <!-- accordion -->

    <!-- Dosya Yükleme Alanı -->
    <hr class="mt-4">
    <h5 class="mt-4">📎 Objektif Kayıt Yükleme (Faaliyet Kapatıldığında)</h5>
    <div class="mb-3">
      <label class="form-label">Dosya Yükle</label>
      <input type="file" name="file" class="form-control">
      @if($uygunsuzluk->file_name)
        <p class="mt-2">Mevcut Dosya: 
          <a href="{{ asset('uploads/'.$uygunsuzluk->file_name) }}" target="_blank">
            {{ $uygunsuzluk->file_name }}
          </a>
        </p>
      @endif
    </div>

    <button type="submit" class="btn btn-primary mt-4">Güncelle</button>
    <a href="{{ route('uygunsuzluk.index') }}" class="btn btn-secondary mt-4">İptal</a>
  </form>
</div>
@endsection