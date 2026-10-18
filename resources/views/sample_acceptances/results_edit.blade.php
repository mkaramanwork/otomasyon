@extends('layouts.admin')

@section('title','Numune Sonuç Girişi')

@section('content')
<div class="container">
    <h4>📄 Numune Sonuç Girişi</h4>

    <!-- ✅ Numune bilgileri -->
    <div class="mb-3">
        <strong>Batch No:</strong> {{ $sample->batch_no }} <br>
        <strong>Müşteri:</strong> {{ $sample->company_name }} <br>
        <strong>Ürün Adı:</strong> {{ $sample->labProduct->product_name ?? '-' }} <br>
        <strong>Rapor No:</strong> {{ $sample->report_no ?? '-' }}
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="alert alert-info py-2">
        İlk analiz girişi doğrudan kaydedilir. Daha önce kaydedilmiş sonuç değiştiriliyorsa revizyon gerekçesi zorunludur.
    </div>

    @if($sample->parameters->isEmpty())
        <div class="alert alert-warning">Bu numuneye ait parametre bulunamadı.</div>
    @else
        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle">
                <thead class="table-dark"><tr>
                    <th>Parametre</th><th>Metot (TR)</th><th>Ölçüm Belirsizliği</th>
                    <th>Sonuç</th><th>Tarih</th><th>Açıklama</th><th>Durum</th><th>Kaydet</th>
                </tr></thead>
                <tbody>
                @foreach($sample->parameters as $param)
                    @php $formId = 'analysis-form-'.$param->id; @endphp
                    <tr>
                        <td>{{ $param->parameter }}</td>
                        <td>{{ $param->method_tr }}</td>
                        <td>{{ $param->uncertainty }}</td>
                        <td><input form="{{ $formId }}" type="text" class="form-control" name="value" required value="{{ $param->pivot->result }}"></td>
                        <td><input form="{{ $formId }}" type="datetime-local" class="form-control" name="date" required value="{{ $param->pivot->result_date ? \Carbon\Carbon::parse($param->pivot->result_date)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i') }}"></td>
                        <td><input form="{{ $formId }}" type="text" class="form-control" name="description" value="{{ $param->pivot->description }}"></td>
                        <td>
                            @if($param->pivot->result !== null && $param->pivot->result !== '')
                                <span class="badge bg-success">Kaydedildi</span>
                            @else
                                <span class="badge bg-secondary">Bekliyor</span>
                            @endif
                        </td>
                        <td>
                            <form id="{{ $formId }}" action="{{ route('samples.results.storeSingle', [$sample->id, $param->id]) }}" method="POST">
                                @csrf
                            </form>
                            <button type="submit" form="{{ $formId }}" class="btn btn-success btn-sm">Kaydet</button>
                        </td>
                    </tr>
                    @if($param->pivot->result !== null && $param->pivot->result !== '')
                        <tr class="table-light"><td colspan="8">
                            <div class="row align-items-center g-2">
                                <div class="col-md-3"><strong>Revizyon gerekçesi</strong> <span class="text-muted">(değişiklik varsa zorunlu)</span></div>
                                <div class="col-md-9"><input form="{{ $formId }}" type="text" name="revision_reason" maxlength="1000" class="form-control form-control-sm" placeholder="Örn. Tekrar analiz sonucuna göre düzeltildi" value="{{ old('revision_reason') }}"></div>
                            </div>
                        </td></tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="card mt-4">
        <div class="card-header"><strong>Analiz Sonucu İşlem ve Revizyon Geçmişi</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive"><table class="table table-sm table-striped mb-0">
                <thead><tr><th>Tarih</th><th>Kullanıcı</th><th>İşlem</th><th>Eski / Yeni Değerler ve Gerekçe</th></tr></thead>
                <tbody>
                @forelse($analysisLogs as $log)
                    <tr>
                        <td class="text-nowrap">{{ $log->created_at?->format('d.m.Y H:i') }}</td>
                        <td>{{ $log->user->name ?? 'Sistem' }}</td>
                        <td>{{ $log->action }}</td>
                        <td style="white-space:pre-line">{{ $log->description }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-3">Henüz analiz işlem kaydı bulunmuyor.</td></tr>
                @endforelse
                </tbody>
            </table></div>
        </div>
    </div>

</div>
@endsection
