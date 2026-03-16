@extends('layouts.admin')

@section('title', 'Üretimdeki İş Emirleri')

@section('content')
<div class="row">
  <div class="col-md-12">
    <h4 class="mb-4">Aktif Üretim Süreci</h4>

    @if($orders->isEmpty())
      <div class="alert alert-info">Şu anda üretimde olan iş emri bulunmamaktadır.</div>
    @else
      <table class="table table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <th>Batch</th>
            <th>Ürün</th>
            <th>Hat</th>
            <th>Planlanan</th>
            <th>Miktar</th>
            <th>Formül</th>
            <th>İşlem</th>
          </tr>
        </thead>
        <tbody>
          @foreach($orders as $order)
            <tr>
              <td>{{ $order->batch_number }}</td>
              <td>{{ $order->product->name }}</td>
              <td>{{ $order->line->name }}</td>
              <td>{{ \Carbon\Carbon::parse($order->planned_at)->format('d.m.Y H:i') }}</td>
              <td>{{ number_format($order->quantity_kg, 2) }} kg</td>
              <td>{{ $order->formula->name }}</td>
              <td>
                <a href="{{ route('production_orders.show', $order->id) }}" class="btn btn-sm btn-outline-info">
                  Görüntüle
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
</div>
@endsection