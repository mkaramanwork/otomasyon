@extends('layouts.admin')

@section('title', 'Ürün Raporu')

@section('content')
<div class="container">
  <h4 class="mb-4">📊 Ürün Raporlama Sayfası</h4>

  <form method="GET" action="{{ route('reports.product') }}" class="row g-3 mb-4">
    <div class="col-md-4">
      <label class="form-label">Ürün Seç</label>
      <select name="product_id" class="form-select">
        <option value="">Tümü</option>
        @foreach(App\Models\Product::all() as $product)
          <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
            {{ $product->name }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">Tarih Seç</label>
      <input type="date" name="date" value="{{ request('date') }}" class="form-control">
    </div>
    <div class="col-md-4 d-flex align-items-end">
      <button type="submit" class="btn btn-primary w-100">Raporla</button>
    </div>
  </form>

  @forelse($orders as $order)
    <div class="card mb-4 shadow-sm">
      <div class="card-header bg-light">
        <strong>{{ $order->product->name }}</strong>
        <span class="text-muted">— Batch: {{ $order->batch_number }}</span>
      </div>
      <div class="card-body">
        <h6 class="text-primary">🗓️ Planlama</h6>
        <p><strong>Tarih:</strong> {{ $order->planned_at->format('d.m.Y H:i') }}</p>
        <p><strong>Planlayan:</strong> {{ $order->creator->name ?? '-' }}</p>

        <h6 class="text-success mt-3">🧪 Reçeteler</h6>
        <p><strong>Üretim Reçetesi:</strong> {{ $order->formula->name ?? '-' }}</p>
        <p><strong>Dolum Reçetesi:</strong> {{ $order->fillingRecipe->name ?? '-' }}</p>
        <p><strong>Ambalaj Reçetesi:</strong> {{ $order->packagingRecipe->name ?? '-' }}</p>

        <h6 class="text-warning mt-3">🧱 Ham Madde Kullanımı</h6>
        <ul>
          @foreach($order->productionFormulaDetails as $detail)
            <li>{{ $detail->formulaDetail->rawMaterial->name ?? '-' }} →
              {{ $detail->calculated_amount }} {{ $detail->formulaDetail->rawMaterial->unit ?? '' }}
            </li>
          @endforeach
        </ul>

        <h6 class="text-info mt-3">🏭 Üretim Süreci</h6>
        <p><strong>Gönderim:</strong> {{ $order->sent_to_production_at?->format('d.m.Y H:i') ?? '-' }}</p>
        <p><strong>Tamamlanma:</strong> {{ $order->completed_at?->format('d.m.Y H:i') ?? '-' }}</p>
        <p><strong>Üretim Miktarı:</strong> {{ $order->produced_quantity }} {{ $order->product->unit }}</p>

        <h6 class="text-secondary mt-3">🧃 Dolum & Ambalaj</h6>
        <p><strong>Doldurulan Adet:</strong> {{ $order->fillingRecords->sum('filled_quantity') }}</p>
        <ul>
          @foreach($order->packagingRecords as $pack)
            <li>{{ $pack->package_type }} → {{ $pack->used_count }} adet</li>
          @endforeach
        </ul>

        <h6 class="text-danger mt-3">📉 Fire & Verim</h6>
        <p><strong>Fire:</strong> {{ $order->waste_quantity ?? '0' }} {{ $order->product->unit }}</p>
        <p><strong>Verim:</strong>
          <span class="badge bg-{{ $order->efficiency_rate >= 90 ? 'success' : ($order->efficiency_rate >= 75 ? 'warning' : 'danger') }}">
            {{ $order->efficiency_rate }}%
          </span>
        </p>

        <h6 class="text-dark mt-3">🔬 Test Sonuçları</h6>
        <ul>
          @foreach($order->testResults as $test)
            <li>{{ $test->name }} →
              <span class="badge bg-{{ $test->result == 'geçti' ? 'success' : ($test->result == 'uyarı' ? 'warning' : 'danger') }}">
                {{ $test->result }}
              </span>
            </li>
          @endforeach
        </ul>
      </div>
    </div>
  @empty
    <div class="alert alert-info">Seçilen kriterlere göre rapor bulunamadı.</div>
  @endforelse
</div>
@endsection