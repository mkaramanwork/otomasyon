@extends('layouts.admin')

@section('content')
<div class="container">
  <h4>Planlanan İç Tetkikler</h4>

  {{-- İç Tetkik Planla Butonu --}}
  <div class="mb-3">
    <a href="{{ route('tetkik.create') }}" class="btn btn-primary">
      + İç Tetkik Planla
    </a>
  </div>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Sıra</th> {{-- sıra numarası sütunu --}}
        <th>No</th>
        <th>Tarih</th>
        <th>Denetçi</th>
        <th>Bölüm</th>
        <th>Durum</th>
        <th>İşlem</th>
      </tr>
    </thead>
    <tbody>
      @foreach($planlar as $plan)
      <tr>
        <td>{{ $loop->iteration }}</td> {{-- sıra numarası --}}
        <td>{{ $plan->tetkik_no }}</td>
        <td>{{ $plan->tetkik_tarih }}</td>
        <td>{{ $plan->denetci }}</td>
        <td>{{ $plan->bolum }}</td>
        <td>{{ $plan->durum }}</td>
        <td>
          <a href="{{ route('tetkik.show',$plan->id) }}" class="btn btn-sm btn-info">Detay</a>
          <a href="{{ route('tetkik.start',$plan->id) }}" class="btn btn-sm btn-success">Tetkik Yap</a>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection