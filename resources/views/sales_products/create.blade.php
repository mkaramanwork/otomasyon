{{-- resources/views/sales_products/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Yeni Ürün Ekle')

@section('content')
<div class="row">
  <div class="col-md-8 offset-md-2">
    <div class="card card-primary card-outline">
      <div class="card-header">
        <h3 class="card-title">Yeni Ürün Ekle</h3>
      </div>
      <form action="{{ route('sales_products.store') }}" method="POST">
        @csrf
        <div class="card-body">
          <div class="form-group mb-3">
            <label>Ürün Adı</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="form-group mb-3">
            <label>Performans Sınıfı</label>
            <input type="text" name="performance_class" class="form-control">
          </div>
          <div class="form-group mb-3">
            <label>Hacim</label>
            <input type="number" step="0.01" name="volume" class="form-control">
          </div>
          <div class="form-group mb-3">
            <label>Fiyat (USD)</label>
            <input type="number" step="0.01" name="price_usd" class="form-control" required>
          </div>
          <div class="form-group mb-3">
            <label>Fiyat (TL)</label>
            <input type="number" step="0.01" name="price_try" class="form-control" required>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Kaydet
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