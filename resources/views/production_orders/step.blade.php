@extends('layouts.admin')
@section('title', 'Üretim Adımı Detayı')

@section('content')
<div class="container">
  <h4 class="mb-4">Adım: {{ $step->name }}</h4>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="mb-3">
    <strong>İş Emri:</strong> {{ $step->productionOrder->batch_number }}<br>
    <strong>Ürün:</strong> {{ $step->productionOrder->product->name }}<br>
    <strong>Formül:</strong> {{ $step->productionOrder->formula->name }}<br>
    <strong>Durum:</strong>
    @if($step->status === 'tamamlandı')
      <span class="badge bg-success">Tamamlandı</span>
    @else
      <span class="badge bg-warning">Bekliyor</span>
    @endif
  </div>

  <h5>Formül Kimyasalları</h5>
  <form action="{{ route('production_orders.step.update_chemicals', $step->id) }}" method="POST">
    @csrf
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Ham Madde</th>
          <th>Miktar (kg)</th>
          <th>Durum</th>
          <th>İşaretle</th>
        </tr>
      </thead>
      <tbody>
        @foreach($step->productionOrder->productionFormulaDetails as $detail)
          <tr>
            <td>{{ $detail->formulaDetail->rawMaterial->name }}</td>
            <td>{{ number_format($detail->calculated_amount, 2) }}</td>
            <td>
              @if($detail->is_completed)
                <span class="badge bg-success">Tamamlandı</span>
              @else
                <span class="badge bg-secondary">Bekliyor</span>
              @endif
            </td>
            <td>
              <input type="checkbox" name="completed_details[]" value="{{ $detail->id }}" {{ $detail->is_completed ? 'checked disabled' : '' }}>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <button type="submit" class="btn btn-primary mt-3">Seçilenleri Tamamla</button>
  </form>

  @php
    $allDone = $step->productionOrder->productionFormulaDetails->every(fn($d) => $d->is_completed);
  @endphp

  @if($allDone && $step->status !== 'tamamlandı')
    <form action="{{ route('production_orders.step.complete', $step->id) }}" method="POST" class="mt-4">
      @csrf
      <button type="submit" class="btn btn-danger">
        <i class="fas fa-check-circle"></i> Üretimi Bitir
      </button>
    </form>
  @endif
</div>
@endsection