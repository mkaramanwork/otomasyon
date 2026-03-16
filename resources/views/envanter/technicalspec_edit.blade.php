@extends('layouts.admin')

@section('content')
<div class="container">
    <h4>Teknik Şartname Düzenle</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('technical-spec.update',$spec->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Başlık</label>
            <input type="text" name="spec_title" class="form-control" value="{{ $spec->spec_title }}" required>
        </div>

        <div class="mb-3">
            <label>Detay</label>
            <textarea name="spec_detail" class="form-control">{{ $spec->spec_detail }}</textarea>
        </div>

        <div class="mb-3">
            <label>Standartlar</label>
            <input type="text" name="standards" class="form-control" value="{{ $spec->standards }}">
        </div>

        <div class="mb-3">
            <label>Uygunluk</label>
            <input type="text" name="compliance" class="form-control" value="{{ $spec->compliance }}">
        </div>

        <div class="mb-3">
            <label>Doküman Kodu</label>
            <input type="text" name="document_code" class="form-control" value="{{ $spec->document_code }}">
        </div>

        <button type="submit" class="btn btn-success">Güncelle</button>
        <a href="{{ route('inventories.show',$spec->inventory_id) }}" class="btn btn-secondary">Geri</a>
    </form>
</div>
@endsection