@extends('layouts.admin')
@section('title', 'Parametre Düzenle')

@section('content')
<div class="container">
  <h4 class="mb-4">Parametreyi Düzenle</h4>

  <form method="POST" action="{{ route('physical_parameter.update', $physicalParameter->id) }}">
    @csrf
    @method('PUT')

    <div class="mb-3">
      <label for="name" class="form-label">Parametre Adı</label>
      <input type="text" name="name" id="name" class="form-control" value="{{ $physicalParameter->name }}" required>
    </div>

    <div class="mb-3">
      <label for="group" class="form-label">Grup</label>
      <select name="group" id="group" class="form-select" required>
        <option value="" disabled>Grup seçiniz...</option>
        @foreach(['Bidon','Koli','Kapak','Etiket','Palet','Streç','Varil','IBC','Köşebent','İşçilik','Ara Karton'] as $group)
          <option value="{{ $group }}" @if($physicalParameter->group === $group) selected @endif>{{ $group }}</option>
        @endforeach
      </select>
    </div>

    <div class="mb-3">
      <label for="description" class="form-label">Açıklama</label>
      <textarea name="description" id="description" class="form-control" rows="3">{{ $physicalParameter->description }}</textarea>
    </div>

    <button type="submit" class="btn btn-success">Güncelle</button>
    <a href="{{ route('physical_parameter.index') }}" class="btn btn-secondary">Geri</a>
  </form>
</div>
@endsection