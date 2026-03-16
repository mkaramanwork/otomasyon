@extends('layouts.admin')

@section('title','Ürün Sicil Kartları')

@section('content')
<div class="container mt-4">
    <h3>📦 Ürün Sicil Kartları</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>#</th>
                <th>Ürün Adı</th>
                <th>Kod</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $index => $product)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->code }}</td>
                    <td>
                        <a href="{{ route('urunAgaci.show',$product->id) }}" class="btn btn-primary btn-sm">
                            Detay Göster
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection