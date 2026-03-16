@extends('layouts.admin')

@section('title', 'Ham Madde Düzenle')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0">Ham Maddeyi Düzenle</h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('raw-materials.update', $rawMaterial->id) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="form-group">
                        <label for="name">Ad</label>
                        <input type="text" name="name" id="name" class="form-control"
                               value="{{ old('name', $rawMaterial->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="unit">Birim</label>
                        <input type="text" name="unit" id="unit" class="form-control"
                               value="{{ old('unit', $rawMaterial->unit) }}">
                    </div>

                    <div class="form-group">
                        <label for="stock_kg">Stok (kg)</label>
                        <input type="number" step="0.01" name="stock_kg" id="stock_kg" class="form-control"
                               value="{{ old('stock_kg', $rawMaterial->stock_kg) }}">
                    </div>

                    <hr>
                    <h5>Fiyat Bilgileri</h5>

                    <div class="form-group">
                        <label for="unit_price_tl">Fiyat (TL)</label>
                        <input type="number" step="0.01" name="unit_price_tl" id="unit_price_tl" class="form-control"
                               value="{{ old('unit_price_tl', $rawMaterial->unit_price_tl) }}">
                    </div>

                    <div class="form-group">
                        <label for="unit_price_usd">Fiyat (USD)</label>
                        <input type="number" step="0.01" name="unit_price_usd" id="unit_price_usd" class="form-control"
                               value="{{ old('unit_price_usd', $rawMaterial->unit_price_usd) }}">
                    </div>

                    <div class="form-group">
                        <label for="unit_price_eur">Fiyat (EUR)</label>
                        <input type="number" step="0.01" name="unit_price_eur" id="unit_price_eur" class="form-control"
                               value="{{ old('unit_price_eur', $rawMaterial->unit_price_eur) }}">
                    </div>

                    <div class="form-group">
                        <label for="currency">Aktif Para Birimi</label>
                        <select name="currency" id="currency" class="form-control">
                            <option value="TL" {{ old('currency', $rawMaterial->currency) == 'TL' ? 'selected' : '' }}>TL</option>
                            <option value="USD" {{ old('currency', $rawMaterial->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                            <option value="EUR" {{ old('currency', $rawMaterial->currency) == 'EUR' ? 'selected' : '' }}>EUR</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('raw-materials.index') }}" class="btn btn-secondary">Geri</a>
                        <button type="submit" class="btn btn-primary">Güncelle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection