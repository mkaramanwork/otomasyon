@extends('layouts.admin')
@section('title', 'Parametre Ekle')

@section('content')
<div class="container">
  <h4 class="mb-4">Reçete: {{ $formula->product_name }}</h4>

@if(session('success'))
    <div class="alert alert-success">
        {!! session('success') !!}
    </div>
@endif


  {{-- Parametre Ekleme Formu --}}
  <form method="POST" action="{{ route('physcial_formula.storeDetail') }}">
    @csrf
    <input type="hidden" name="physical_formula_id" value="{{ $formula->id }}">

    <div class="mb-3">
      <label class="form-label">Seçim Etiketi</label>
      <input type="text" name="selection_label" class="form-control" placeholder="1. Seçim, 2. Seçim..." required>
    </div>

    <div class="mb-3">
      <label class="form-label">Fiziksel Parametre</label>
      <select name="physical_parameter_id" class="form-select" required>
        <option value="" disabled selected>Parametre seçiniz...</option>
        @foreach($parameters as $param)
          <option value="{{ $param->id }}">{{ $param->group }} - {{ $param->name }}</option>
        @endforeach
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Gerekli Miktar</label>
      <input type="number" name="quantity" step="0.001" class="form-control" placeholder="Örn: 2.5" required>
    </div>

    <button type="submit" class="btn btn-success">Ekle</button>
    <a href="{{ route('physcial_formula.details', $formula->id) }}" class="btn btn-secondary">Detaylara Dön</a>
  </form>

  {{-- Mevcut Parametreler Listesi --}}
  <hr class="my-4">
  <h5>Tanımlı Parametreler</h5>

  <table class="table table-bordered table-striped mt-3">
    <thead class="table-light">
      <tr>
        <th>#</th>
        <th>Seçim Etiketi</th>
        <th>Parametre</th>
        <th>Grup</th>
        <th>Miktar</th>
      </tr>
    </thead>
    <tbody>
      @forelse($formula->details()->with('parameter')->get() as $detail)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $detail->selection_label }}</td>
          <td>{{ $detail->parameter?->name ?? '-' }}</td>
          <td>{{ $detail->parameter?->group ?? '-' }}</td>
          <td>{{ number_format($detail->quantity, 3) }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="text-center text-muted">Henüz parametre eklenmedi.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection