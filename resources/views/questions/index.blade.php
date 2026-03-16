@extends('layouts.admin')
@section('title','Soru Listesi')
@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h4>📑 Soru Listesi</h4>
        <a href="{{ route('questions.create') }}" class="btn btn-primary">Yeni Soru</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Soru</th>
                <th>Kategori</th>
                <th>Zorluk</th>
                <th>Doğru Cevap</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @forelse($questions as $q)
            <tr>
                <td>{{ $q->question_text }}</td>
                <td>{{ $q->category->name }}</td>
                <td>{{ $q->difficulty }}</td>
                <td>{{ $q->correct_option }}</td>
                <td>
                    <a href="{{ route('questions.edit',$q->id) }}" class="btn btn-warning btn-sm">Güncelle</a>
                    <form action="{{ route('questions.destroy',$q->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Silmek istediğine emin misin?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">Henüz soru yok</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection