@extends('layouts.admin')
@section('title','Personel Güncelle')
@section('content')
<div class="container">
    <h4>✏️ Personel Güncelle</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('personnel.update',$personnel->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Adı</label>
            <input type="text" name="name" class="form-control" value="{{ old('name',$personnel->name) }}" required>
        </div>
        <div class="mb-3">
            <label>Ünvanı</label>
            <input type="text" name="title" class="form-control" value="{{ old('title',$personnel->title) }}" required>
        </div>
        <div class="mb-3">
            <label>Görevi</label>
            <input type="text" name="duty" class="form-control" value="{{ old('duty',$personnel->duty) }}" required>
        </div>
        <div class="mb-3">
            <label>İşe Başlama Tarihi</label>
            <input type="date" name="start_date" class="form-control" value="{{ old('start_date',$personnel->start_date) }}" required>
        </div>
        <div class="mb-3">
            <label>Vekili</label>
            <input type="text" name="deputy" class="form-control" value="{{ old('deputy',$personnel->deputy) }}">
        </div>
        <button type="submit" class="btn btn-success">Güncelle</button>
        <a href="{{ route('personnel.index') }}" class="btn btn-secondary">Geri</a>
    </form>
</div>
@endsection