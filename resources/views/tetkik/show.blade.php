@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Tetkik Detayı</h4>
        <a href="{{ route('tetkik.rapor',$plan->id) }}" class="btn btn-success">
            📄 Rapor İndir
        </a>
    </div>

    <table class="table table-bordered">
        <tr>
            <th>Tetkik No</th>
            <td>{{ $plan->tetkik_no }}</td>
        </tr>
        <tr>
            <th>Tarih</th>
            <td>{{ $plan->tetkik_tarih }}</td>
        </tr>
        <tr>
            <th>Denetçi</th>
            <td>{{ $plan->denetci }}</td>
        </tr>
        <tr>
            <th>Bölüm</th>
            <td>{{ $plan->bolum }}</td>
        </tr>
        <tr>
            <th>Durum</th>
            <td>{{ $plan->durum }}</td>
        </tr>
        <tr>
            <th>Amac</th>
            <td>{{ $plan->amac }}</td>
        </tr>
        <tr>
            <th>Kapsam</th>
            <td>{{ $plan->kapsam }}</td>
        </tr>
    </table>

    <h5>Sorular</h5>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Sıra</th>
                <th>Standart Maddesi</th>
                <th>Soru</th>
                <th>Delil</th>
                <th>Uygunsuzluk</th>
                <th>Açıklama</th>
            </tr>
        </thead>
        <tbody>
            @foreach($plan->sorular as $soru)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $soru->standart_maddesi }}</td>
                <td>{{ $soru->soru }}</td>
                <td>{{ $soru->delil }}</td>
                <td>
                    @if($soru->uygunsuzluk_var == 0)
                        Yok
                    @elseif($soru->uygunsuzluk_var == 1)
                        Var
                    @else
                        Gözlem
                    @endif
                </td>
                <td>{{ $soru->uygunsuzluk_aciklama }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('tetkik.index') }}" class="btn btn-secondary">Geri Dön</a>
</div>
@endsection