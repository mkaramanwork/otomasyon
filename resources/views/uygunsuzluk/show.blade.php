@extends('layouts.admin')

@section('title', 'Uygunsuzluk Detayı')

@section('content')
<div class="container">
  <h4 class="mb-4">📄 Uygunsuzluk Detayı</h4>

  <table class="table table-bordered">
    <tr><th>ID</th><td>{{ $uygunsuzluk->id }}</td></tr>
    <tr><th>Bölüm</th><td>{{ $uygunsuzluk->bolum }}</td></tr>
    <tr><th>Tanım</th><td>{{ $uygunsuzluk->tanim }}</td></tr>
    <tr><th>Durum</th>
      <td>
        <span class="badge bg-{{ $uygunsuzluk->durum === 'closed' ? 'success' : 'warning' }}">
          {{ ucfirst($uygunsuzluk->durum) }}
        </span>
      </td>
    </tr>
    <tr><th>Bildirim Tarihi</th><td>{{ $uygunsuzluk->bildirim_tarihi }}</td></tr>
    <tr><th>Kaynak</th><td>{{ $uygunsuzluk->kaynak }}</td></tr>
    <tr><th>Açıklama</th><td>{{ $uygunsuzluk->uy_aciklama }}</td></tr>
    <tr><th>Yapılacak Faaliyet</th><td>{{ $uygunsuzluk->yapilacak_faaliyet }}</td></tr>
    <tr><th>Kök Neden</th><td>{{ $uygunsuzluk->kaplam_kok }}</td></tr>
    <tr><th>Düzeltici Faaliyet</th><td>{{ $uygunsuzluk->duzeltici_faaliyet }}</td></tr>
    <tr><th>Tespit Eden</th><td>{{ $uygunsuzluk->tespit_eden }}</td></tr>
    <tr><th>Faaliyet Sorumlu</th><td>{{ $uygunsuzluk->falliyet_sorumlu }}</td></tr>
    <tr><th>Planlanan Tarih</th><td>{{ $uygunsuzluk->planlanan_tarih }}</td></tr>
    <tr><th>Faaliyet Değerlendirme</th><td>{{ $uygunsuzluk->faaliyet_degerlendirme }}</td></tr>
    <tr><th>Faaliyet Açıklama</th><td>{{ $uygunsuzluk->faaliyet_aciklama }}</td></tr>
    <tr><th>Faaliyet Kapatan</th><td>{{ $uygunsuzluk->faaliyet_kapatan }}</td></tr>
    <tr><th>Faaliyet Tarih</th><td>{{ $uygunsuzluk->faaliyet_tarih }}</td></tr>
    <tr><th>Etkinlik Değerlendirme</th><td>{{ $uygunsuzluk->etkinlik_degerlendirme }}</td></tr>
    <tr><th>Etkinlik Açıklama</th><td>{{ $uygunsuzluk->etkinlik_aciklama }}</td></tr>
    <tr><th>Etkinlik Kapatan</th><td>{{ $uygunsuzluk->etkinlik_kapatan }}</td></tr>
    <tr><th>Etkinlik Tarih</th><td>{{ $uygunsuzluk->etkinlik_tarih }}</td></tr>

    <!-- Dosya Alanı -->
    <tr>
      <th>Objektif Kayıt</th>
      <td>
        @if($uygunsuzluk->file_name)
          <a href="{{ asset('uploads/'.$uygunsuzluk->file_name) }}" target="_blank" class="btn btn-sm btn-outline-info">
            📎 Dosyayı Görüntüle
          </a>
          <p class="mt-2"><small>Yüklendi: {{ $uygunsuzluk->uploaded_on }}</small></p>
        @else
          <span class="text-muted">Dosya yüklenmemiş</span>
        @endif
      </td>
    </tr>

    <tr><th>Oluşturulma</th><td>{{ $uygunsuzluk->created_at }}</td></tr>
    <tr><th>Güncellenme</th><td>{{ $uygunsuzluk->updated_at }}</td></tr>
  </table>

  <div class="mt-3">
    <a href="{{ route('uygunsuzluk.index') }}" class="btn btn-secondary">⬅️ Listeye Dön</a>
    <a href="{{ route('uygunsuzluk.edit', $uygunsuzluk->id) }}" class="btn btn-primary">✏️ Düzenle</a>
    <form action="{{ route('uygunsuzluk.destroy', $uygunsuzluk->id) }}" method="POST" 
          onsubmit="return confirm('Bu kaydı silmek istediğinize emin misiniz?')" style="display:inline;">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn btn-danger">🗑️ Sil</button>
    </form>
  </div>
</div>
@endsection