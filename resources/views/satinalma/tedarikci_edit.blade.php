@extends('layouts.admin')

@section('content')
<div class="container">
    <h4>Tedarikçi Düzenle</h4>

    <form action="{{ route('suppliers.update',$supplier->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Firma Adı</label>
            <input type="text" name="name" class="form-control" value="{{ $supplier->name }}" required>
        </div>
        <div class="mb-3">
            <label>İletişim</label>
            <input type="text" name="contact" class="form-control" value="{{ $supplier->contact }}">
        </div>
        <div class="mb-3">
            <label>Adres</label>
            <input type="text" name="address" class="form-control" value="{{ $supplier->address }}">
        </div>
        <div class="mb-3">
            <label>Akreditasyon</label>
            <input type="text" name="accreditation" class="form-control" value="{{ $supplier->accreditation }}">
        </div>
        <div class="mb-3">
            <label>Durum</label>
            <select name="status" class="form-select">
                <option value="Aktif" {{ $supplier->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Pasif" {{ $supplier->status == 'Pasif' ? 'selected' : '' }}>Pasif</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Performans Puanı</label>
            <input type="number" name="performance_score" class="form-control" value="{{ $supplier->performance_score }}">
        </div>
        <div class="mb-3">
            <label>Notlar</label>
            <textarea name="notes" class="form-control">{{ $supplier->notes }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Kaydet</button>
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Geri</a>
    </form>
</div>
@endsection