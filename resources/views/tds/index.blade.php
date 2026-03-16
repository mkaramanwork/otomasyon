@extends('layouts.admin')

@section('title','TDS Listesi')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>📑 TDS Listesi</h4>
        <div>
            <a href="{{ route('tds.create') }}" class="btn btn-success">➕ Yeni TDS</a>
            <a href="{{ route('tds.printAll') }}" class="btn btn-primary">🖨️ Tüm TDS’leri Yazdır</a>
            <a href="{{ route('tds.karamanAl') }}" class="btn btn-danger">🤖 Karaman_Al Sor</a>
        </div>
    </div>


    <table class="table table-striped table-hover table-bordered">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Ürün Adı</th>
                <th>Ürün Kodu</th>
                <th>Açıklama</th>
                <th>Oluşturulma</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @foreach($documents as $doc)
            <tr>
                <td>{{ $doc->id }}</td>
                <td>{{ $doc->product_name }}</td>
                <td>{{ $doc->product_code }}</td>
                <td>{{ Str::limit($doc->description,50) }}</td>
                <td>{{ $doc->created_at->format('d.m.Y H:i') }}</td>
                <td>
                    <a href="{{ route('tds.show',$doc->id) }}" class="btn btn-info btn-sm">🔍 Detay</a>
                    <a href="{{ route('tds.edit',$doc->id) }}" class="btn btn-warning btn-sm">✏️ Düzenle</a>
                    <a href="{{ route('tds.exportWord',$doc->id) }}" class="btn btn-primary btn-sm">📥 Word</a>
                    <form action="{{ route('tds.destroy',$doc->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Silmek istediğine emin misin?')">🗑️ Sil</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection