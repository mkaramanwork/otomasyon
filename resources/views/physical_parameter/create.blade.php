@extends('layouts.admin')
@section('title', 'Yeni Fiziksel Parametre')

@section('content')
<div class="container">
  <h4 class="mb-4">Yeni Fiziksel Parametre Ekle</h4>

  <form method="POST" action="{{ route('physical_parameter.store') }}">
    @csrf

    <div class="mb-3">
      <label for="name" class="form-label">Parametre Adı</label>
      <input type="text" name="name" id="name" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="group" class="form-label">Grup</label>
      <select name="group" id="group" class="form-select" required>
        <option value="" disabled selected>Grup seçiniz...</option>
        <option value="Bidon">Bidon</option>
        <option value="Koli">Koli</option>
        <option value="Kapak">Kapak</option>
        <option value="Etiket">Etiket</option>
        <option value="Palet">Palet</option>
        <option value="Streç">Streç</option>
        <option value="Varil">Varil</option>
        <option value="IBC">IBC</option>
        <option value="Köşebent">Köşebent</option>
        <option value="İşçilik">İşçilik</option>
        <option value="Ara Karton">Ara Karton</option>
      </select>
    </div>

    <div class="mb-3">
      <label for="description" class="form-label">Açıklama</label>
      <textarea name="description" id="description" class="form-control" rows="3"></textarea>
    </div>

    <button type="submit" class="btn btn-success">Kaydet</button>
    <a href="{{ route('physical_parameter.index') }}" class="btn btn-secondary">Geri</a>
  </form>
</div>
@endsection