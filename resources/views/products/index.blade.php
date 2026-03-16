@extends('layouts.admin')

@section('title', 'Ürün Sicil Kartları')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4>📦 Ürün Sicil Kartları</h4>
  </div>

  @if(session('success'))
    <div class="alert alert-success" id="successMessage">
      {{ session('success') }}
    </div>
    <script>
      setTimeout(function() {
        @if(session('new_product_id'))
          window.location.href = "{{ route('urunAgaci.show', session('new_product_id')) }}";
        @endif
      }, 1000);
    </script>
  @endif

  <!-- Accordion: Ürün Ekleme Formu -->
  <div class="accordion mb-4" id="urunAccordion">
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingForm">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseForm">
          ➕ Yeni Ürün Ekle
        </button>
      </h2>
      <div id="collapseForm" class="accordion-collapse collapse" data-bs-parent="#urunAccordion">
        <div class="accordion-body">
          <form action="{{ route('products.store') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label>Ürün Adı</label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Ürün Kodu</label>
              <input type="text" name="code" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Birim</label>
              <select name="unit" class="form-control" required>
                <option value="kg">kg</option>
                <option value="lt">lt</option>
                <option value="adet">adet</option>
              </select>
            </div>
            <button type="submit" class="btn btn-primary">Kaydet</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Ürün Listesi -->
  @if($products->isEmpty())
    <div class="alert alert-info">Sonuç bulunamadı.</div>
  @else
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Ad</th>
          <th>Kod</th>
          <th>Birim</th>
          <th>Ekleyen</th>
          <th>Eklenme Tarihi</th>
          <th class="text-center">İşlem</th>
        </tr>
      </thead>
      <tbody>
        @foreach($products as $product)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $product->name }}</td>
            <td>{{ $product->code }}</td>
            <td>{{ $product->unit }}</td>
            <td>{{ $product->addedBy->name ?? '-' }}</td>
            <td>{{ $product->created_at->format('d.m.Y H:i') }}</td>
            <td class="text-center">
              <div class="d-flex justify-content-center gap-2">
                <a href="{{ route('urunAgaci.show', $product->id) }}" class="btn btn-sm btn-outline-primary">Detay</a>
                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-secondary">Düzenle</a>
                <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Bu ürünü silmek istediğinize emin misiniz?')" style="display:inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">Sil</button>
                </form>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif
</div>
@endsection