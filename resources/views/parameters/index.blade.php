@extends('layouts.admin')

@section('title', 'Parametreler')

@section('content')
<div class="container">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4>🧪 Parametre Listesi</h4>
    <div class="d-flex gap-2">
      <a href="{{ route('parameters.create') }}" class="btn btn-success">
        <i class="fas fa-plus"></i> Yeni Parametre Ekle
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if($parameters->isEmpty())
    <div class="alert alert-info">Sonuç bulunamadı.</div>
  @else
    <div class="card card-outline card-primary">
      <div class="card-body p-0">
        <table class="table table-bordered table-striped align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Numune Türü</th>
              <th>Parametre</th>
              <th>Metod (TR)</th>
              <th>Metod (EN)</th>
              <th>Metod (RU)</th>
              <th>Birim</th>
              <th>Kapsam</th>
              <th>Belirsizlik</th> <!-- ✅ yeni sütun -->
              <th class="text-center">İşlem</th>
            </tr>
          </thead>
          <tbody>
            @foreach($parameters as $parameter)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $parameter->sample_type }}</td>
                <td>{{ $parameter->parameter }}</td>
                <td>{{ $parameter->method_tr }}</td>
                <td>{{ $parameter->method_en }}</td>
                <td>{{ $parameter->method_ru }}</td>
                <td>{{ $parameter->unit }}</td>
                <td>{{ $parameter->scope }}</td>
                <td>{{ $parameter->uncertainty ?? '-' }}</td> <!-- ✅ belirsizlik değeri -->
                <td class="text-center">
                  <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('parameters.edit', $parameter->id) }}" class="btn btn-sm btn-outline-primary">
                      <i class="fas fa-edit"></i> Düzenle
                    </a>
                    <form action="{{ route('parameters.destroy', $parameter->id) }}" method="POST"
                          onsubmit="return confirm('Bu parametreyi silmek istediğinize emin misiniz?')" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-trash"></i> Sil
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="card-footer text-muted">
        Toplam Parametre: {{ $parameters->count() }}
      </div>
    </div>
  @endif
</div>
@endsection