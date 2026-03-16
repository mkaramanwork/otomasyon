@extends('layouts.admin')
@section('title','Cihaz Güncelle')
@section('content')
<div class="container">
    <h4>✏️ Cihaz Güncelle</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('equipment.update',$equipment->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Kod</label>
            <input type="text" name="tag" class="form-control" value="{{ old('tag',$equipment->tag) }}" required>
        </div>

        <div class="mb-3">
            <label>Adı</label>
            <input type="text" name="name" class="form-control" value="{{ old('name',$equipment->name) }}" required>
        </div>

        <div class="mb-3">
            <label>Model</label>
            <input type="text" name="model" class="form-control" value="{{ old('model',$equipment->model) }}">
        </div>

        <div class="mb-3">
            <label>Seri No</label>
            <input type="text" name="serial_no" class="form-control" value="{{ old('serial_no',$equipment->serial_no) }}">
        </div>

        <div class="mb-3">
            <label>Satın Alma Tarihi</label>
            <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date',$equipment->purchase_date) }}">
        </div>

        <div class="mb-3">
            <label>Kalibrasyon Tarihi</label>
            <input type="date" name="calibration_due_at" class="form-control" value="{{ old('calibration_due_at',$equipment->calibration_due_at) }}">
        </div>

        <div class="mb-3">
            <label>Durum</label>
            <select name="status" class="form-control">
                <option value="active" {{ $equipment->status=='active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ $equipment->status=='inactive' ? 'selected' : '' }}>Pasif</option>
                <option value="out_of_service" {{ $equipment->status=='out_of_service' ? 'selected' : '' }}>Servis Dışı</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Güncelle</button>
        <a href="{{ route('equipment.index') }}" class="btn btn-secondary">Geri</a>
    </form>
</div>
@endsection