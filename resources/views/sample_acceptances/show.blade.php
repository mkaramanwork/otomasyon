@extends('layouts.admin')

@section('title','Numune Rapor Detayı')

@section('content')

@if(session('success'))
    <div class="alert alert-success mt-3">
        {{ session('success') }}
    </div>
@endif

<div class="container">

    <h4>📑 Numune Rapor Detayı</h4>

    <p><strong>Ürün:</strong> {{ $sample->labProduct->product_name ?? '-' }}</p>
    <p><strong>Firma:</strong> {{ $sample->company_name }}</p>
    <p><strong>Batch No:</strong> {{ $sample->batch_no }}</p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Parametre</th>
                <th>Metot</th>
                <th>Min</th>
                <th>Max</th>
                <th>Net</th>
                <th>Sonuç</th>
                <th>Durum</th>
            </tr>
        </thead>

        <tbody>
            @foreach($sample->parameters as $param)

                @php
                    $limits = $sample->labProduct->parameters->firstWhere(
                        'parameter_id',
                        $param->id
                    );

                    $result = $param->pivot->result;

                    $min = $limits->min_value ?? '-';
                    $max = $limits->max_value ?? '-';
                    $net = $limits->net_value ?? '-';

                    $status = 'Bekliyor';
                    $class = '';

                    if (
                        ($result === '-' || is_null($result)) &&
                        $min === '-' &&
                        $max === '-' &&
                        $net === '-'
                    ) {
                        $status = 'Yorumsuz';
                        $class = 'table-secondary';

                    } elseif (!is_null($result) && $result !== '-') {

                        if ($min !== '-' && $max !== '-') {

                            if ($result < $min) {
                                $status = 'Altında';
                                $class = 'table-danger';

                            } elseif ($result > $max) {
                                $status = 'Üzerinde';
                                $class = 'table-danger';

                            } else {
                                $status = 'Uygun';
                                $class = 'table-success';
                            }

                        } elseif ($net !== '-') {

                            if ($result == $net) {
                                $status = 'Uygun';
                                $class = 'table-success';

                            } else {
                                $status = 'Uygun Değil';
                                $class = 'table-danger';
                            }

                        } else {
                            $status = 'Kontrol Ediniz';
                            $class = 'table-warning';
                        }
                    }
                @endphp

                <tr class="{{ $class }}">
                    <td>{{ $param->parameter }}</td>
                    <td>{{ $param->method_tr ?? '-' }}</td>
                    <td>{{ $min }}</td>
                    <td>{{ $max }}</td>
                    <td>{{ $net }}</td>
                    <td>{{ $result ?? 'Bekliyor' }}</td>
                    <td>{{ $status }}</td>
                </tr>

            @endforeach
        </tbody>
    </table>

    <form
        method="POST"
        action="{{ route('samples.report.store') }}"
        enctype="multipart/form-data"
        class="d-flex gap-2 mt-3"
    >
        @csrf

        <input
            type="hidden"
            name="sample_id"
            value="{{ $sample->id }}"
        >

        <input
            type="date"
            name="report_date"
            value="{{ $sample->report_date }}"
            class="form-control w-auto"
            required
        >

        <input
            type="text"
            name="description"
            value="{{ $sample->report_description }}"
            class="form-control w-50"
            placeholder="Açıklama"
        >

        <input
            type="file"
            name="images[]"
            class="form-control w-auto"
            multiple
        >

        <button
            type="submit"
            class="btn btn-success"
        >
            Kaydet
        </button>
    </form>

    @if($sample->report_date)

        @php
            $qrFileName = "raporno-{$sample->report_no}.svg";
            $qrFilePath = public_path("reports/{$qrFileName}");
            $qrExists = file_exists($qrFilePath);
        @endphp

        <div class="mt-3 d-flex align-items-start gap-4">

            <div>

                <a
                    href="{{ route('samples.report.generate',['sample'=>$sample->id,'lang'=>'TR']) }}"
                    class="btn btn-primary"
                >
                    Rapor Yaz (TR)
                </a>

                <a
                    href="{{ route('samples.report.generate',['sample'=>$sample->id,'lang'=>'EN']) }}"
                    class="btn btn-secondary"
                >
                    Rapor Yaz (EN)
                </a>

                <a
                    href="{{ route('samples.report.generate',['sample'=>$sample->id,'lang'=>'RU']) }}"
                    class="btn btn-dark"
                >
                    Rapor Yaz (RU)
                </a>

            </div>

            @if($qrExists)

                <div class="text-center">

                    <img
                        src="{{ asset('reports/'.$qrFileName) }}"
                        alt="QR Kod"
                        style="width:80px; height:80px;"
                    >

                    <div class="mt-2">

                        <a
                            href="{{ asset('reports/'.$qrFileName) }}"
                            class="btn btn-sm btn-info"
                            download
                        >
                            📥 QR İndir
                        </a>

                    </div>

                    <small class="text-muted d-block mt-1">
                        Doğrulama için tarayın
                    </small>

                </div>

            @endif

        </div>

    @else

        <div class="alert alert-warning mt-3">
            ⚠ Önce rapor tarihi ve açıklama kaydedilmelidir.
        </div>

    @endif

    <div class="mt-4">

        @if($sample->report_date)

            <p>
                <strong>📅 Rapor Tarihi:</strong>
                {{ $sample->report_date->format('d.m.Y') }}
            </p>

        @endif

        @if(!empty($sample->report_images))

            <h5>📷 Rapor Resimleri</h5>

            <div class="d-flex flex-wrap gap-3">

                @foreach($sample->report_images as $index => $img)

                    <div class="text-center">

                        <a
                            href="#"
                            data-bs-toggle="modal"
                            data-bs-target="#imageModal{{ $index }}"
                        >

                            <img
                                src="{{ asset('reports/picture/thumbs/'.$img) }}"
                                alt="Rapor Resmi"
                                class="img-thumbnail"
                                style="width:150px; height:auto;"
                            >

                        </a>

                        <form
                            action="{{ route('samples.report.image.delete',$sample->id) }}"
                            method="POST"
                            class="mt-1"
                        >
                            @csrf
                            @method('DELETE')

                            <input
                                type="hidden"
                                name="image"
                                value="{{ $img }}"
                            >

                            <button
                                type="submit"
                                class="btn btn-sm btn-danger"
                            >
                                Sil
                            </button>

                        </form>

                    </div>

                    <div
                        class="modal fade"
                        id="imageModal{{ $index }}"
                        tabindex="-1"
                        aria-hidden="true"
                    >

                        <div class="modal-dialog modal-dialog-centered modal-lg">

                            <div class="modal-content">

                                <div class="modal-body text-center">

                                    <img
                                        src="{{ asset('reports/picture/'.$img) }}"
                                        class="img-fluid"
                                        alt="Rapor Resmi"
                                    >

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