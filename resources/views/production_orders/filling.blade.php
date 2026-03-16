@extends('layouts.admin')

@section('title', 'Dolumdaki İş Emirleri')

@section('content')
<div class="row">
  <div class="col-md-12">
    <h4 class="mb-4">Aktif Dolum Süreci</h4>

    @if($fillings->isEmpty())
      <div class="alert alert-info">Şu anda dolumda olan iş emri bulunmamaktadır.</div>
    @else
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
          <tr>
            <th>Batch</th>
            <th>Ürün</th>
            <th>Dolum Hattı</th>
            <th>Planlanan Tarih</th>

            {{-- ✅ Planlanan Dolum --}}
            <th>Planlanan Dolum</th>

            <th>Fiziksel Reçete</th>
            <th>Durum</th>

            {{-- ✅ Gerçekleşen Dolum --}}
            <th>Gerçekleşen Dolum</th>

            {{-- ✅ Dolum Yüzdesi --}}
            <th>Dolum Yüzdesi</th>

            {{-- ✅ Kalan Tonaj --}}
            <th>Kalan Tonaj</th>

            <th>Dolum İşlemi</th>
          </tr>
        </thead>
        <tbody>
          @foreach($fillings as $filling)
            @php
              $order = $filling->productionOrder;

              $plannedKg = $order->quantity_kg;
              $plannedAdet = $plannedKg > 0 && $filling->density > 0 && $filling->volume > 0
                  ? floor($plannedKg / ($filling->density * $filling->volume))
                  : 0;

              $filledKg = $filling->total_kg ?? 0;
              $filledAdet = $filling->filled_quantity ?? 0;

              $percent = $plannedKg > 0 ? round(($filledKg / $plannedKg) * 100, 2) : 0;
              $remaining = round($plannedKg - $filledKg, 2);
            @endphp

            <tr>
              <td>{{ $order->batch_number }}</td>
              <td>{{ $order->product->name }}</td>
              <td>{{ $filling->fillingLine->name ?? '-' }}</td>
              <td>{{ \Carbon\Carbon::parse($order->planned_at)->format('d.m.Y H:i') }}</td>

              {{-- ✅ Planlanan Dolum --}}
              <td>
                <strong>{{ $plannedAdet }} adet</strong><br>
                <small class="text-muted">{{ number_format($plannedKg, 2) }} kg</small>
              </td>

              <td>{{ $filling->physicalFormula->product_name ?? '-' }}</td>

              <td>
                <span class="badge bg-{{ $filling->statusColor() }}">
                  {{ ucfirst(str_replace('_', ' ', $filling->status)) }}
                </span>
              </td>

              {{-- ✅ Gerçekleşen Dolum --}}
              <td>
                <strong>{{ $filledAdet }} adet</strong><br>
                <small class="text-muted">{{ number_format($filledKg, 2) }} kg</small>
              </td>

              {{-- ✅ Dolum Yüzdesi --}}
              <td>
                <strong>%{{ $percent }}</strong>
                <div class="progress mt-1" style="height: 6px;">
                  <div class="progress-bar bg-success" role="progressbar"
                       style="width: {{ $percent }}%"></div>
                </div>
              </td>

              {{-- ✅ Kalan Tonaj --}}
              <td>
                <strong>{{ $remaining }} kg</strong>
              </td>

              {{-- ✅ Dolum İşlemi --}}
              <td>
                @if(in_array($filling->status, ['pending', 'in_progress']))
                  <form action="{{ route('fillings.complete', $filling->id) }}" method="POST" class="d-flex">
                    @csrf
                    <input type="number" name="filled_quantity" class="form-control form-control-sm me-2"
                           min="1" required placeholder="Adet">
                    <button type="submit" class="btn btn-sm btn-success">Tamamla</button>
                  </form>
                @else
                  <span class="text-success fw-bold">Tamamlandı</span>
                @endif
              </td>

            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
</div>
@endsection