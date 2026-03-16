@extends('layouts.admin')
@section('title','Analiz Bekleyen Numuneler')

@section('content')
<div class="container">
    <h4>📊 Raporlama - Analiz Bekleyen Numuneler</h4>

    <!-- ✅ Özet Kartlar -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="card-title">Toplam Numune</h6>
                    <h4>{{ $samples->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="card-title">Tamamlanan Numune</h6>
                    <h4>
                        {{ $samples->filter(function($s){
                            $total = $s->parameters->count();
                            $done = $s->parameters->whereNotNull('pivot.result')->count();
                            return $total > 0 && $done == $total;
                        })->count() }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Arama / Filtreleme Formu -->
    <form method="GET" action="{{ route('samples.analysis-pending') }}" class="row g-3 mb-4">
        <div class="col-md-2">
            <input type="text" name="batch_no" class="form-control" placeholder="Batch No" value="{{ request('batch_no') }}">
        </div>
        <div class="col-md-3">
            <input type="text" name="company_name" class="form-control" placeholder="Firma" value="{{ request('company_name') }}">
        </div>
        <div class="col-md-2">
            <input type="text" name="report_no" class="form-control" placeholder="Rapor No" value="{{ request('report_no') }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Ara</button>
        </div>
    </form>

    @if($samples->isEmpty())
        <div class="alert alert-info">Şu anda analiz bekleyen numune yok.</div>
    @else
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Batch No</th>
                    <th>Firma</th>
                    <th>Rapor No</th>
                    <th>Numune Adı</th>
                    <th>Kabul Tarihi</th>
                    <th>İlerleme Durumu</th>
                    <th class="text-center">İşlem</th>
                </tr>
            </thead>
            <tbody>
                @foreach($samples as $sample)
                    @php
                        $total = $sample->parameters->count();
                        $done = $sample->parameters->whereNotNull('pivot.result')->count();
                        $percent = $total > 0 ? round(($done / $total) * 100) : 0;
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $sample->batch_no }}</td>
                        <td>{{ $sample->company_name }}</td>
                        <td>{{ $sample->report_no ?? '-' }}</td>
                        <td>{{ $sample->product->name ?? '-' }}</td>
                        <td>{{ $sample->taken_at ? $sample->taken_at->format('d.m.Y H:i') : '-' }}</td>
                        <td>
                            <div class="progress" style="height:20px;">
                                <div class="progress-bar 
                                    @if($percent < 50) bg-danger 
                                    @elseif($percent < 100) bg-warning 
                                    @else bg-success @endif"
                                    role="progressbar"
                                    style="width: {{ $percent }}%;" aria-valuenow="{{ $percent }}"
                                    aria-valuemin="0" aria-valuemax="100">
                                    {{ $percent }}%
                                </div>
                            </div>
                            @if($percent == 100)
                                <span class="badge bg-success mt-1">✔ Numune girişi tamamlandı</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('samples.results.edit',$sample->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Sonuç Gir
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection