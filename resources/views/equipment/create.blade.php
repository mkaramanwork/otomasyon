@extends('layouts.admin')
@section('title','Yeni Cihaz')
@section('content')
<div class="container">
    <h4>➕ Yeni Cihaz Ekle</h4>

    <form action="{{ route('equipment.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Kod</label>
            <input type="text" name="tag" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Adı</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Model</label>
            <input type="text" name="model" class="form-control">
        </div>
        <div class="mb-3">
            <label>Seri No</label>
            <input type="text" name="serial_no" class="form-control">
        </div>
        <div class="mb-3">
            <label>Satın Alma Tarihi</label>
            <input type="date" name="purchase_date" class="form-control">
        </div>
        <div class="mb-3">
            <label>Kalibrasyon Tarihi</label>
            <input type="date" name="calibration_due_at" class="form-control">
        </div>
        <div class="mb-3">
            <label>Durum</label>
            <select name="status" class="form-control">
                <option value="active">Aktif</option>
                <option value="inactive">Pasif</option>
                <option value="out_of_service">Servis Dışı</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Kaydet</button>
        <a href="{{ route('equipment.index') }}" class="btn btn-secondary">Geri</a>
    </form>
</div>
@endsection