@extends('layouts.admin')

@section('title', 'Ürün Düzenle')

@section('content')
<div class="container">
  <h4 class="mb-4">✏️ Ürün Düzenle – {{ $product->name }}</h4>

  <form action="{{ route('products.update', $product->id) }}" method="POST" class="row g-3">
    @csrf
    @method('PUT')

    <div class="col-md-6">
      <label for="name" class="form-label">Ürün Adı</label>
      <input type="text" name="name" id="name" class="form-control" value="{{ $product->name }}" required>
    </div>

    <div class="col-md-6">
      <label for="code" class="form-label">Ürün Kodu</label>
      <input type="text" name="code" id="code" class="form-control" value="{{ $product->code }}" required>
    </div>

    <div class="col-md-4">
      <label for="unit" class="form-label">Birim</label>
      <select name="unit" id="unit" class="form-select" required>
        <option value="kg" @selected($product->unit === 'kg')>kg</option>
        <option value="lt" @selected($product->unit === 'lt')>lt</option>
        <option value="adet" @selected($product->unit === 'adet')>adet</option>
      </select>
    </div>

    <div class="col-12 text-end">
      <button type="submit" class="btn btn-primary">Güncelle</button>
      <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Geri</a>
    </div>
  </form>
</div>
@endsection