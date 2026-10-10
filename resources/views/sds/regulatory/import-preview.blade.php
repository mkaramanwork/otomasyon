@extends('layouts.admin')



@section('title','Mevzuat Import Önizleme')



@section('content')



<div class="container-fluid">



    <div class="d-flex justify-content-between align-items-center mb-3">



        <div>



            <h4 class="mb-0">

                🔍 Mevzuat Import Önizleme

            </h4>



            <small class="text-muted">

                Dosya verileri henüz veritabanına aktarılmamıştır.

            </small>



        </div>



        <a href="{{ route('sds.regulatory.import') }}"

           class="btn btn-outline-secondary">



            ← Geri Dön



        </a>



    </div>





    <div class="card mb-3">



        <div class="card-header bg-dark text-white">



            <strong>

                Kaynak Bilgileri

            </strong>



        </div>



        <div class="card-body">



            <div class="row">



                <div class="col-md">



                    <strong>

                        Mevzuat Profili

                    </strong>



                    <div>

                        {{ $regulationProfile }}

                    </div>



                </div>



                <div class="col-md">



                    <strong>

                        Kaynak

                    </strong>



                    <div>

                        {{ $sourceName }}

                    </div>



                </div>



                <div class="col-md">



                    <strong>

                        Versiyon

                    </strong>



                    <div>

                        {{ $sourceVersion ?? '-' }}

                    </div>



                </div>



                <div class="col-md">



                    <strong>

                        Kaynak Tarihi

                    </strong>



                    <div>

                        {{ $sourceDate ?? '-' }}

                    </div>



                </div>



                <div class="col-md">



                    <strong>

                        Dosya

                    </strong>



                    <div>

                        {{ $fileName }}

                    </div>



                </div>



            </div>



        </div>



    </div>





    <div class="row mb-3">



        <div class="col-md">



            <div class="card border-success">



                <div class="card-body text-center">



                    <div class="fs-3 fw-bold">

                        {{ $summary['new'] }}

                    </div>



                    <div class="text-success">

                        YENİ

                    </div>



                </div>



            </div>



        </div>





        <div class="col-md">



            <div class="card border-warning">



                <div class="card-body text-center">



                    <div class="fs-3 fw-bold">

                        {{ $summary['changed'] }}

                    </div>



                    <div class="text-warning">

                        DEĞİŞMİŞ

                    </div>



                </div>



            </div>



        </div>





        <div class="col-md">



            <div class="card">



                <div class="card-body text-center">



                    <div class="fs-3 fw-bold">

                        {{ $summary['same'] }}

                    </div>



                    <div class="text-muted">

                        AYNI

                    </div>



                </div>



            </div>



        </div>





        <div class="col-md">



            <div class="card border-danger">



                <div class="card-body text-center">



                    <div class="fs-3 fw-bold">

                        {{ $summary['invalid'] }}

                    </div>



                    <div class="text-danger">

                        HATALI

                    </div>



                </div>



            </div>



        </div>





        <div class="col-md">



            <div class="card border-primary">



                <div class="card-body text-center">



                    <div class="fs-3 fw-bold">

                        {{ $summary['total'] }}

                    </div>



                    <div class="text-primary">

                        TOPLAM

                    </div>



                </div>



            </div>



        </div>



    </div>





    @if(!empty($summary['reach_breakdown']))
        @php
            $reachBreakdown = $summary['reach_breakdown'];
        @endphp

        <div class="card mb-3">
            <div class="card-header bg-dark text-white">
                <strong>REACH Registered Değişiklik Analizi</strong>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4 col-xl-2">
                        <div class="border rounded p-3 h-100 text-center">
                            <div class="fs-4 fw-bold">{{ number_format($reachBreakdown['source_record_missing'] ?? 0) }}</div>
                            <div class="small text-muted">Sadece REACH Kaydı Eksik</div>
                        </div>
                    </div>

                    <div class="col-md-4 col-xl-2">
                        <div class="border rounded p-3 h-100 text-center">
                            <div class="fs-4 fw-bold">{{ number_format($reachBreakdown['name_changed'] ?? 0) }}</div>
                            <div class="small text-muted">Madde Adı Farklı</div>
                        </div>
                    </div>

                    <div class="col-md-4 col-xl-2">
                        <div class="border rounded p-3 h-100 text-center">
                            <div class="fs-4 fw-bold">{{ number_format($reachBreakdown['ec_changed'] ?? 0) }}</div>
                            <div class="small text-muted">EC No Farklı</div>
                        </div>
                    </div>

                    <div class="col-md-4 col-xl-2">
                        <div class="border border-danger rounded p-3 h-100 text-center">
                            <div class="fs-4 fw-bold text-danger">{{ number_format($reachBreakdown['cas_conflict'] ?? 0) }}</div>
                            <div class="small text-muted">CAS Çakışması</div>
                        </div>
                    </div>

                    <div class="col-md-4 col-xl-2">
                        <div class="border rounded p-3 h-100 text-center">
                            <div class="fs-4 fw-bold">{{ number_format($reachBreakdown['echa_identity_changed'] ?? 0) }}</div>
                            <div class="small text-muted">ECHA Kimliği / URL</div>
                        </div>
                    </div>

                    <div class="col-md-4 col-xl-2">
                        <div class="border border-warning rounded p-3 h-100 text-center">
                            <div class="fs-4 fw-bold text-warning">{{ number_format($reachBreakdown['real_data_change'] ?? 0) }}</div>
                            <div class="small text-muted">Gerçek Veri Değişikliği</div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-3 mb-0">
                    <strong>Kontrol mantığı:</strong>
                    Kategoriler birbiriyle çakışabilir. “Gerçek Veri Değişikliği”, yalnızca REACH kaynak kaydının eksik olmasından farklı olarak ad, EC, CAS veya ECHA kimliği/URL tarafında fark bulunan tekil kayıt sayısını gösterir. CAS çakışmaları otomatik olarak üzerine yazılmaz.
                </div>
            </div>
        </div>
    @endif

    <div class="card mb-3">



        <div class="card-header bg-dark text-white">



            <strong>

                Dosya Karşılaştırması

            </strong>



        </div>





        <div class="card-body p-0">



            <div class="table-responsive">



                <table class="table table-striped table-hover table-bordered mb-0">



                    <thead>



                        <tr>



                            <th>Satır</th>



                            <th style="min-width:180px;">

                                Kimyasal

                            </th>



                            <th style="min-width:150px;">

                                Kimlik

                            </th>



                            <th style="min-width:180px;">

                                Sınıflandırma

                            </th>



                            <th style="min-width:150px;">

                                GHS / Signal

                            </th>



                            <th style="min-width:160px;">

                                H / EUH

                            </th>



                            <th style="min-width:150px;">

                                SCL / ATE / M

                            </th>



                            <th style="min-width:170px;">

                                Mevzuat

                            </th>



                            <th>Durum</th>



                            <th style="min-width:250px;">

                                Fark

                            </th>



                        </tr>



                    </thead>





                    <tbody>



                        @foreach($previewRows as $row)



                            <tr>



                                <td>

                                    {{ $row['row_number'] }}

                                </td>





                                <td>



                                    <strong>

                                        {{ $row['preferred_name'] ?: '-' }}

                                    </strong>



                                </td>





                                <td>



                                    <div>

                                        <strong>CAS:</strong>

                                        {{ $row['cas_no'] ?: '-' }}

                                    </div>



                                    <div>

                                        <strong>EC:</strong>

                                        {{ $row['ec_no'] ?: '-' }}

                                    </div>



                                    <div>

                                        <strong>Index:</strong>

                                        {{ $row['index_no'] ?: '-' }}

                                    </div>



                                </td>





                                <td>



                                    @if(is_array($row['hazard_classifications']))



                                        @foreach($row['hazard_classifications'] as $item)



                                            <span class="badge text-bg-secondary mb-1">

                                                {{ $item }}

                                            </span>



                                        @endforeach



                                    @else



                                        -



                                    @endif



                                </td>





                                <td>



                                    <div class="mb-1">



                                        <strong>

                                            {{ $row['signal_word'] ?: '-' }}

                                        </strong>



                                    </div>



                                    @if(is_array($row['pictograms']))



                                        @foreach($row['pictograms'] as $item)



                                            <span class="badge text-bg-danger mb-1">

                                                {{ $item }}

                                            </span>



                                        @endforeach



                                    @endif



                                </td>





                                <td>



                                    @if(is_array($row['hazard_statements']))



                                        @foreach($row['hazard_statements'] as $item)



                                            <span class="badge text-bg-warning mb-1">

                                                {{ $item }}

                                            </span>



                                        @endforeach



                                    @endif





                                    @if(is_array($row['supplemental_hazard_statements']))



                                        @foreach($row['supplemental_hazard_statements'] as $item)



                                            <span class="badge text-bg-info mb-1">

                                                {{ $item }}

                                            </span>



                                        @endforeach



                                    @endif



                                </td>





                                <td>



                                    <div>

                                        <strong>SCL:</strong>



                                        {{ $row['specific_concentration_limits']

                                            ? json_encode($row['specific_concentration_limits'], JSON_UNESCAPED_UNICODE)

                                            : '-' }}

                                    </div>



                                    <div>

                                        <strong>ATE:</strong>



                                        {{ $row['ate_data']

                                            ? json_encode($row['ate_data'], JSON_UNESCAPED_UNICODE)

                                            : '-' }}

                                    </div>



                                    <div>

                                        <strong>M Akut:</strong>

                                        {{ $row['m_factor_acute'] ?? '-' }}

                                    </div>



                                    <div>

                                        <strong>M Kronik:</strong>

                                        {{ $row['m_factor_chronic'] ?? '-' }}

                                    </div>



                                </td>





                                <td>



                                    <div>

                                        REACH:

                                        {{ $row['reach_registered'] === true ? 'Evet' : ($row['reach_registered'] === false ? 'Hayır' : '-') }}

                                    </div>



                                    <div>

                                        SVHC:

                                        {{ $row['svhc'] === true ? 'Evet' : ($row['svhc'] === false ? 'Hayır' : '-') }}

                                    </div>



                                    <div>

                                        Annex VI:

                                        {{ $row['annex_vi'] === true ? 'Evet' : ($row['annex_vi'] === false ? 'Hayır' : '-') }}

                                    </div>



                                    <div>

                                        Annex XIV:

                                        {{ $row['annex_xiv'] === true ? 'Evet' : ($row['annex_xiv'] === false ? 'Hayır' : '-') }}

                                    </div>



                                    <div>

                                        Annex XVII:

                                        {{ $row['annex_xvii'] === true ? 'Evet' : ($row['annex_xvii'] === false ? 'Hayır' : '-') }}

                                    </div>



                                </td>





                                <td>



                                    @if($row['status'] === 'new')



                                        <span class="badge text-bg-success">

                                            YENİ

                                        </span>



                                    @elseif($row['status'] === 'changed')



                                        <span class="badge text-bg-warning">

                                            DEĞİŞMİŞ

                                        </span>



                                    @elseif($row['status'] === 'same')



                                        <span class="badge text-bg-secondary">

                                            AYNI

                                        </span>



                                    @else



                                        <span class="badge text-bg-danger">

                                            HATALI

                                        </span>



                                    @endif



                                </td>





                                <td>



                                    @if(count($row['errors']) > 0)



                                        @foreach($row['errors'] as $error)



                                            <div class="text-danger">

                                                {{ $error }}

                                            </div>



                                        @endforeach



                                    @endif
                                    @foreach(($row['warnings'] ?? []) as $warning)
                                        <div class="text-warning">⚠ {{ $warning }}</div>
                                    @endforeach
                                    @if(count($row['errors']) > 0)
                                        {{-- Errors shown above; do not repeat changes. --}}
                                    @elseif(count($row['differences']) > 0)



                                        @foreach($row['differences'] as $difference)



                                            <div>

                                                • {{ $difference }}

                                            </div>



                                        @endforeach



                                    @elseif($row['status'] === 'new')



                                        <span class="text-success">

                                            Yeni mevzuat kaydı.

                                        </span>



                                    @else



                                        <span class="text-muted">

                                            Değişiklik yok.

                                        </span>



                                    @endif



                                </td>



                            </tr>



                        @endforeach



                    </tbody>



                </table>



            </div>



        </div>



    </div>





    @if($summary['invalid'] > 0)

        <div class="alert alert-warning">



            <strong>

                Kısmi aktarım yapılabilir.

            </strong>



            Dosyada {{ $summary['invalid'] }} hatalı satır bulunuyor.

            Bu satırlar listede gösterilir ve aktarım sırasında atlanır. Diğer kayıtlar aktarılabilir.



        </div>



    @endif

        <div class="alert alert-warning">



            <strong>

                Aktarım henüz yapılmadı.

            </strong>



            Onay verdiğinizde YENİ ve DEĞİŞMİŞ kayıtlar

            mevzuat veritabanına aktarılacaktır.



            AYNI kayıtlar yeniden oluşturulmayacaktır.



        </div>





        <form method="POST"

              action="{{ route('sds.regulatory.import.store') }}"

              onsubmit="return confirm('Mevzuat kayıtlarını veritabanına aktarmak istediğinize emin misiniz?')">



            @csrf



            <input type="hidden"

                   name="import_token"

                   value="{{ $importToken }}">



            <div class="d-flex justify-content-between">



                <a href="{{ route('sds.regulatory.import') }}"

                   class="btn btn-outline-secondary">



                    ← Vazgeç



                </a>



                <button type="submit"

                        class="btn btn-success btn-lg">



                    ✓ Onayla ve Aktar



                </button>



            </div>



        </form>







</div>



@endsection