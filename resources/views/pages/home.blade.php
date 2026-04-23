@extends('layouts.admin')

@section('title','Ana Sayfa')

@section('content')
<div class="container-fluid">
    <!-- Başlık + Yeni Numune Kaydet butonu -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>🧪 Numune Kabul Listesi</h4>
        <a href="{{ route('samples.accept-manual-create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Numune Kaydet
        </a>
    </div>

    <!-- ✅ Kabul Edilen Numuneler -->
    <h5 class="mt-4">✅ Kabul Edilen Numuneler</h5>
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
    @endif
</div>
@endsection
