{{-- resources/views/sales_products/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Ürün Düzenle')

@section('content')
<div class="row">
  <div class="col-md-8 offset-md-2">
    <div class="card card-warning card-outline">
      <div class="card-header">
        <h3 class="card-title">Ürün Düzenle</h3>
      </div>
      <form action="{{ route('sales_products.update', $salesProduct->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
          <div class="form-group mb-3">
            <label>Ürün Adı</label>
            <input type="text" name="name" class="form-control" value="{{ $salesProduct->name }}" required>
          </div>
          <div class="form-group mb-3">
            <label>Performans Sınıfı</label>
            <input type="text" name="performance_class" class="form-control" value="{{ $salesProduct->performance_class }}">
          </div>
          <div class="form-group mb-3">
            <label>Hacim</label>
            <input type="number" step="0.01" name="volume" class="form-control" value="{{ $salesProduct->volume }}">
          </div>
          <div class="form-group mb-3">
            <label>Fiyat (USD)</label>
            <input type="number" step="0.01" name="price_usd" class="form-control" value="{{ $salesProduct->price_usd }}" required>
          </div>
          <div class="form-group mb-3">
            <label>Fiyat (TL)</label>
            <input type="number" step="0.01" name="price_try" class="form-control" value="{{ $salesProduct->price_try }}" required>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-sync-alt"></i> Güncelle
          </button>
          <a href="{{ route('sales_products.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri
          </a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection