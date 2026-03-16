@extends('layouts.admin')

@section('title', 'Parametre Ekle')

@section('content')
<div class="container">
    <h4>Yeni Parametre Ekle</h4>
    <form action="{{ route('parameters.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Numune Türü</label>
            <select name="sample_type" class="form-control" required>
                <option value="madeni yağ">Madeni Yağ</option>
                <option value="antifriz">Antifriz</option>
                <option value="gres">Gres</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Parametre</label>
            <input type="text" name="parameter" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Metod (TR)</label>
            <input type="text" name="method_tr" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Metod (EN)</label>
            <input type="text" name="method_en" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Metod (RU)</label>
            <input type="text" name="method_ru" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Birim</label>
            <input type="text" name="unit" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Kapsam</label>
            <select name="scope" class="form-control" required>
                <option value="TÜRKAK">TÜRKAK</option>
                <option value="Kapsam Dışı">Kapsam Dışı</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Belirsizlik</label>
            <input type="text" name="uncertainty" class="form-control" placeholder="Örn: ±0.5 pH" required>
        </div>
        <button type="submit" class="btn btn-success">Kaydet</button>
    </form>
</div>
@endsection