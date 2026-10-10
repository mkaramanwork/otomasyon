@extends('layouts.admin')

@section('title','Hammadde SDS Yönet')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>

            <h4 class="mb-0">
                ✏️ Hammadde SDS Yönet
            </h4>

            <small class="text-muted">
                {{ $rawMaterial->name }}
            </small>

        </div>

        <div class="d-flex gap-2">

            <a href="{{ route('sds.raw-materials.show', [$rawMaterial->id, $profile->id]) }}"
               class="btn btn-outline-secondary">

                ← SDS Detay

            </a>

        </div>

    </div>


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


    <form method="POST"
          action="{{ route('sds.raw-materials.update', [
              $rawMaterial->id,
              $profile->id
          ]) }}"
          enctype="multipart/form-data">

        @csrf
        @method('PUT')


        <div class="card mb-3">

            <div class="card-header bg-dark text-white">

                <strong>
                    Hammadde
                </strong>

            </div>

            <div class="card-body">

                <input type="text"
                       class="form-control"
                       value="{{ $rawMaterial->name }}"
                       disabled>

            </div>

        </div>


        <div class="card mb-3">

            <div class="card-header">

                <strong>
                    1. Tedarikçi SDS Bilgileri
                </strong>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">
                            Tedarikçi Adı
                        </label>

                        <input type="text"
                               name="supplier_name"
                               class="form-control"
                               value="{{ old('supplier_name', $profile->supplier_name) }}">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">
                            Tedarikçi Ürün Adı
                        </label>

                        <input type="text"
                               name="supplier_product_name"
                               class="form-control"
                               value="{{ old('supplier_product_name', $profile->supplier_product_name) }}">

                    </div>


                    <div class="col-md-3 mb-3">

                        <label class="form-label fw-bold">
                            SDS Dili
                        </label>

                        <select name="sds_language"
                                class="form-select">

                            @foreach([
                                'tr' => 'Türkçe',
                                'en' => 'İngilizce',
                                'de' => 'Almanca',
                                'fr' => 'Fransızca',
                                'es' => 'İspanyolca'
                            ] as $code => $label)

                                <option value="{{ $code }}"
                                    {{ old('sds_language', $profile->sds_language) == $code ? 'selected' : '' }}>

                                    {{ $label }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-3 mb-3">

                        <label class="form-label fw-bold">
                            Mevzuat Profili
                        </label>

                        <select name="regulation_profile"
                                class="form-select">

                            <option value="">
                                Seçiniz
                            </option>

                            <option value="EU_REACH_CLP"
                                {{ old('regulation_profile', $profile->regulation_profile) == 'EU_REACH_CLP' ? 'selected' : '' }}>

                                EU REACH / CLP

                            </option>

                            <option value="TR_KKDIK_SEA"
                                {{ old('regulation_profile', $profile->regulation_profile) == 'TR_KKDIK_SEA' ? 'selected' : '' }}>

                                TR KKDİK / SEA

                            </option>

                        </select>

                    </div>


                    <div class="col-md-3 mb-3">

                        <label class="form-label fw-bold">
                            SDS Tarihi
                        </label>

                        <input type="date"
                               name="supplier_sds_date"
                               class="form-control"
                               value="{{ old(
                                   'supplier_sds_date',
                                   $profile->supplier_sds_date?->format('Y-m-d')
                               ) }}">

                    </div>


                    <div class="col-md-3 mb-3">

                        <label class="form-label fw-bold">
                            Revizyon
                        </label>

                        <input type="text"
                               name="supplier_sds_revision"
                               class="form-control"
                               value="{{ old(
                                   'supplier_sds_revision',
                                   $profile->supplier_sds_revision
                               ) }}">

                    </div>


                    <div class="col-md-3 mb-3">

                        <label class="form-label fw-bold">
                            Versiyon
                        </label>

                        <input type="text"
                               name="supplier_sds_version"
                               class="form-control"
                               value="{{ old(
                                   'supplier_sds_version',
                                   $profile->supplier_sds_version
                               ) }}">

                    </div>


                    <div class="col-md-5 mb-3">

                        <label class="form-label fw-bold">
                            Yeni SDS Dosyası
                        </label>

                        <input type="file"
                               name="supplier_sds_file"
                               class="form-control"
                               accept=".pdf,.doc,.docx">

                        @if($profile->supplier_sds_file)

                            <small class="text-muted">
                                Yeni dosya seçmezsen mevcut dosya korunur.
                            </small>

                        @endif

                    </div>


                    <div class="col-md-4 mb-3">

                        <label class="form-label fw-bold">
                            Madde / Karışım
                        </label>

                        <select name="substance_or_mixture"
                                class="form-select">

                            <option value="">
                                Seçiniz
                            </option>

                            <option value="substance"
                                {{ old(
                                    'substance_or_mixture',
                                    $profile->substance_or_mixture
                                ) == 'substance' ? 'selected' : '' }}>

                                Madde

                            </option>

                            <option value="mixture"
                                {{ old(
                                    'substance_or_mixture',
                                    $profile->substance_or_mixture
                                ) == 'mixture' ? 'selected' : '' }}>

                                Karışım

                            </option>

                        </select>

                    </div>

                </div>

            </div>

        </div>


        <div class="card mb-3">

            <div class="card-header">

                <strong>
                    2. Ana Madde / Kimlik Bilgileri
                </strong>

            </div>

            <div class="card-body">

                <div class="alert alert-info">

                    Tek madde SDS'lerinde ana kimlik bilgilerini burada tutuyoruz.
                    Karışım bileşenleri SDS Detay ekranındaki
                    <strong>Bölüm 3</strong> alanından yönetilir.

                </div>

                <div class="row">

                    <div class="col-md-3 mb-3">

                        <label class="form-label fw-bold">
                            CAS No
                        </label>

                        <input type="text"
                               name="cas_no"
                               class="form-control"
                               value="{{ old('cas_no', $profile->cas_no) }}">

                    </div>


                    <div class="col-md-3 mb-3">

                        <label class="form-label fw-bold">
                            EC No
                        </label>

                        <input type="text"
                               name="ec_no"
                               class="form-control"
                               value="{{ old('ec_no', $profile->ec_no) }}">

                    </div>


                    <div class="col-md-3 mb-3">

                        <label class="form-label fw-bold">
                            Index No
                        </label>

                        <input type="text"
                               name="index_no"
                               class="form-control"
                               value="{{ old('index_no', $profile->index_no) }}">

                    </div>


                    <div class="col-md-3 mb-3">

                        <label class="form-label fw-bold">
                            REACH Kayıt No
                        </label>

                        <input type="text"
                               name="reach_registration_no"
                               class="form-control"
                               value="{{ old(
                                   'reach_registration_no',
                                   $profile->reach_registration_no
                               ) }}">

                    </div>


                    <div class="col-md-4 mb-3">

                        <label class="form-label fw-bold">
                            KKDİK Kayıt No
                        </label>

                        <input type="text"
                               name="kkdik_registration_no"
                               class="form-control"
                               value="{{ old(
                                   'kkdik_registration_no',
                                   $profile->kkdik_registration_no
                               ) }}">

                    </div>

                </div>

            </div>

        </div>


        <div class="card mb-3">

            <div class="card-header">

                <strong>
                    3. Kaynak Bilgileri
                </strong>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">
                            Kaynak Adı
                        </label>

                        <input type="text"
                               name="source_name"
                               class="form-control"
                               value="{{ old(
                                   'source_name',
                                   $profile->source_name
                               ) }}">

                    </div>


                    <div class="col-md-3 mb-3">

                        <label class="form-label fw-bold">
                            Kaynak Tarihi
                        </label>

                        <input type="date"
                               name="source_date"
                               class="form-control"
                               value="{{ old(
                                   'source_date',
                                   $profile->source_date?->format('Y-m-d')
                               ) }}">

                    </div>

                </div>

            </div>

        </div>


        <div class="card">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <a href="{{ route('sds.raw-materials.show', [
                        $rawMaterial->id,
                        $profile->id
                    ]) }}"
                       class="btn btn-outline-secondary">

                        İptal

                    </a>

                    <button type="submit"
                            class="btn btn-success">

                        💾 Değişiklikleri Kaydet

                    </button>

                </div>

            </div>

        </div>

    </form>

</div>

@endsection