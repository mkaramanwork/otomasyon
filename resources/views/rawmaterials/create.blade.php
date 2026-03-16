@extends('layouts.admin')

@section('title', 'Ham Madde Ekle')

@section('content')
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Yeni Ham Madde Ekle</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('raw-materials.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name">Ad</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="unit">Birim</label>
                        <input type="text" name="unit" id="unit" class="form-control" value="kg">
                    </div>

                    <div class="form-group">
                        <label for="stock_kg">Stok (kg)</label>
                        <input type="number" step="0.01" name="stock_kg" id="stock_kg" class="form-control" value="0">
                    </div>

                    <hr>
                    <h5>Fiyat Bilgileri</h5>

                    <div class="form-group">
                        <label for="unit_price_tl">Fiyat (TL)</label>
                        <input type="number" step="0.01" name="unit_price_tl" id="unit_price_tl" class="form-control" value="0">
                    </div>

                    <div class="form-group">
                        <label for="unit_price_usd">Fiyat (USD)</label>
                        <input type="number" step="0.01" name="unit_price_usd" id="unit_price_usd" class="form-control" value="0">
                    </div>

                    <div class="form-group">
                        <label for="unit_price_eur">Fiyat (EUR)</label>
                        <input type="number" step="0.01" name="unit_price_eur" id="unit_price_eur" class="form-control" value="0">
                    </div>

                    <div class="form-group">
                        <label for="currency">Aktif Para Birimi</label>
                        <select name="currency" id="currency" class="form-control">
                            <option value="TL" selected>TL</option>
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('raw-materials.index') }}" class="btn btn-secondary">Geri</a>
                        <button type="submit" class="btn btn-success">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection