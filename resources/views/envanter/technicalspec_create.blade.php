@extends('layouts.admin')

@section('content')
<div class="container">
    <h4>Teknik Şartname Ekle ({{ $inventory->product_name }})</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('technical-spec.store',$inventory->id) }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Başlık</label>
            <input type="text" name="spec_title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Detay</label>
            <textarea name="spec_detail" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label>Standartlar</label>
            <input type="text" name="standards" class="form-control">
        </div>

        <div class="mb-3">
            <label>Uygunluk</label>
            <input type="text" name="compliance" class="form-control">
        </div>

        <div class="mb-3">
            <label>Doküman Kodu</label>
            <input type="text" name="document_code" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">Kaydet</button>
        <a href="{{ route('inventories.show',$inventory->id) }}" class="btn btn-secondary">Geri</a>
    </form>
</div>
@endsection