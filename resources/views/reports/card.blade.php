@extends('layouts.admin')

@section('title', $product->name.' Stok Kartı')

@section('content')
<div class="container">
    <h4>📦 {{ $product->name }} Stok Kartı</h4>
    <p>Güncel Stok: <strong>{{ $stock }}</strong> {{ $product->unit ?? '' }}</p>

    {{-- Sayım Formu --}}
    <form action="{{ route('stocks.adjustment',$product->id) }}" method="POST" class="mb-3">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <input type="number" step="0.01" name="counted_qty" class="form-control" placeholder="Sayım sonucu">
            </div>
            <div class="col-md-2">
                <button class="btn btn-warning">Sayım Kaydet</button>
            </div>
        </div>
    </form>

    {{-- Hareketler Tablosu --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tarih</th>
                <th>Tip</th>
                <th>Miktar</th>
                <th>Referans</th>
                <th>Kullanıcı</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movements as $m)
            <tr>
                <td>{{ $m->created_at->format('d.m.Y H:i') }}</td>
                <td>{{ strtoupper($m->type) }}</td>
                <td>{{ $m->amount }}</td>
                <td>{{ $m->reference ?? '-' }}</td>
                <td>{{ $m->triggeredBy->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection