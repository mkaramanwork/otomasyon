@extends('layouts.admin')
@section('title', 'Fiziksel Reçeteler')

@section('content')
<div class="container">
  <h4 class="mb-4">Tanımlı Fiziksel Reçeteler</h4>

  <a href="{{ route('physcial_formula.create') }}" class="btn btn-primary mb-3">Yeni Reçete Oluştur</a>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <table class="table table-bordered table-striped">
    <thead class="table-light">
      <tr>
        <th style="width: 60px;">#</th>
        <th>Ürün Kodu</th>
        <th>Ürün Adı</th>
        <th>Birim</th>
        <th>Açıklama</th>
        <th style="width: 240px;">İşlemler</th>
      </tr>
    </thead>
    <tbody>
      @forelse($formulas as $formula)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $formula->product_code }}</td>
          <td>{{ $formula->product_name }}</td>
          <td>{{ $formula->unit }}</td>
          <td>{{ $formula->description }}</td>
          <td>
            <a href="{{ route('physcial_formula.edit', ['physcial_formula' => $formula->id]) }}" class="btn btn-sm btn-warning">Düzenle</a>
            <a href="{{ route('physcial_formula.details', $formula->id) }}" class="btn btn-sm btn-info">Parametreler</a>
            <a href="{{ route('physcial_formula.addDetail', $formula->id) }}" class="btn btn-sm btn-success">Parametre Ekle</a>
            <form action="{{ route('physcial_formula.destroy', ['physcial_formula' => $formula->id]) }}" method="POST" class="d-inline">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Silmek istediğine emin misin?')">Sil</button>
            </form>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="text-center text-muted">Henüz fiziksel reçete tanımlanmadı.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection