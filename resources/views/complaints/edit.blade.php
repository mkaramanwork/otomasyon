@extends('layouts.admin')

@section('title','Şikayet Düzenleme')

@section('content')
<div class="container mt-4">
    <h3>✍️ Şikayet Düzenleme: {{ $complaint->complaint_no }}</h3>

    <form method="POST" action="{{ route('complaints.update',$complaint->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Konu</label>
            <input type="text" name="subject" value="{{ $complaint->subject }}" class="form-control">
        </div>

        <div class="mb-3">
            <label>Tanım</label>
            <textarea name="description" class="form-control" rows="4">{{ $complaint->description }}</textarea>
        </div>

        <div class="mb-3">
            <label>Sahibi</label>
            <input type="text" name="owner" value="{{ $complaint->owner }}" class="form-control">
        </div>

        <div class="mb-3">
            <label>Telefon</label>
            <input type="text" name="phone" value="{{ $complaint->phone }}" class="form-control">
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" value="{{ $complaint->email }}" class="form-control">
        </div>

        <div class="mb-3">
            <label>Şikayeti Alan</label>
            <input type="text" name="received_by" value="{{ $complaint->received_by }}" class="form-control">
        </div>

        <div class="mb-3">
            <label>Ürün Seçimi</label>
            <select name="product_id" class="form-control">
                @foreach($products as $p)
                    <option value="{{ $p->id }}" @if($complaint->product_id==$p->id) selected @endif>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Değerlendirme</label>
            <textarea name="evaluation" class="form-control" rows="4">{{ $complaint->evaluation }}</textarea>
        </div>

        <div class="mb-3">
            <label>Sonuç</label>
            <textarea name="result" class="form-control" rows="4">{{ $complaint->result }}</textarea>
        </div>

        <div class="mb-3">
            <label>DIF Açılacak mı?</label>
            <select name="dif_opened" class="form-control">
                <option value="hayir" @if($complaint->dif_opened=='hayir') selected @endif>Hayır</option>
                <option value="evet" @if($complaint->dif_opened=='evet') selected @endif>Evet</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Uygunsuzluk Açıklaması</label>
            <textarea name="dif_description" class="form-control" rows="3">{{ $complaint->dif_description }}</textarea>
        </div>

        <div class="mb-3">
            <label>Geri Bildirim</label>
            <textarea name="feedback" class="form-control" rows="3">{{ $complaint->feedback }}</textarea>
        </div>

        <div class="mb-3">
            <label>Yeni Dosya Ekle (Analiz Raporu / Görsel)</label>
            <input type="file" name="files[]" multiple class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Güncelle</button>
        <a href="{{ route('complaints.index') }}" class="btn btn-secondary">⬅️ Geri</a>
    </form>
</div>
@endsection