@extends('layouts.admin')

@section('title', 'Ürünler')

@section('content')
<div class="container">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4>📦 Ürün Listesi</h4>
    <div class="d-flex gap-2">
      <a href="{{ route('sales_products.create') }}" class="btn btn-success">
        <i class="fas fa-plus"></i> Manuel Ürün Ekle
      </a>
      <form action="{{ route('sales_products.import') }}" method="POST" enctype="multipart/form-data" class="d-inline">
        @csrf
        <div class="input-group">
          <input type="file" name="file" class="form-control form-control-sm" required>
          <button type="submit" class="btn btn-info btn-sm">
            <i class="fas fa-file-excel"></i> Excel Yükle/Güncelle
          </button>
        </div>
      </form>
    </div>
  </div>

  <div class="alert alert-warning mb-4">
    <strong>Not:</strong> Excel dosyasında <code>id</code> dolu olan satırlar mevcut ürünleri günceller,
    <code>id</code> boş bırakılan satırlar ise yeni ürün olarak eklenir.
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if($products->isEmpty())
    <div class="alert alert-info">Sonuç bulunamadı.</div>
  @else
    <div class="card card-outline card-primary">
      <div class="card-body p-0">
        <table class="table table-bordered table-striped align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Ürün Adı</th>
              <th>Performans Sınıfı</th>
              <th>Hacim</th>
              <th>Fiyat (USD)</th>
              <th>Fiyat (TL)</th>
              <th class="text-center">İşlem</th>
            </tr>
          </thead>
          <tbody>
            @foreach($products as $product)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->performance_class ?? '-' }}</td>
                <td>{{ $product->volume ?? '-' }}</td>
                <td>{{ number_format($product->price_usd, 2) }} $</td>
                <td>{{ number_format($product->price_try, 2) }} ₺</td>
                <td class="text-center">
                  <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('sales_products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary">
                      <i class="fas fa-edit"></i> Düzenle
                    </a>
                    <form action="{{ route('sales_products.destroy', $product->id) }}" method="POST"
                          onsubmit="return confirm('Bu ürünü silmek istediğinize emin misiniz?')" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-trash"></i> Sil
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="card-footer text-muted">
        Toplam Ürün: {{ $products->count() }}
      </div>
    </div>
  @endif
</div>
@endsection