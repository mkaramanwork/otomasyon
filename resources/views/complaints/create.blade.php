@extends('layouts.app')

@section('content')
<h3>Yeni Şikayet / Öneri Kaydı</h3>

<form method="POST" action="{{ route('complaints.store') }}" enctype="multipart/form-data">
    @csrf
    <input type="text" name="subject" placeholder="Şikayet Konusu" class="form-control mb-2">
    <textarea name="description" placeholder="Tanım" class="form-control mb-2"></textarea>
    <input type="text" name="owner" placeholder="Firma/Kişi" class="form-control mb-2">
    <input type="text" name="phone" placeholder="Telefon" class="form-control mb-2">
    <input type="email" name="email" placeholder="Email" class="form-control mb-2">
    <input type="text" name="received_by" placeholder="Şikayeti Alan" class="form-control mb-2">

    <select name="product_id" class="form-control mb-2">
        @foreach($products as $p)
            <option value="{{ $p->id }}">{{ $p->name }}</option>
        @endforeach
    </select>

    <input type="file" name="files[]" multiple class="form-control mb-2">

    <button type="submit" class="btn btn-success">Kaydet</button>
</form>
@endsection