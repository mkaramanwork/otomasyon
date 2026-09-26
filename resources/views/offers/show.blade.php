@extends('layouts.admin')
@section('title','Teklif Detay')

@section('content')
<div class="container">
    <h4 class="mb-3">📄 Teklif Detay - {{ $offer->offer_no }}</h4>

    <!-- Genel Bilgiler -->
    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Firma:</strong> {{ $offer->customer->name }}</p>
            <p><strong>Başlık:</strong> {{ $offer->title }}</p>
            <p><strong>Metin:</strong> {{ $offer->text }}</p>
            <p><strong>Tarih:</strong> {{ $offer->offer_date }}</p>
            <p><strong>Durum:</strong> {{ ucfirst($offer->status) }}</p>
            <p><strong>Toplam:</strong> {{ number_format($offer->total_amount,2) }} ₺</p>
            <p><strong>İndirim:</strong> %{{ $offer->discount_rate }}</p>
            <p><strong>Net:</strong> {{ number_format($offer->net_amount,2) }} ₺</p>
            <p><strong>Kalan Numune Hakkı:</strong> {{ $offer->sample_count }}</p>
        </div>
    </div>

    <!-- Parametreler -->
    <h5>Parametreler</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Parametre</th>
                <th>Metot</th>
                <th>Fiyat (₺)</th>
                <th>Kapsam</th>
            </tr>
        </thead>
        <tbody>
            @foreach($offer->parameters as $param)
            <tr>
                <td>{{ $param->parameter->parameter }}</td>
                <td>{{ $param->method }}</td>
                <td>{{ number_format($param->price,2) }}</td>
                <td>{{ $param->scope }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Onaylı Teklif PDF -->
    <h5 class="mt-4">Onaylı Teklif PDF</h5>
    @if($offer->approved_pdf)
        <a href="{{ asset('storage/'.$offer->approved_pdf) }}" target="_blank" class="btn btn-outline-primary">
            📄 PDF İndir
        </a>
    @else
        <form action="{{ route('offers.uploadPdf',$offer->id) }}" method="POST" enctype="multipart/form-data" class="mt-2">
            @csrf
            <input type="file" name="approved_pdf" class="form-control mb-2" accept="application/pdf" required>
            <button class="btn btn-primary">PDF Yükle</button>
        </form>
    @endif

    <!-- Accordion -->
    <div class="accordion mt-4" id="samplesAccordion">

        <!-- Mevcut Numuneler -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingActive">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseActive">
                    📦 Mevcut Numuneler
                </button>
            </h2>
            <div id="collapseActive" class="accordion-collapse collapse show" data-bs-parent="#samplesAccordion">
                <div class="accordion-body">
                    <ul>
                        @foreach($samples as $sample)
                            <li>
                                {{ $sample->batch_no }} - {{ $sample->company_name }}
                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#sampleModal{{ $sample->id }}">
                                    Detay
                                </button>
                            </li>

                            <!-- Modal -->
                            <div class="modal fade" id="sampleModal{{ $sample->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Numune Detayı - {{ $sample->batch_no }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Firma:</strong> {{ $sample->company_name }}</p>
                                            <p><strong>Ülke:</strong> {{ $sample->country }}</p>
                                            <p><strong>Alınma Tarihi:</strong> {{ $sample->taken_at }}</p>
                                            <p><strong>Miktar:</strong> {{ $sample->amount }}</p>
                                            <p><strong>Açıklama:</strong> {{ $sample->description }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Silinmiş Numuneler -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingDeleted">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDeleted">
                    ❌ Silinmiş Numuneler
                </button>
            </h2>
            <div id="collapseDeleted" class="accordion-collapse collapse" data-bs-parent="#samplesAccordion">
                <div class="accordion-body">
                    <ul>
                        @foreach($deletedSamples as $sample)
                            <li>
                                {{ $sample->batch_no }} - {{ $sample->company_name }}
                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deletedSampleModal{{ $sample->id }}">
                                    Detay
                                </button>
                            </li>

                            <!-- Modal -->
                            <div class="modal fade" id="deletedSampleModal{{ $sample->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Silinmiş Numune - {{ $sample->batch_no }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Firma:</strong> {{ $sample->company_name }}</p>
                                            <p><strong>Ülke:</strong> {{ $sample->country }}</p>
                                            <p><strong>Alınma Tarihi:</strong> {{ $sample->taken_at }}</p>
                                            <p><strong>Miktar:</strong> {{ $sample->amount }}</p>
                                            <p><strong>Açıklama:</strong> {{ $sample->description }}</p>
                                            <p class="text-danger"><strong>Durum:</strong> Bu numune silinmiş</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

    </div>

    <!-- Numune Bağlama Formu -->
    <form action="{{ route('offers.attachSample',$offer->id) }}" method="POST" class="mt-3">
        @csrf
        <label for="sample_id" class="form-label">Numune Seç</label>
        <select name="sample_id" class="form-control mb-2" required>
            <option value="">Numune seçiniz</option>
            @foreach($samples as $sample)
                @if(!$sample->trashed())
                    <option value="{{ $sample->id }}">{{ $sample->batch_no }} - {{ $sample->company_name }}</option>
                @endif
            @endforeach
        </select>
        <button class="btn btn-success">Numune Bağla</button>
    </form>

    <!-- Tümünü Bağla Butonu -->
    <form action="{{ route('offers.attachAllSamples',$offer->id) }}" method="POST" class="mt-3">
        @csrf
        <button class="btn btn-warning">Tüm Numuneleri Bu Teklife Bağla</button>
    </form>
</div>
@endsection
