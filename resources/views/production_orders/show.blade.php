@extends('layouts.admin')

@section('title', 'İş Emri Detayı')

@section('content')
<div class="container">
  <h4 class="mb-4">İş Emri: {{ $productionOrder->batch_number }}</h4>

  <div class="row mb-4">
    <div class="col-md-6">
      <strong>Müşteri:</strong> {{ $productionOrder->customer->name ?? 'Tanımsız' }}<br>
      <strong>Ürün:</strong> {{ $productionOrder->product->name ?? 'Tanımsız' }}<br>
      <strong>Formül:</strong> {{ $productionOrder->formula->name ?? 'Tanımsız' }}<br>
      <strong>Üretim Hattı:</strong> {{ $productionOrder->line->name ?? 'Tanımsız' }}<br>
      <strong>Planlanan Başlangıç:</strong> {{ optional($productionOrder->planned_at)->format('d.m.Y H:i') }}<br>
      <strong>Planlanan Bitiş:</strong> {{ optional($productionOrder->planned_end_at)->format('d.m.Y H:i') }}<br>
      @if($productionOrder->planned_at && $productionOrder->planned_end_at)
        @php
          $days = $productionOrder->planned_at->diffInDays($productionOrder->planned_end_at);
        @endphp
        <strong>Planlanan Gün Sayısı:</strong> {{ $days }} gün<br>
      @endif
      <strong>Planlanan Miktar:</strong> {{ number_format($productionOrder->quantity_kg, 2) }} kg<br>
      <strong>Durum:</strong>
      <span class="badge bg-{{ match($productionOrder->status) {
        'planned' => 'secondary',
        'in_production' => 'primary',
        'produced' => 'success',
        'in_filling' => 'warning text-dark',
        'filled' => 'info text-dark',
        'cancelled' => 'danger',
         'addition' => 'dark',
        default => 'secondary',
      } }}">
        {{ match($productionOrder->status) {
          'planned' => 'Planlandı',
          'in_production' => 'Üretimde',
          'produced' => 'Üretim Tamam',
          'in_filling' => 'Dolumda',
          'filled' => 'Dolum Tamam',
          'cancelled' => 'İptal',
            'addition' => 'İlave Alındı',
          default => 'Tanımsız',
        } }}
      </span>
    </div>
    <div class="col-md-6 text-end">
      <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($productionOrder->batch_number) }}&size=120x120" alt="QR Kod">
      <div class="mt-2"><small>Batch QR</small></div>
    </div>
  </div>

  <hr>

  <h5>Formül İçeriği</h5>
  <form method="POST" action="{{ route('production_orders.complete', $productionOrder->id) }}">
    @csrf
    <table class="table table-bordered">
      <thead class="table-light">
        <tr>
          <th>✔</th>
          <th>Ham Madde</th>
          <th>Yüzde (%)</th>
          <th>Hesaplanan Miktar (kg)</th>
          <th>Birim</th>
        </tr>
      </thead>
      <tbody>
        @foreach($productionOrder->formula->details as $detail)
          @php
            $calculated = ($detail->percentage / 100) * $productionOrder->quantity_kg;
          @endphp
          <tr>
            <td><input type="checkbox" name="completed_materials[]" value="{{ $detail->id }}"></td>
            <td>{{ $detail->rawMaterial->name ?? 'Tanımsız' }}</td>
            <td>{{ number_format($detail->percentage, 2) }}</td>
            <td>{{ number_format($calculated, 2) }}</td>
            <td>{{ $detail->unit ?? '-' }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <div class="mt-4 text-end">
      <button type="submit" class="btn btn-success">Üretimi Bitir</button>
    </div>
  </form>

  @if($relatedOrders->isNotEmpty())
    <hr>
    <h5 class="mt-4">🔗 Aynı Batch Numarasına Sahip Diğer Üretimler</h5>
    <ul class="list-group mb-4">
      @foreach($relatedOrders as $order)
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <div>
            <strong>{{ $order->product->name }}</strong> – {{ $order->customer->name }}
            <br><small>{{ $order->planned_at->format('d.m.Y H:i') }} | Hat: {{ $order->line->name }}</small>
          </div>
          <a href="{{ route('production_orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">Görüntüle</a>
        </li>
      @endforeach
    </ul>
  @endif

  @if($productionOrder->logs->isNotEmpty())
    <hr>
    <h5 class="mt-4">📜 Üretim Geçmişi</h5>
    <ul class="list-group">
      @foreach($productionOrder->logs->sortByDesc('logged_at') as $log)
        <li class="list-group-item">
          <strong>{{ $log->event }}</strong> – {{ $log->description }}
          <br>
          <small>
            Log Zamanı: {{ optional($log->logged_at)->format('d.m.Y H:i') }} |
            Oluşturulma: {{ optional($log->created_at)->format('d.m.Y H:i') }} |
            Kullanıcı: {{ $log->user->name ?? 'Sistem' }}
          </small>
        </li>
      @endforeach
    </ul>
  @endif

  <div class="mt-4">
    <a href="{{ route('production_orders.index') }}" class="btn btn-secondary">Geri Dön</a>
  </div>
</div>
@endsection
