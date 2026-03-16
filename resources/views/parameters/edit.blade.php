@extends('layouts.admin')

@section('title', 'Parametre Düzenle')

@section('content')
<div class="container">
    <h4>Parametre Düzenle</h4>
    <form action="{{ route('parameters.update', $parameter->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Numune Türü</label>
            <select name="sample_type" class="form-control" required>
                <option value="madeni yağ" {{ $parameter->sample_type == 'madeni yağ' ? 'selected' : '' }}>Madeni Yağ</option>
                <option value="antifriz" {{ $parameter->sample_type == 'antifriz' ? 'selected' : '' }}>Antifriz</option>
                <option value="gres" {{ $parameter->sample_type == 'gres' ? 'selected' : '' }}>Gres</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Parametre</label>
            <input type="text" name="parameter" class="form-control" value="{{ $parameter->parameter }}" required>
        </div>
        <div class="mb-3">
            <label>Metod (TR)</label>
            <input type="text" name="method_tr" class="form-control" value="{{ $parameter->method_tr }}" required>
        </div>
        <div class="mb-3">
            <label>Metod (EN)</label>
            <input type="text" name="method_en" class="form-control" value="{{ $parameter->method_en }}" required>
        </div>
        <div class="mb-3">
            <label>Metod (RU)</label>
            <input type="text" name="method_ru" class="form-control" value="{{ $parameter->method_ru }}" required>
        </div>
        <div class="mb-3">
            <label>Birim</label>
            <input type="text" name="unit" class="form-control" value="{{ $parameter->unit }}" required>
        </div>
        <div class="mb-3">
            <label>Kapsam</label>
            <select name="scope" class="form-control" required>
                <option value="TÜRKAK" {{ $parameter->scope == 'TÜRKAK' ? 'selected' : '' }}>TÜRKAK</option>
                <option value="Kapsam Dışı" {{ $parameter->scope == 'Kapsam Dışı' ? 'selected' : '' }}>Kapsam Dışı</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Belirsizlik</label>
            <input type="text" name="uncertainty" class="form-control" value="{{ $parameter->uncertainty }}" required>
        </div>
        <button type="submit" class="btn btn-success">Güncelle</button>
    </form>
</div>
@endsection