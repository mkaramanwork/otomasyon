@extends('layouts.admin')

@section('title','Numune Rapor Detayı')

@section('content')
<div class="container">
    <h4>📑 Numune Rapor Detayı</h4>

    <!-- ✅ Ürün ve Numune Bilgileri -->
    <p><strong>Ürün:</strong> {{ $sample->product->name ?? '-' }}</p>
    <p><strong>Firma:</strong> {{ $sample->company_name }}</p>
    <p><strong>Batch No:</strong> {{ $sample->batch_no }}</p>

    <!-- ✅ Parametreler ve Sonuçlar -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Parametre</th>
                <th>Metot</th> <!-- ✅ yeni sütun -->
                <th>Sonuç</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sample->parameters as $param)
                <tr>
                    <td>{{ $param->parameter }}</td>
                    <td>{{ $param->method_tr ?? '-' }}</td> <!-- ✅ metot adı -->
                    <td>{{ $param->pivot->result ?? 'Bekliyor' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- ✅ Tek satır form -->
    <form method="POST" action="{{ route('samples.report.store') }}" enctype="multipart/form-data" class="d-flex gap-2">
        @csrf
        <input type="hidden" name="sample_id" value="{{ $sample->id }}">
        <input type="date" name="report_date" value="{{ $sample->report_date }}" class="form-control w-auto">
        <input type="text" name="description" value="{{ $sample->report_description }}" class="form-control w-50" placeholder="Açıklama">
        <input type="file" name="images[]" class="form-control w-auto" multiple>
        <button type="submit" class="btn btn-success">Kaydet</button>
    </form>

    <!-- ✅ Rapor Yaz Butonları -->
    @if($sample->report_date)
        <div class="mt-3">
            <a href="{{ route('samples.report.generate',['sample'=>$sample->id,'lang'=>'TR']) }}" class="btn btn-primary">Rapor Yaz (TR)</a>
            <a href="{{ route('samples.report.generate',['sample'=>$sample->id,'lang'=>'EN']) }}" class="btn btn-secondary">Rapor Yaz (EN)</a>
            <a href="{{ route('samples.report.generate',['sample'=>$sample->id,'lang'=>'RU']) }}" class="btn btn-dark">Rapor Yaz (RU)</a>
        </div>
    @else
        <div class="alert alert-warning mt-3">⚠ Önce rapor tarihi ve açıklama kaydedilmelidir.</div>
    @endif

    <!-- ✅ En altta rapor tarihi ve resimler -->
    <div class="mt-4">
        @if($sample->report_date)
            <p><strong>📅 Rapor Tarihi:</strong> {{ $sample->report_date->format('d.m.Y') }}</p>
        @endif

        @if(!empty($sample->report_images))
            <h5>📷 Rapor Resimleri</h5>
            <div class="d-flex flex-wrap gap-3">
                @foreach($sample->report_images as $index => $img)
                    <div class="text-center">
                        <!-- Thumbnail + Modal Trigger -->
                        <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal{{ $index }}">
                            <img src="{{ asset('storage/reports/'.$img) }}"
                                 alt="Rapor Resmi"
                                 class="img-thumbnail"
                                 style="width:150px; height:auto;">
                        </a>

                        <!-- Silme Formu -->
                        <form action="{{ route('samples.report.image.delete',$sample->id) }}" method="POST" class="mt-1">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="image" value="{{ $img }}">
                            <button type="submit" class="btn btn-sm btn-danger">Sil</button>
                        </form>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="imageModal{{ $index }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-body text-center">
                                    <img src="{{ asset('storage/reports/'.$img) }}" class="img-fluid" alt="Rapor Resmi">
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection