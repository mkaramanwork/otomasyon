@extends('layouts.admin')

@section('title', 'Numune Kabul')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>🧪 Numune Kabul Listesi</h4>
        <div>
            <a href="{{ route('samples.accept-manual-create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Yeni Numune Kaydet
            </a>
            <a href="{{ route('samples.export-excel') }}" class="btn btn-success ms-2">
                <i class="fas fa-file-excel"></i> Numune Kabul Aktar (Excel)
            </a>
            <a href="{{ route('samples.export-excel-template') }}" class="btn btn-success ms-2">
                <i class="fas fa-file-excel"></i> Numune Kabul Aktar (Excel Template)
            </a>
                    </div>
    </div>

    {{-- ✅ Flash mesajlar --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- ✅ Analiz Bekleyen Numuneler -->
    <h5 class="mt-4">📥 Analiz Bekleyen Numuneler</h5>
    @if($pendingSamples->isEmpty())
        <div class="alert alert-info">Bekleyen numune bulunamadı.</div>
    @else
        <div class="card card-outline card-primary mb-4 shadow-sm">
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
                        @foreach($pendingSamples as $sample)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $sample->labProduct->product_name ?? '-' }}</td>
                                <td>{{ $sample->company_name }}</td>
                                <td>{{ $sample->batch_no }}</td>
                                <td>{{ $sample->report_no }}</td>
                                <td>{{ $sample->sample_type }}</td>
                                <td>{{ $sample->taken_place }}</td>
                                <td>{{ $sample->taken_by }}</td>
                                <td>{{ $sample->taken_at }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('samples.acceptForm',$sample->id) }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i> Kabul Et
                                        </a>
                                        <form action="{{ route('samples.reject', $sample->id) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Bu numuneyi reddetmek istediğinize emin misiniz?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-times"></i> Reddet
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-muted">
                Toplam Bekleyen Numune: {{ $pendingSamples->count() }}
            </div>
        </div>
    @endif

    <!-- ✅ Kabul Edilen Numuneler -->
    <h5 class="mt-4">✅ Kabul Edilen Numuneler</h5>
    <div class="mb-3">
        <label for="acceptedSampleSearch" class="form-label fw-semibold">Ürün Adı / Batch No ile Ara</label>
        <input type="search" id="acceptedSampleSearch" class="form-control"
               placeholder="Ürün adı veya Batch No yazınız..." autocomplete="off">
    </div>
    @if($acceptedSamples->isEmpty())
        <div class="alert alert-info">Henüz kabul edilen numune yok.</div>
    @else
        <div class="card card-outline card-success shadow-sm">
            <div class="card-body p-0">
                <table id="acceptedSamplesTable" class="table table-bordered table-striped align-middle mb-0 sampleTable">
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
                                <td>{{ $sample->report_no }}</td>
                                <td>{{ $sample->sample_type }}</td>
                                <td>{{ $sample->taken_place }}</td>
                                <td>{{ $sample->taken_by }}</td>
                                <td>{{ $sample->taken_at }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('samples.labels',$sample->id) }}" target="_blank" class="btn btn-sm btn-success">
                                            <i class="fas fa-tag"></i> Etiket
                                        </a>
                                        <button type="button" class="btn btn-sm btn-info"
                                                data-bs-toggle="modal"
                                                data-bs-target="#sampleModal{{ $sample->id }}">
                                            <i class="fas fa-eye"></i> Detay
                                        </button>
                                        <a href="{{ route('samples.edit',$sample->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Güncelle
                                        </a>
                                        <form action="{{ route('samples.destroy',$sample->id) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Bu numuneyi silmek istediğinize emin misiniz?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> Sil
                                            </button>
                                        </form>
                                    </div>
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

        <!-- ✅ Detay Modalları -->
        @foreach($acceptedSamples as $sample)
            <div class="modal fade" id="sampleModal{{ $sample->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title">Numune Detay</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <ul class="list-group mb-3">
                                <li class="list-group-item"><strong>Firma:</strong> {{ $sample->company_name }}</li>
                                <li class="list-group-item"><strong>Batch No:</strong> {{ $sample->batch_no }}</li>
                                <li class="list-group-item"><strong>Talep No:</strong> {{ $sample->request_no }}</li>
                                <li class="list-group-item"><strong>Numune Türü:</strong> {{ $sample->sample_type }}</li>
                                <li class="list-group-item"><strong>Alındığı Yer:</strong> {{ $sample->taken_place }}</li>
                                <li class="list-group-item"><strong>Alan Kişi:</strong> {{ $sample->taken_by }}</li>
                                <li class="list-group-item"><strong>Alınma Tarihi:</strong> {{ $sample->taken_at }}</li>
                            </ul>

                            <h6>📊 Parametreler</h6>
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

                            {{-- Numuneye ait tum islem ve revizyon kayitlari --}}
                            <hr class="my-4">
                            <h6 class="mb-3"><i class="fas fa-history me-1"></i> Numune İşlem ve Revizyon Geçmişi</h6>
                            <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                                <table class="table table-sm table-bordered table-striped align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="min-width: 145px;">İşlem Tarihi</th>
                                            <th style="min-width: 150px;">İşlem</th>
                                            <th style="min-width: 130px;">Kullanıcı</th>
                                            <th style="min-width: 300px;">Açıklama / Değişiklik</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($sampleLogsById->get($sample->id, collect()) as $log)
                                            <tr>
                                                <td>{{ $log->created_at?->format('d.m.Y H:i:s') ?? '-' }}</td>
                                                <td>{{ $log->action }}</td>
                                                <td>{{ $log->user->name ?? 'Bilinmiyor' }}</td>
                                                <td style="white-space: pre-line; overflow-wrap: anywhere;">{{ $log->description }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-3">Bu numuneye ait işlem kaydı bulunamadı.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

@if(session('open_labels'))
    <script>
        window.open("{{ session('open_labels') }}", "_blank");
    </script>
@endif

{{-- ✅ Yalnızca kabul edilen numunelerde ürün adı ve Batch No araması --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('acceptedSampleSearch');
        const table = document.getElementById('acceptedSamplesTable');
        if (!searchInput || !table) return;

        searchInput.addEventListener('input', function () {
            const search = this.value.trim().toLocaleLowerCase('tr-TR');
            table.querySelectorAll('tbody tr').forEach(function (row) {
                const product = (row.cells[1]?.textContent || '').toLocaleLowerCase('tr-TR');
                const batch = (row.cells[3]?.textContent || '').toLocaleLowerCase('tr-TR');
                row.style.display = (product.includes(search) || batch.includes(search)) ? '' : 'none';
            });
        });
    });
</script>
@endsection

