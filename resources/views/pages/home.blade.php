@extends('layouts.admin')

@section('title','Ana Sayfa')

@section('content')
<div class="container-fluid">
    <!-- 📊 İstatistik Kutuları -->
    <div class="row mb-3">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box">
                <span class="info-box-icon text-bg-primary shadow-sm">
                    <i class="bi bi-flask"></i> 
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Toplam Numune</span>
                    <span class="info-box-number">{{ $totalSamples }}</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box">
                <span class="info-box-icon text-bg-success shadow-sm">
                    <i class="bi bi-file-earmark-check-fill"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Raporlanan Numune</span>
                    <span class="info-box-number">{{ $reportedSamples }}</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box">
                <span class="info-box-icon text-bg-warning shadow-sm">
                    <i class="bi bi-people-fill"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Kayıtlı Kullanıcı</span>
                    <span class="info-box-number">{{ $userCount }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Kabul Edilen Numuneler Listesi -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>🧪 Numune Kabul Listesi</h4>
        <a href="{{ route('samples.accept-manual-create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Numune Kaydet
        </a>
    </div>

    @if($acceptedSamples->isEmpty())
        <div class="alert alert-info">Henüz kabul edilen numune yok.</div>
    @else
        <div class="card card-outline card-success shadow-sm">
            <div class="card-body p-0">
                <table class="table table-bordered table-striped align-middle mb-0 sampleTable">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Ürün</th>
                            <th>Firma</th>
                            <th>Batch</th>
                            <th>Talep No</th>
                            <th>Numune Türü</th>
                            <th>Alındığı Yer</th>
                            <th>Alan Kişi</th>
                            <th>Alınma Tarihi</th>
                            <th class="text-center">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($acceptedSamples as $sample)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $sample->labProduct->product_name ?? '-' }}</td>
                                <td>{{ $sample->company_name }}</td>
                                <td>{{ $sample->batch_no }}</td>
                                <td>{{ $sample->request_no }}</td>
                                <td>{{ $sample->sample_type }}</td>
                                <td>{{ $sample->taken_place }}</td>
                                <td>{{ $sample->taken_by }}</td>
                                <td>{{ $sample->taken_at }}</td>
                                <td class="text-center">
                                    <!-- Etiket -->
                                    <a href="{{ route('samples.labels',$sample->id) }}" target="_blank" class="btn btn-sm btn-success">
                                        <i class="fas fa-tag"></i> Etiket
                                    </a>
                                    <!-- Detay -->
                                    <button type="button" class="btn btn-sm btn-info"
                                            data-bs-toggle="modal"
                                            data-bs-target="#sampleModal{{ $sample->id }}">
                                        <i class="fas fa-eye"></i> Detay
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-muted">
                Toplam Kabul Edilen Numune: {{ $acceptedSamples->count() }}
            </div>
        </div>
    @endif
</div>

<!-- ✅ Detay Modalları -->
@foreach($acceptedSamples as $sample)
<div class="modal fade" id="sampleModal{{ $sample->id }}" tabindex="-1" aria-labelledby="sampleModalLabel{{ $sample->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="sampleModalLabel{{ $sample->id }}">Numune Detayı</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <ul class="list-group mb-3">
            <li class="list-group-item"><strong>Ürün:</strong> {{ $sample->labProduct->product_name ?? '-' }}</li>
            <li class="list-group-item"><strong>Firma:</strong> {{ $sample->company_name }}</li>
            <li class="list-group-item"><strong>Batch No:</strong> {{ $sample->batch_no }}</li>
            <li class="list-group-item"><strong>Talep No:</strong> {{ $sample->request_no }}</li>
            <li class="list-group-item"><strong>Numune Türü:</strong> {{ $sample->sample_type }}</li>
            <li class="list-group-item"><strong>Alındığı Yer:</strong> {{ $sample->taken_place }}</li>
            <li class="list-group-item"><strong>Alan Kişi:</strong> {{ $sample->taken_by }}</li>
            <li class="list-group-item"><strong>Alınma Tarihi:</strong> {{ $sample->taken_at }}</li>
        </ul>

        <h6>📊 Parametreler</h6>
        @if($sample->parameters->isNotEmpty())
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>Numune Türü</th>
                    <th>Parametre</th>
                    <th>Metot (TR)</th>
                    <th>Birim</th>
                    <th>Kapsam</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sample->parameters as $param)
                    <tr>
                        <td>{{ $param->sample_type }}</td>
                        <td>{{ $param->parameter }}</td>
                        <td>{{ $param->method_tr }}</td>
                        <td>{{ $param->unit }}</td>
                        <td>{{ $param->scope }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @else
            <span class="text-muted">Parametre yok</span>
        @endif
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
      </div>
    </div>
  </div>
</div>
@endforeach
@endsection
