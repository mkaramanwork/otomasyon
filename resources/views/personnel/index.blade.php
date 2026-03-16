@extends('layouts.admin')
@section('title','Personel Listesi')
@section('content')
<div class="container">
    <h4>👥 Personel Listesi</h4>
    <a href="{{ route('personnel.create') }}" class="btn btn-primary mb-3">➕ Yeni Personel</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Adı</th>
                <th>Ünvanı</th>
                <th>Görevi</th>
                <th>İşe Başlama</th>
                <th>Vekili</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @foreach($personnel as $p)
            <tr>
                <td>{{ $p->name }}</td>
                <td>{{ $p->title }}</td>
                <td>{{ $p->duty }}</td>
                <td>{{ $p->start_date }}</td>
                <td>{{ $p->deputy }}</td>
                <td>
                    <a href="{{ route('personnel.show',$p->id) }}" class="btn btn-info btn-sm">Detay</a>
                    <a href="{{ route('personnel.edit',$p->id) }}" class="btn btn-warning btn-sm">Güncelle</a>
                    <form action="{{ route('personnel.destroy',$p->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection