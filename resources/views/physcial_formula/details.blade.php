@extends('layouts.admin')
@section('title', 'Reçete Detayları')

@section('content')
<div class="container">
  <h4 class="mb-4">Reçete: {{ $formula->product_name }}</h4>

  <a href="{{ route('physcial_formula.addDetail', $formula->id) }}" class="btn btn-primary mb-3">Yeni Parametre Ekle</a>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <table class="table table-bordered table-striped">
    <thead class="table-light">
      <tr>
        <th>#</th>
        <th>Seçim Etiketi</th>
        <th>Parametre</th>
        <th>Grup</th>
      </tr>
    </thead>
    <tbody>
      @forelse($details as $detail)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $detail->selection_label }}</td>
          <td>{{ $detail->parameter->name }}</td>
          <td>{{ $detail->parameter->group }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="4" class="text-center text-muted">Henüz parametre eklenmedi.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <a href="{{ route('physcial_formula.index') }}" class="btn btn-secondary mt-3">Reçetelere Dön</a>
</div>
@endsection