@extends('layouts.admin')

@section('content')
<div class="container">
  <h4>Yeni İç Tetkik Planı</h4>
  <form action="{{ route('tetkik.store') }}" method="POST">
    @csrf
    <input type="text" name="tetkik_no" placeholder="Tetkik No" class="form-control mb-2">
    <input type="date" name="tetkik_tarih" class="form-control mb-2">
    <input type="text" name="denetci" placeholder="Denetçi" class="form-control mb-2">
    <input type="text" name="bolum" placeholder="Bölüm" class="form-control mb-2">
    <textarea name="amac" placeholder="Amaç" class="form-control mb-2"></textarea>
    <textarea name="kapsam" placeholder="Kapsam" class="form-control mb-2"></textarea>

    <h5>Soru Listesi</h5>
    @foreach($soruListesi as $soru)
      <div class="form-check">
        <input type="checkbox" name="sorular[]" value="{{ $soru->id }}" class="form-check-input">
        <label class="form-check-label">{{ $soru->standart_maddesi }} - {{ $soru->soru }}</label>
      </div>
    @endforeach

    <button type="submit" class="btn btn-primary mt-3">Kaydet</button>
  </form>
</div>
@endsection