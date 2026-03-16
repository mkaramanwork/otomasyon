{{-- resources/views/tv/show.blade.php --}}
@extends('layouts.tv')

@section('title', 'Üretim Takip Paneli')

@section('content')
<div class="container-fluid">
  <h2 class="text-center my-4">Üretim Durumları</h2>

  <table class="table table-bordered table-striped table-hover text-center">
    <thead class="table-dark">
      <tr>
        <th>Batch No</th>
        <th>Ürün</th>
        <th>Müşteri</th>
        <th>Üretim Hattı</th>
        <th>Dolum Hattı</th>
        <th>Dolum Miktarı</th>
        <th>Plan</th>
        <th>Miktar</th>
        <th>Durum</th>
        <th>Güncelleme</th>
      </tr>
    </thead>
    <tbody>
      @foreach($orders as $order)
        <tr>
          <td>{{ $order->batch_number ?? 'Tanımsız' }}</td>
          <td>{{ $order->product->name }}</td>
          <td>{{ $order->customer->name }}</td>
          <td>{{ $order->productionLine->name }}</td>
          <td>
            @if($order->status == 'in_filling' && $order->filling && $order->filling->fillingLine)
              {{ $order->filling->fillingLine->name }}
            @else
              -
            @endif
          </td>
          <td>
            @if($order->status == 'in_filling' && $order->filling)
              {{ $order->filling->total_kg }} kg
            @else
              -
            @endif
          </td>
          <td>{{ \Carbon\Carbon::parse($order->planned_at)->format('d.m.Y H:i') }}</td>
          <td>{{ $order->quantity_kg }} kg</td>
          <td>
            @if($order->status == 'planned')
              <span class="badge bg-secondary">Planlandı</span>
            @elseif($order->status == 'in_production')
              <span class="badge bg-warning text-dark">Üretimde</span>
            @elseif($order->status == 'in_filling')
              <span class="badge bg-info text-dark">Dolumda</span>
            @elseif($order->status == 'produced')
              <span class="badge bg-success">Tamamlandı</span>
            @elseif($order->status == 'filled')
              <span class="badge bg-primary">Dolum Tamamlandı</span>
            @elseif($order->status == 'cancelled')
              <span class="badge bg-danger">İptal</span>
            @endif
          </td>
          <td>{{ $order->updated_at->diffForHumans() }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection