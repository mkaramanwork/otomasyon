@extends('layouts.admin')

@section('title','Rapor Doğrulama')

@section('content')
<div class="container mt-4">
    @if(isset($error))
        <h4 style="color:red;">✘ {{ $error }}</h4>
    @else
        <h4 style="color:green;">✔ Bu rapor orijinaldir</h4>

        <p><strong>Rapor No:</strong> {{ $sample->report_no ?? '-' }}</p>
        <p><strong>Rapor Tarihi:</strong> {{ $sample->report_date?->format('d.m.Y') ?? '-' }}</p>

        <h5>Analiz Edilen Parametreler</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Parametre</th>
                    <th>Metot</th>
                    <th>Sonuç</th>
                    <th>Birim</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sample->parameters as $param)
                    <tr>
                        <td>{{ $param->parameter }}</td>
                        <td>{{ $param->method_tr }}</td>
                        <td>{{ $param->pivot->result ?? '-' }}</td>
                        <td>{{ $param->unit ?? $param->pivot->unit ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p class="mt-3"><em>Bu rapor ALKİM PETROKİMYA A.Ş. laboratuvarı tarafından oluşturulmuştur.Bu rapor sistem kayıtlarımızda mevcuttur.</em></p>
    @endif
</div>
@endsection
