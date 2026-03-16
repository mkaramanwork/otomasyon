@extends('layouts.admin')
@section('title','Sınav Atama')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-info text-white">👥 Kullanıcıya Sınav Atama</div>
        <div class="card-body">
            <form action="{{ route('exams.assign',$exam->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label>Kullanıcı Seç</label>
                    <select name="user_ids[]" class="form-select" multiple required>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Birden fazla kullanıcı seçebilirsin.</small>
                </div>
                <button type="submit" class="btn btn-success">Ata</button>
            </form>
        </div>
    </div>
</div>
@endsection