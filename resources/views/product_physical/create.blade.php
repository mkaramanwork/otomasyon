@extends('layouts.admin')
@section('title', 'Yeni Fiziksel Reçete')

@section('content')
<div class="container">
  <h4 class="mb-4">Yeni Fiziksel Reçete Oluştur</h4>

  <form method="POST" action="{{ route('product_physical.store') }}">
    @csrf

    <div class="mb-3">
      <label class="form-label">Ürün Kodu</label>
      <input type="text" name="product_code" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Ürün Adı</label>
      <input type="text" name="product_name" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Birim</label>
      <input type="text" name="unit" class="form-control" placeholder="adet, kg, m²...">
    </div>

    <div class="mb-3">
      <label class="form-label">Açıklama</label>
      <textarea name="description" class="form-control" rows="3"></textarea>
    </div>

    <button type="submit" class="btn btn-success">Kaydet</button>
    <a href="{{ route('product_physical.index') }}" class="btn btn-secondary">Geri</a>
  </form>
</div>
@endsection