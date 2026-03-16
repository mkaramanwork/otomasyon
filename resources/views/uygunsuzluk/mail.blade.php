{{-- Tek mail şablonu --}}
@switch($type)

    {{-- Yeni Uygunsuzluk --}}
    @case('uygunsuzluk_new')
        <p>Merhaba,</p>
        <p>Yeni uygunsuzluk kaydı oluşturuldu:</p>
        <ul>
            <li><strong>Bölüm:</strong> {{ $uygunsuzluk->bolum }}</li>
            <li><strong>Tanım:</strong> {{ $uygunsuzluk->tanim }}</li>
            <li><strong>Bildirim Tarihi:</strong> {{ $uygunsuzluk->bildirim_tarihi }}</li>
            <li><strong>Durum:</strong> {{ ucfirst($uygunsuzluk->durum) }}</li>
        </ul>
    @break

    {{-- Uygunsuzluk Güncelleme --}}
    @case('uygunsuzluk_update')
        <p><strong>{{ $uygunsuzluk->bolum }}</strong> bölümündeki uygunsuzluk kaydı güncellendi.</p>
        <p>
            Yeni Durum: {{ ucfirst($uygunsuzluk->durum) }} <br>
            Güncelleme Tarihi: {{ $uygunsuzluk->updated_at }}
        </p>
    @break

    {{-- Uygunsuzluk Kapatma --}}
    @case('uygunsuzluk_close')
        <p><strong>{{ $uygunsuzluk->bolum }}</strong> bölümündeki uygunsuzluk kapatıldı.</p>
        <p>
            Kapatma Tarihi: {{ $uygunsuzluk->faaliyet_tarih }} <br>
            Kapatan: {{ $uygunsuzluk->faaliyet_kapatan }}
        </p>
    @break

@endswitch

<hr>
<p style="font-size: 12px; color: gray;">
    Murat KARAMAN Wep Tarafından otomatik olarak gönderilmiştir.
</p>