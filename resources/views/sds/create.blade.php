@extends('layouts.admin')

@section('title','Hammadde SDS Oluştur')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>
            <h4 class="mb-0">
                🧪 Hammadde SDS Oluştur
            </h4>

            <small class="text-muted">
                Mevcut hammadde için tedarikçi SDS ana kaydı oluştur
            </small>
        </div>

        <div>
            <a href="{{ route('sds.raw-materials.index') }}"
               class="btn btn-outline-secondary">
                ← Listeye Dön
            </a>
        </div>

    </div>


    @if($errors->any())

        <div class="alert alert-danger alert-dismissible fade show">

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

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    <div class="card mb-3">

        <div class="card-header bg-dark text-white">
            <strong>
                Hammadde Bilgisi
            </strong>
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-2 mb-3">

                    <label class="form-label fw-bold">
                        Hammadde ID
                    </label>

                    <input type="text"
                           class="form-control"
                           value="{{ $rawMaterial->id }}"
                           disabled>

                </div>


                <div class="col-md-7 mb-3">

                    <label class="form-label fw-bold">
                        Hammadde Adı
                    </label>

                    <input type="text"
                           class="form-control"
                           value="{{ $rawMaterial->name }}"
                           disabled>

                </div>


                <div class="col-md-3 mb-3">

                    <label class="form-label fw-bold">
                        Birim
                    </label>

                    <input type="text"
                           class="form-control"
                           value="{{ $rawMaterial->unit ?? '-' }}"
                           disabled>

                </div>

            </div>


            @if($rawMaterial->currentSdsProfile)

                <div class="alert alert-warning mb-0">

                    Bu hammaddenin mevcut bir SDS kaydı bulunmaktadır.

                    <strong>
                        {{
                            $rawMaterial->currentSdsProfile->supplier_sds_revision
                            ?? $rawMaterial->currentSdsProfile->supplier_sds_version
                            ?? 'Revizyon bilgisi yok'
                        }}
                    </strong>

                    <div class="small mt-1">
                        Yeni kayıt oluşturulursa mevcut kayıt geçmişte tutulacak
                        ve yeni kayıt güncel SDS olarak işaretlenecektir.
                    </div>

                </div>

            @endif

        </div>

    </div>


    <form method="POST"
          action="{{ route('sds.raw-materials.store', $rawMaterial->id) }}"
          enctype="multipart/form-data">

        @csrf


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
                               value="{{ old('supplier_name') }}"
                               placeholder="Tedarikçi firma adı">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">
                            Tedarikçi Ürün Adı
                        </label>

                        <input type="text"
                               name="supplier_product_name"
                               class="form-control"
                               value="{{ old('supplier_product_name', $rawMaterial->name) }}"
                               placeholder="SDS üzerinde yazan ürün adı">

                        <small class="text-muted">
                            Hammadde adı otomatik getirildi.
                            Tedarikçi SDS'de farklıysa değiştirebilirsiniz.
                        </small>

                    </div>


                    <div class="col-md-3 mb-3">

                        <label class="form-label fw-bold">
                            SDS Dili
                        </label>

                        <select name="sds_language"
                                class="form-select">

                            <option value="en"
                                {{ old('sds_language', 'en') == 'en' ? 'selected' : '' }}>
                                İngilizce
                            </option>

                            <option value="tr"
                                {{ old('sds_language') == 'tr' ? 'selected' : '' }}>
                                Türkçe
                            </option>

                            <option value="de"
                                {{ old('sds_language') == 'de' ? 'selected' : '' }}>
                                Almanca
                            </option>

                            <option value="fr"
                                {{ old('sds_language') == 'fr' ? 'selected' : '' }}>
                                Fransızca
                            </option>

                            <option value="es"
                                {{ old('sds_language') == 'es' ? 'selected' : '' }}>
                                İspanyolca
                            </option>

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
                                {{ old('regulation_profile') == 'EU_REACH_CLP' ? 'selected' : '' }}>
                                EU REACH / CLP
                            </option>

                            <option value="TR_KKDIK_SEA"
                                {{ old('regulation_profile') == 'TR_KKDIK_SEA' ? 'selected' : '' }}>
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
                               value="{{ old('supplier_sds_date') }}">

                    </div>


                    <div class="col-md-3 mb-3">

                        <label class="form-label fw-bold">
                            Revizyon
                        </label>

                        <input type="text"
                               name="supplier_sds_revision"
                               class="form-control"
                               value="{{ old('supplier_sds_revision') }}"
                               placeholder="Örn: 6">

                    </div>


                    <div class="col-md-3 mb-3">

                        <label class="form-label fw-bold">
                            Versiyon
                        </label>

                        <input type="text"
                               name="supplier_sds_version"
                               class="form-control"
                               value="{{ old('supplier_sds_version') }}"
                               placeholder="Örn: V6">

                    </div>


                    <div class="col-md-5 mb-3">

                        <label class="form-label fw-bold">
                            SDS Dosyası
                        </label>

                        <input type="file"
                               name="supplier_sds_file"
                               class="form-control"
                               accept=".pdf,.doc,.docx">

                        <small class="text-muted">
                            PDF veya Word SDS yüklenebilir.
                        </small>

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
                                {{ old('substance_or_mixture') == 'substance' ? 'selected' : '' }}>
                                Madde
                            </option>

                            <option value="mixture"
                                {{ old('substance_or_mixture') == 'mixture' ? 'selected' : '' }}>
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
                    2. Kaynak Bilgileri
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
                               value="{{ old('source_name', 'Supplier SDS') }}"
                               placeholder="Örn: Supplier SDS">

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

                </div>

            </div>

        </div>


        <div class="card">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <a href="{{ route('sds.raw-materials.index') }}"
                       class="btn btn-outline-secondary">
                        İptal
                    </a>

                    <button type="submit"
                            class="btn btn-success">
                        💾 SDS Ana Kartını Kaydet
                    </button>

                </div>

            </div>

        </div>

    </form>

</div>

@endsection