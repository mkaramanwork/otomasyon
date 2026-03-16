@extends('layouts.admin')

@section('title', 'Uygunsuzluklar')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4>⚠️ Uygunsuzluk Listesi</h4>
    <a href="{{ route('uygunsuzluk.create') }}" class="btn btn-success">Yeni Uygunsuzluk Ekle</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if($records->isEmpty())
    <div class="alert alert-info">Sonuç bulunamadı.</div>
  @else
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Bölüm</th>
          <th>Tanım</th>
          <th>Bildirim Tarihi</th>
          <th>Durum</th>
          <th class="text-center">İşlem</th>
        </tr>
      </thead>
      <tbody>
        @foreach($records as $rec)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $rec->bolum }}</td>
            <td>{{ $rec->tanim }}</td>
            <td>{{ $rec->bildirim_tarihi }}</td>
            <td>
              <span class="badge bg-{{ $rec->durum === 'closed' ? 'success' : 'warning' }}">
                {{ ucfirst($rec->durum) }}
              </span>
            </td>
            <td class="text-center">
              <div class="d-flex justify-content-center gap-2">
                <a href="{{ route('uygunsuzluk.show', $rec->id) }}" class="btn btn-sm btn-outline-info">Detay</a>
                <a href="{{ route('uygunsuzluk.edit', $rec->id) }}" class="btn btn-sm btn-outline-primary">Düzenle</a>
                <form action="{{ route('uygunsuzluk.destroy', $rec->id) }}" method="POST" 
                      onsubmit="return confirm('Bu kaydı silmek istediğinize emin misiniz?')" style="display:inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">Sil</button>
                </form>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif
</div>
@endsection