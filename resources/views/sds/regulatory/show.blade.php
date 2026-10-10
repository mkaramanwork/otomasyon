@extends('layouts.admin')

@section('title','Mevzuat Maddesi Detay')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>

            <h4 class="mb-0">
                🌐 Mevzuat Maddesi Detay
            </h4>

            <small class="text-muted">
                {{ $substance->preferred_name }}
            </small>

        </div>

        <div>

            <a href="{{ route('sds.regulatory.index') }}"
               class="btn btn-outline-secondary">

                ← Mevzuat Veritabanı

            </a>

        </div>

    </div>


    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    @if($errors->any())

        <div class="alert alert-danger">

            <strong>
                İşlem gerçekleştirilemedi.
            </strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    <div class="card mb-3">

        <div class="card-header bg-dark text-white">

            <div class="d-flex justify-content-between">

                <strong>
                    Kimyasal Kimliği
                </strong>

                <div>

                    @if($substance->verification_status === 'verified')

                        <span class="badge text-bg-success">
                            ✓ Doğrulandı
                        </span>

                    @else

                        <span class="badge text-bg-warning">
                            Kontrol Gerekli
                        </span>

                    @endif

                </div>

            </div>

        </div>


        <div class="card-body">

            <div class="row">

                <div class="col-md-4 mb-3">

                    <strong>Kimyasal Adı</strong>

                    <div>
                        {{ $substance->preferred_name ?? '-' }}
                    </div>

                </div>


                <div class="col-md-2 mb-3">

                    <strong>CAS No</strong>

                    <div>
                        {{ $substance->cas_no ?? '-' }}
                    </div>

                </div>


                <div class="col-md-2 mb-3">

                    <strong>EC No</strong>

                    <div>
                        {{ $substance->ec_no ?? '-' }}
                    </div>

                </div>


                <div class="col-md-2 mb-3">

                    <strong>Index No</strong>

                    <div>
                        {{ $substance->index_no ?? '-' }}
                    </div>

                </div>


                <div class="col-md-2 mb-3">

                    <strong>Madde Türü</strong>

                    <div>
                        {{ $substance->substance_type ?? '-' }}
                    </div>

                </div>


                <div class="col-md-3 mb-3">

                    <strong>Kaynak</strong>

                    <div>
                        {{ $substance->created_from ?? '-' }}
                    </div>

                </div>


                <div class="col-md-3 mb-3">

                    <strong>ECHA UUID</strong>

                    <div>
                        {{ $substance->echa_uuid ?? '-' }}
                    </div>

                </div>


                <div class="col-md-3 mb-3">

                    <strong>Son ECHA Kontrolü</strong>

                    <div>

                        @if($substance->last_echa_check_at)

                            {{ $substance->last_echa_check_at->format('d.m.Y H:i') }}

                        @else

                            -

                        @endif

                    </div>

                </div>


                <div class="col-md-3 mb-3">

                    <strong>SDS Eşleşmesi</strong>

                    <div>

                        <span class="badge text-bg-primary">
                            {{ $matchedComponents->count() }}
                            bileşen
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="card mb-3">

        <div class="card-header bg-dark text-white">

            <div class="d-flex justify-content-between align-items-center">

                <strong>
                    Mevzuat Sınıflandırmaları
                </strong>

                <button type="button"
                        class="btn btn-success btn-sm"
                        data-bs-toggle="collapse"
                        data-bs-target="#classificationForm">

                    ➕ Sınıflandırma Ekle

                </button>

            </div>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-striped table-hover table-bordered mb-0">

                    <thead>

                        <tr>

                            <th>Profil</th>

                            <th>Kaynak</th>

                            <th>Sınıflandırma</th>

                            <th>Signal Word</th>

                            <th>Piktogram</th>

                            <th>H / EUH</th>

                            <th>Annex VI</th>

                            <th>SVHC</th>

                            <th>Durum</th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($classifications as $classification)

                            <tr>

                                <td>

                                    <strong>
                                        {{ $classification->regulation_profile }}
                                    </strong>

                                    @if($classification->is_current)

                                        <div>
                                            <span class="badge text-bg-success">
                                                Güncel
                                            </span>
                                        </div>

                                    @else

                                        <div>
                                            <span class="badge text-bg-secondary">
                                                Eski
                                            </span>
                                        </div>

                                    @endif

                                </td>


                                <td>

                                    {{ $classification->source_name ?? '-' }}

                                    @if($classification->source_date)

                                        <div class="small text-muted">
                                            {{ $classification->source_date->format('d.m.Y') }}
                                        </div>

                                    @endif

                                </td>


                                <td>

                                    @if(is_array($classification->hazard_classifications))

                                        @foreach($classification->hazard_classifications as $item)

                                            <span class="badge text-bg-secondary mb-1">
                                                {{ $item }}
                                            </span>

                                        @endforeach

                                    @else

                                        -

                                    @endif

                                </td>


                                <td>
                                    {{ $classification->signal_word ?? '-' }}
                                </td>


                                <td>

                                    @if(is_array($classification->pictograms))

                                        @foreach($classification->pictograms as $item)

                                            <span class="badge text-bg-danger mb-1">
                                                {{ $item }}
                                            </span>

                                        @endforeach

                                    @else

                                        -

                                    @endif

                                </td>


                                <td>

                                    @if(is_array($classification->hazard_statements))

                                        @foreach($classification->hazard_statements as $item)

                                            <span class="badge text-bg-warning mb-1">
                                                {{ $item }}
                                            </span>

                                        @endforeach

                                    @endif


                                    @if(is_array($classification->supplemental_hazard_statements))

                                        @foreach($classification->supplemental_hazard_statements as $item)

                                            <span class="badge text-bg-info mb-1">
                                                {{ $item }}
                                            </span>

                                        @endforeach

                                    @endif

                                </td>


                                <td>

                                    @if($classification->annex_vi)

                                        <span class="badge text-bg-success">
                                            Evet
                                        </span>

                                    @else

                                        <span class="badge text-bg-secondary">
                                            Hayır
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    @if($classification->svhc)

                                        <span class="badge text-bg-danger">
                                            Evet
                                        </span>

                                    @else

                                        <span class="badge text-bg-secondary">
                                            Hayır
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    @if($classification->is_verified)

                                        <span class="badge text-bg-success">
                                            ✓ Doğrulandı
                                        </span>

                                    @else

                                        <span class="badge text-bg-warning">
                                            Kontrol Bekliyor
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="9"
                                    class="text-center py-5">

                                    <div class="text-muted mb-3">

                                        Bu kimyasal için henüz mevzuat
                                        sınıflandırması bulunmuyor.

                                    </div>

                                    <button type="button"
                                            class="btn btn-success"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#classificationForm">

                                        ➕ İlk Sınıflandırmayı Ekle

                                    </button>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <div class="collapse {{ $errors->any() ? 'show' : '' }}"
         id="classificationForm">

        <div class="card mb-3">

            <div class="card-header">

                <strong>
                    ➕ Mevzuat Sınıflandırması Ekle
                </strong>

            </div>


            <div class="card-body">

                <div class="alert alert-warning">

                    Bu aşamada kayıt manuel girilmektedir.

                    Bu nedenle kayıt
                    <strong>Kontrol Bekliyor</strong>
                    durumunda oluşturulacaktır.

                </div>


                <form method="POST"
                      action="{{ route(
                          'sds.regulatory.classifications.store',
                          $substance->id
                      ) }}">

                    @csrf


                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <label class="form-label fw-bold">
                                Mevzuat Profili *
                            </label>

                            <select name="regulation_profile"
                                    class="form-select"
                                    required>

                                <option value="">
                                    Seçiniz
                                </option>

                                <option value="EU_REACH_CLP"
                                    {{ old('regulation_profile') === 'EU_REACH_CLP' ? 'selected' : '' }}>

                                    EU REACH / CLP

                                </option>

                                <option value="TR_KKDIK_SEA"
                                    {{ old('regulation_profile') === 'TR_KKDIK_SEA' ? 'selected' : '' }}>

                                    TR KKDİK / SEA

                                </option>

                            </select>

                        </div>


                        <div class="col-md-2 mb-3">

                            <label class="form-label fw-bold">
                                Yetki Alanı
                            </label>

                            <input type="text"
                                   name="jurisdiction"
                                   class="form-control"
                                   value="{{ old('jurisdiction') }}"
                                   placeholder="EU / TR">

                        </div>


                        <div class="col-md-3 mb-3">

                            <label class="form-label fw-bold">
                                Kaynak Adı *
                            </label>

                            <input type="text"
                                   name="source_name"
                                   class="form-control"
                                   value="{{ old('source_name') }}"
                                   placeholder="Örn: ECHA Annex VI"
                                   required>

                        </div>


                        <div class="col-md-3 mb-3">

                            <label class="form-label fw-bold">
                                Kaynak Tarihi
                            </label>

                            <input type="date"
                                   name="source_date"
                                   class="form-control"
                                   value="{{ old('source_date') }}">

                        </div>


                        <div class="col-md-4 mb-3">

                            <label class="form-label fw-bold">
                                Tehlike Sınıflandırması
                            </label>

                            <input type="text"
                                   name="hazard_classifications_text"
                                   class="form-control"
                                   value="{{ old('hazard_classifications_text') }}"
                                   placeholder="Örn: Skin Irrit. 2, Aquatic Chronic 2">

                        </div>


                        <div class="col-md-2 mb-3">

                            <label class="form-label fw-bold">
                                Signal Word
                            </label>

                            <select name="signal_word"
                                    class="form-select">

                                <option value="">
                                    Yok
                                </option>

                                <option value="Danger"
                                    {{ old('signal_word') === 'Danger' ? 'selected' : '' }}>

                                    Danger

                                </option>

                                <option value="Warning"
                                    {{ old('signal_word') === 'Warning' ? 'selected' : '' }}>

                                    Warning

                                </option>

                            </select>

                        </div>


                        <div class="col-md-3 mb-3">

                            <label class="form-label fw-bold">
                                Piktogramlar
                            </label>

                            <input type="text"
                                   name="pictograms_text"
                                   class="form-control"
                                   value="{{ old('pictograms_text') }}"
                                   placeholder="Örn: GHS07, GHS09">

                        </div>


                        <div class="col-md-3 mb-3">

                            <label class="form-label fw-bold">
                                H Kodları
                            </label>

                            <input type="text"
                                   name="hazard_statements_text"
                                   class="form-control"
                                   value="{{ old('hazard_statements_text') }}"
                                   placeholder="Örn: H315, H411">

                        </div>


                        <div class="col-md-4 mb-3">

                            <label class="form-label fw-bold">
                                EUH Kodları
                            </label>

                            <input type="text"
                                   name="supplemental_hazard_statements_text"
                                   class="form-control"
                                   value="{{ old('supplemental_hazard_statements_text') }}"
                                   placeholder="Örn: EUH208">

                        </div>


                        <div class="col-md-2 mb-3">

                            <label class="form-label fw-bold">
                                M-Factor Akut
                            </label>

                            <input type="number"
                                   step="0.0001"
                                   min="0"
                                   name="m_factor_acute"
                                   class="form-control"
                                   value="{{ old('m_factor_acute') }}">

                        </div>


                        <div class="col-md-2 mb-3">

                            <label class="form-label fw-bold">
                                M-Factor Kronik
                            </label>

                            <input type="number"
                                   step="0.0001"
                                   min="0"
                                   name="m_factor_chronic"
                                   class="form-control"
                                   value="{{ old('m_factor_chronic') }}">

                        </div>


                        <div class="col-md-2 mb-3">

                            <label class="form-label fw-bold">
                                Yürürlük Başlangıcı
                            </label>

                            <input type="date"
                                   name="effective_from"
                                   class="form-control"
                                   value="{{ old('effective_from') }}">

                        </div>


                        <div class="col-md-2 mb-3">

                            <label class="form-label fw-bold">
                                Yürürlük Sonu
                            </label>

                            <input type="date"
                                   name="effective_until"
                                   class="form-control"
                                   value="{{ old('effective_until') }}">

                        </div>

                    </div>


                    <hr>


                    <div class="row">

                        <div class="col-md-2 mb-3">

                            <div class="form-check">

                                <input class="form-check-input"
                                       type="checkbox"
                                       name="reach_registered"
                                       value="1"
                                       id="reachRegistered"
                                       {{ old('reach_registered') ? 'checked' : '' }}>

                                <label class="form-check-label"
                                       for="reachRegistered">

                                    REACH Kayıtlı

                                </label>

                            </div>

                        </div>


                        <div class="col-md-2 mb-3">

                            <div class="form-check">

                                <input class="form-check-input"
                                       type="checkbox"
                                       name="svhc"
                                       value="1"
                                       id="svhc"
                                       {{ old('svhc') ? 'checked' : '' }}>

                                <label class="form-check-label"
                                       for="svhc">

                                    SVHC

                                </label>

                            </div>

                        </div>


                        <div class="col-md-2 mb-3">

                            <div class="form-check">

                                <input class="form-check-input"
                                       type="checkbox"
                                       name="annex_vi"
                                       value="1"
                                       id="annexVI"
                                       {{ old('annex_vi') ? 'checked' : '' }}>

                                <label class="form-check-label"
                                       for="annexVI">

                                    Annex VI

                                </label>

                            </div>

                        </div>


                        <div class="col-md-2 mb-3">

                            <div class="form-check">

                                <input class="form-check-input"
                                       type="checkbox"
                                       name="annex_xiv"
                                       value="1"
                                       id="annexXIV"
                                       {{ old('annex_xiv') ? 'checked' : '' }}>

                                <label class="form-check-label"
                                       for="annexXIV">

                                    Annex XIV

                                </label>

                            </div>

                        </div>


                        <div class="col-md-2 mb-3">

                            <div class="form-check">

                                <input class="form-check-input"
                                       type="checkbox"
                                       name="annex_xvii"
                                       value="1"
                                       id="annexXVII"
                                       {{ old('annex_xvii') ? 'checked' : '' }}>

                                <label class="form-check-label"
                                       for="annexXVII">

                                    Annex XVII

                                </label>

                            </div>

                        </div>

                    </div>


                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <label class="form-label fw-bold">
                                Annex VI Kaydı
                            </label>

                            <input type="text"
                                   name="annex_vi_entry"
                                   class="form-control"
                                   value="{{ old('annex_vi_entry') }}">

                        </div>


                        <div class="col-md-4 mb-3">

                            <label class="form-label fw-bold">
                                Annex XIV Kaydı
                            </label>

                            <input type="text"
                                   name="annex_xiv_entry"
                                   class="form-control"
                                   value="{{ old('annex_xiv_entry') }}">

                        </div>


                        <div class="col-md-4 mb-3">

                            <label class="form-label fw-bold">
                                Annex XVII Kaydı
                            </label>

                            <input type="text"
                                   name="annex_xvii_entry"
                                   class="form-control"
                                   value="{{ old('annex_xvii_entry') }}">

                        </div>

                    </div>


                    <div class="text-end">

                        <button type="submit"
                                class="btn btn-success">

                            💾 Sınıflandırmayı Kaydet

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    <div class="card">

        <div class="card-header">

            <strong>
                Bu CAS ile Eşleşen SDS Bileşenleri
            </strong>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-bordered table-hover mb-0">

                    <thead>

                        <tr>
                            <th>Hammadde</th>
                            <th>Kimyasal</th>
                            <th>CAS</th>
                            <th>Oran</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($matchedComponents as $component)

                            <tr>

                                <td>
                                    {{ $component->sdsProfile?->rawMaterial?->name ?? '-' }}
                                </td>

                                <td>
                                    {{ $component->chemical_name }}
                                </td>

                                <td>
                                    {{ $component->cas_no ?? '-' }}
                                </td>

                                <td>

                                    {{ $component->concentration_min ?? '?' }}
                                    -
                                    {{ $component->concentration_max ?? '?' }}
                                    %

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="4"
                                    class="text-center text-muted py-4">

                                    Bu kimyasal henüz hiçbir SDS bileşeni ile eşleşmemiş.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection