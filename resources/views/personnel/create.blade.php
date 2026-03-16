@extends('layouts.admin')
@section('title','Yeni Personel')
@section('content')
<div class="container">
    <h4>➕ Yeni Personel</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('personnel.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Adı</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Ünvanı</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Görevi</label>
            <input type="text" name="duty" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>İşe Başlama Tarihi</label>
            <input type="date" name="start_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Vekili</label>
            <input type="text" name="deputy" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Kaydet</button>
        <a href="{{ route('personnel.index') }}" class="btn btn-secondary">Geri</a>
    </form>
</div>
@endsection