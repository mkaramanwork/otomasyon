@extends('layouts.admin')

@section('title', 'Sipariş/Teklif Düzenle')

@section('content')
<div class="row">
  <div class="col-md-10 offset-md-1">
    <div class="card card-warning card-outline">
      <div class="card-header">
        <h3 class="card-title">Sipariş/Teklif Düzenle</h3>
      </div>
      <form action="{{ route('orders.update', $order->id) }}" method="POST" id="orderForm">
        @csrf @method('PUT')
        <div class="card-body">
          <div class="form-group mb-3">
            <label>Müşteri Adı</label>
            <input type="text" name="customer_name" class="form-control" value="{{ $order->customer_name }}" required>
          </div>
          <div class="form-group mb-3">
            <label>Müşteri Email</label>
            <input type="email" name="customer_email" class="form-control" value="{{ $order->customer_email }}">
          </div>
          <div class="form-group mb-3">
            <label>Tip</label>
            <select name="type" class="form-control">
              <option value="sipariş" @selected($order->type == 'sipariş')>Sipariş</option>
              <option value="teklif" @selected($order->type == 'teklif')>Teklif</option>
            </select>
          </div>
          <div class="form-group mb-3">
            <label>Durum</label>
            <select name="status" class="form-control">
              <option value="pending" @selected($order->status == 'pending')>Beklemede</option>
              <option value="approved" @selected($order->status == 'approved')>Onaylandı</option>
              <option value="rejected" @selected($order->status == 'rejected')>Reddedildi</option>
            </select>
          </div>

          <hr>
          <h5>Ürünler</h5>
          <div id="product-list">
            @foreach($order->items as $index => $item)
              <div class="row mb-2 product-row">
                <div class="col-md-3">
                  <select name="products[{{ $index }}][id]" class="form-control product-select" onchange="updateRowTotal(this)">
                    @foreach($salesProducts as $product)
                      <option value="{{ $product->id }}" 
                              data-price-try="{{ $product->price_try }}" 
                              data-price-usd="{{ $product->price_usd }}"
                              @selected($item->sales_product_id == $product->id)>
                        {{ $product->name }} ({{ $product->price_try }} ₺ / {{ $product->price_usd }} $)
                      </option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-2">
                  <input type="number" name="products[{{ $index }}][quantity]" class="form-control quantity"
                         value="{{ $item->quantity }}" min="1" oninput="updateRowTotal(this)">
                </div>
                <div class="col-md-2">
                  <input type="number" name="products[{{ $index }}][discount]" class="form-control discount"
                         value="{{ $item->discount }}" min="0" max="100" oninput="updateRowTotal(this)">
                </div>
                <div class="col-md-2">
                  <input type="text" class="form-control row-total-try" readonly value="{{ number_format($item->total_price,2) }} ₺">
                </div>
                <div class="col-md-2">
                  <input type="text" class="form-control row-total-usd" readonly value="{{ number_format(($item->product->price_usd - ($item->product->price_usd * $item->discount / 100)) * $item->quantity,2) }} $">
                </div>
              </div>
            @endforeach
          </div>

          <hr>
          <div class="form-group mb-3">
            <label>Genel İskonto (%)</label>
            <input type="number" name="global_discount" id="global_discount" class="form-control" min="0" max="100" value="0" oninput="updateGrandTotal()">
          </div>

          <div class="form-group mb-3">
            <label>Toplam Fiyat (TL)</label>
            <input type="text" id="grand_total_try" class="form-control" readonly value="{{ number_format($order->total_price,2) }} ₺">
          </div>
          <div class="form-group mb-3">
            <label>Toplam Fiyat (USD)</label>
            <input type="text" id="grand_total_usd" class="form-control" readonly value="{{ number_format($order->total_price_usd,2) }} $">
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-warning">
            <i class="fas fa-save"></i> Güncelle
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