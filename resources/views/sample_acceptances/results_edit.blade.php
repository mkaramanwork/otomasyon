@extends('layouts.admin')

@section('title','Numune Sonuç Girişi')

@section('content')
<div class="container">
    <h4>📄 Numune Sonuç Girişi</h4>

    <!-- ✅ Numune bilgileri -->
    <div class="mb-3">
        <strong>Batch No:</strong> {{ $sample->batch_no }} <br>
        <strong>Müşteri:</strong> {{ $sample->company_name }} <br>
        <strong>Numune Adı:</strong> {{ $sample->product->name ?? '-' }} <br>
        <strong>Rapor No:</strong> {{ $sample->report_no ?? '-' }}
    </div>

    <!-- ✅ Parametreler tablosu -->
    @if($sample->parameters->isEmpty())
        <div class="alert alert-warning">Bu numuneye ait parametre bulunamadı.</div>
    @else
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Parametre</th>
                    <th>Metot (TR)</th>
                    <th>Ölçüm Belirsizliği</th>
                    <th>Sonuç</th>
                    <th>Tarih</th>
                    <th>Açıklama</th>
                    <th>Durum</th>
                    <th>Kaydet</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sample->parameters as $param)
                    <tr>
                        <form action="{{ route('samples.results.storeSingle', [$sample->id, $param->id]) }}" method="POST">
                            @csrf
                            <td>{{ $param->parameter }}</td>
                            <td>{{ $param->method_tr }}</td>
                            <td>{{ $param->uncertainty }}</td>
                            <td>
                                <input type="text" class="form-control"
                                       name="value" value="{{ $param->pivot->result }}">
                            </td>
                            <td>
                                <input type="datetime-local" class="form-control"
                                       name="date"
                                       value="{{ $param->pivot->result_date ?? now()->format('Y-m-d\TH:i') }}">
                            </td>
                            <td>
                                <input type="text" class="form-control"
                                       name="description" value="{{ $param->pivot->description }}">
                            </td>
                            <td>
                                {{ $param->parameter }}
                                @if($param->pivot->result)
                                    <span class="badge bg-success">✔ Kaydedildi</span>
                                @else
                                    <span class="badge bg-secondary">Bekliyor</span>
                                @endif
                            </td>
                            <td>
                                <button type="submit" class="btn btn-success btn-sm">Kaydet</button>
                            </td>
                        </form>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($sample->logs->isNotEmpty())
    <tr>
        <td colspan="7">
            <strong>📜 İşlem Geçmişi:</strong>
            <table class="table table-sm table-bordered mt-2">
                <thead>
                    <tr>
                        <th>Tarih</th>
                        <th>Kullanıcı</th>
                        <th>İşlem</th>
                        <th>Açıklama</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sample->logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('d.m.Y H:i') }}</td>
                            <td>{{ $log->user->name ?? 'Sistem' }}</td>
                            <td>{{ $log->action }}</td>
                            <td>{{ $log->description }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </td>
    </tr>
@endif
    @endif
</div>
@endsection