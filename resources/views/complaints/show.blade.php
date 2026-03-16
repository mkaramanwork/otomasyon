@extends('layouts.admin')

@section('title','Şikayet Detayı')

@section('content')
<div class="container mt-4">
    <h3>📑 Şikayet Detayı: {{ $complaint->complaint_no }}</h3>

    <p><strong>Alınma Tarihi:</strong> {{ $complaint->received_date }}</p>
    <p><strong>Konu:</strong> {{ $complaint->subject }}</p>
    <p><strong>Tanım:</strong> {{ $complaint->description }}</p>
    <p><strong>Sahibi:</strong> {{ $complaint->owner }}</p>
    <p><strong>Telefon:</strong> {{ $complaint->phone }}</p>
    <p><strong>Email:</strong> {{ $complaint->email }}</p>
    <p><strong>Şikayeti Alan:</strong> {{ $complaint->received_by }}</p>
    <p><strong>Ürün:</strong> {{ $complaint->product->name }}</p>
    <p><strong>Kayıt Eden:</strong> {{ $complaint->user->name }}</p>
    <p><strong>Durum:</strong> {{ $complaint->status }}</p>

    <hr>

    <h5>📝 Değerlendirme</h5>
    <p>{{ $complaint->evaluation }}</p>
    <p><strong>Tarih:</strong> {{ $complaint->evaluation_date }}</p>
    <p><strong>Yapan:</strong> {{ $complaint->evaluated_by }}</p>

    <h5>📊 Sonuç</h5>
    <p>{{ $complaint->result }}</p>

    <h5>⚠️ DIF</h5>
    <p><strong>Açıldı mı?:</strong> {{ $complaint->dif_opened }}</p>
    @if($complaint->dif_opened == 'evet')
        <p><strong>Uygunsuzluk Açıklaması:</strong> {{ $complaint->dif_description }}</p>
    @endif

    <h5>🔄 Geri Bildirim</h5>
    <p>{{ $complaint->feedback }}</p>

    <hr>

    <h5>📂 Dosyalar</h5>
    <ul>
        @foreach($complaint->files as $f)
            <li>
                <a href="{{ asset('storage/'.$f->file_path) }}" target="_blank">
                    {{ strtoupper($f->stage) }} - {{ $f->file_type }}
                </a>
            </li>
        @endforeach
    </ul>

    <h5>🛠️ Aksiyonlar</h5>
    <ul>
        @foreach($complaint->actions as $a)
            <li>{{ $a->action_description }} - {{ $a->due_date }}</li>
        @endforeach
    </ul>

    {{-- İşlem Butonları --}}
    <div class="mt-3">
        <a href="{{ route('complaints.index') }}" class="btn btn-secondary">⬅️ Geri</a>
    </div>
</div>
@endsection