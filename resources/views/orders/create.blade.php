@extends('layouts.admin')

@section('title', 'Yeni Sipariş/Teklif')

@section('content')
<div class="row">
  <div class="col-md-10 offset-md-1">
    <div class="card card-primary card-outline">
      <div class="card-header">
        <h3 class="card-title">Yeni Sipariş/Teklif</h3>
      </div>
      <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
        @csrf
        <div class="card-body">
          <div class="form-group mb-3">
            <label>Müşteri Adı</label>
            <input type="text" name="customer_name" class="form-control" required>
          </div>
          <div class="form-group mb-3">
            <label>Müşteri Email</label>
            <input type="email" name="customer_email" class="form-control">
          </div>
          <div class="form-group mb-3">
            <label>Tip</label>
            <select name="type" class="form-control">
              <option value="sipariş">Sipariş</option>
              <option value="teklif">Teklif</option>
            </select>
          </div>
          <div class="form-group mb-3">
            <label>Durum</label>
            <select name="status" class="form-control">
              <option value="pending">Beklemede</option>
              <option value="approved">Onaylandı</option>
              <option value="rejected">Reddedildi</option>
            </select>
          </div>

          <hr>
          <h5>Ürünler</h5>
          <div id="product-list">
            <div class="row mb-2 product-row">
              <div class="col-md-3">
                <select name="products[0][id]" class="form-control product-select" onchange="updateRowTotal(this)">
                  @foreach($salesProducts as $product)
                    <option value="{{ $product->id }}" 
                            data-price-try="{{ $product->price_try }}" 
                            data-price-usd="{{ $product->price_usd }}">
                      {{ $product->name }} ({{ $product->price_try }} ₺ / {{ $product->price_usd }} $)
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <input type="number" name="products[0][quantity]" class="form-control quantity" min="1" value="1" oninput="updateRowTotal(this)">
              </div>
              <div class="col-md-2">
                <input type="number" name="products[0][discount]" class="form-control discount" min="0" max="100" value="0" oninput="updateRowTotal(this)">
              </div>
              <div class="col-md-2">
                <input type="text" class="form-control row-total-try" readonly value="0 ₺">
              </div>
              <div class="col-md-2">
                <input type="text" class="form-control row-total-usd" readonly value="0 $">
              </div>
            </div>
          </div>

          <button type="button" class="btn btn-sm btn-outline-info" onclick="addProductRow()">+ Ürün Satırı Ekle</button>

          <hr>
          <div class="form-group mb-3">
            <label>Genel İskonto (%)</label>
            <input type="number" name="global_discount" id="global_discount" class="form-control" min="0" max="100" value="0" oninput="updateGrandTotal()">
          </div>

          <div class="form-group mb-3">
            <label>Toplam Fiyat (TL)</label>
            <input type="text" id="grand_total_try" class="form-control" readonly value="0 ₺">
          </div>
          <div class="form-group mb-3">
            <label>Toplam Fiyat (USD)</label>
            <input type="text" id="grand_total_usd" class="form-control" readonly value="0 $">
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Kaydet
          </button>
          <a href="{{ route('orders.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri
          </a>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
let productIndex = 1;

function addProductRow() {
  const container = document.getElementById('product-list');
  const row = document.createElement('div');
  row.classList.add('row','mb-2','product-row');
  row.innerHTML = `
    <div class="col-md-3">
      <select name="products[${productIndex}][id]" class="form-control product-select" onchange="updateRowTotal(this)">
        @foreach($salesProducts as $product)
          <option value="{{ $product->id }}" 
                  data-price-try="{{ $product->price_try }}" 
                  data-price-usd="{{ $product->price_usd }}">
            {{ $product->name }} ({{ $product->price_try }} ₺ / {{ $product->price_usd }} $)
          </option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2">
      <input type="number" name="products[${productIndex}][quantity]" class="form-control quantity" min="1" value="1" oninput="updateRowTotal(this)">
    </div>
    <div class="col-md-2">
      <input type="number" name="products[${productIndex}][discount]" class="form-control discount" min="0" max="100" value="0" oninput="updateRowTotal(this)">
    </div>
    <div class="col-md-2">
      <input type="text" class="form-control row-total-try" readonly value="0 ₺">
    </div>
    <div class="col-md-2">
      <input type="text" class="form-control row-total-usd" readonly value="0 $">
    </div>
  `;
  container.appendChild(row);
  productIndex++;
  updateGrandTotal();
}

function updateRowTotal(el) {
  const row = el.closest('.product-row');
  const select = row.querySelector('.product-select');
  const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
  const discount = parseFloat(row.querySelector('.discount').value) || 0;
  const unitPriceTry = parseFloat(select.options[select.selectedIndex].dataset.priceTry) || 0;
  const unitPriceUsd = parseFloat(select.options[select.selectedIndex].dataset.priceUsd) || 0;

  const discountedPriceTry = unitPriceTry - (unitPriceTry * discount / 100);
  const discountedPriceUsd = unitPriceUsd - (unitPriceUsd * discount / 100);

  const lineTotalTry = discountedPriceTry * quantity;
  const lineTotalUsd = discountedPriceUsd * quantity;

  row.querySelector('.row-total-try').value = lineTotalTry.toFixed(2) + ' ₺';
  row.querySelector('.row-total-usd').value = lineTotalUsd.toFixed(2) + ' $';

  updateGrandTotal();
}

function updateGrandTotal() {
  let totalTry = 0;
  let totalUsd = 0;
  document.querySelectorAll('.row-total-try').forEach(input => {
    totalTry += parseFloat(input.value) || 0;
  });
  document.querySelectorAll('.row-total-usd').forEach(input => {
    totalUsd += parseFloat(input.value) || 0;
  });

  const globalDiscount = parseFloat(document.getElementById('global_discount').value) || 0;
  const finalTotalTry = totalTry - (totalTry * globalDiscount / 100);
  const finalTotalUsd = totalUsd - (totalUsd * globalDiscount / 100);

  document.getElementById('grand_total_try').value = finalTotalTry.toFixed(2) + ' ₺';
  document.getElementById('grand_total_usd').value = finalTotalUsd.toFixed(2) + ' $';
}
</script>
@endsection