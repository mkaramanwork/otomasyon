@extends('layouts.admin')
@section('title', 'Fiziksel Parametreler')

@section('content')
<div class="container">
  <h4 class="mb-4">Tanımlı Fiziksel Parametreler</h4>

  <a href="{{ route('physical_parameter.create') }}" class="btn btn-primary mb-3">Yeni Parametre Ekle</a>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <table class="table table-bordered table-striped">
    <thead class="table-light">
      <tr>
        <th style="width: 60px;">#</th>
        <th>Ad</th>
        <th>Grup</th>
        <th>Açıklama</th>
        <th style="width: 160px;">İşlem</th>
      </tr>
    </thead>
    <tbody>
      @forelse($parameters as $parameter)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $parameter->name }}</td>
          <td>{{ $parameter->group }}</td>
          <td>{{ $parameter->description }}</td>
          <td>
            <a href="{{ route('physical_parameter.edit', $parameter->id) }}" class="btn btn-sm btn-warning">Düzenle</a>
            <form action="{{ route('physical_parameter.destroy', $parameter->id) }}" method="POST" class="d-inline">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Silmek istediğine emin misin?')">Sil</button>
            </form>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="text-center text-muted">Henüz fiziksel parametre tanımlanmadı.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection