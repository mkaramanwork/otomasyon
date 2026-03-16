@extends('layouts.admin')
@section('title', 'Fiziksel Reçete Düzenle')

@section('content')
<div class="container">
  <h4 class="mb-4">Fiziksel Reçeteyi Düzenle</h4>

  <form method="POST" action="{{ route('product_physical.update', $physicalFormula->id) }}">
    @csrf
    @method('PUT')

    <div class="mb-3">
      <label class="form-label">Ürün Kodu</label>
      <input type="text" name="product_code" class="form-control" value="{{ $physicalFormula->product_code }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Ürün Adı</label>
      <input type="text" name="product_name" class="form-control" value="{{ $physicalFormula->product_name }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Birim</label>
      <input type="text" name="unit" class="form-control" value="{{ $physicalFormula->unit }}">
    </div>

    <div class="mb-3">
      <label class="form-label">Açıklama</label>
      <textarea name="description" class="form-control" rows="3">{{ $physicalFormula->description }}</textarea>
    </div>

    <button type="submit" class="btn btn-success">Güncelle</button>
    <a href="{{ route('product_physical.index') }}" class="btn btn-secondary">Geri</a>
  </form>
</div>
@endsection