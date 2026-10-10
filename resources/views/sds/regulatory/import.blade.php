@extends('layouts.admin')

@section('title','Mevzuat Güncelleme')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>

            <h4 class="mb-0">
                🔄 Mevzuat Güncelleme
            </h4>

            <small class="text-muted">
                Resmî mevzuat verilerinin Excel / CSV üzerinden güncellenmesi
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


    <div class="card">

        <div class="card-header bg-dark text-white">

            <strong>
                Mevzuat Veri Dosyası
            </strong>

        </div>

        <div class="card-body">

            <div class="alert alert-info">

                Bu ekran üzerinden ECHA, REACH / CLP veya
                KKDİK / SEA kaynaklarından hazırlanan veri dosyaları
                sisteme alınacaktır.

                Veri doğrudan mevcut kayıtların üzerine yazılmayacaktır.

            </div>


            <form method="POST"
                  action="{{ route('sds.regulatory.import.preview') }}"
                  enctype="multipart/form-data">

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


                    <div class="col-md-4 mb-3">

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


                    <div class="col-md-2 mb-3">

                        <label class="form-label fw-bold">
                            Kaynak Versiyonu
                        </label>

                        <input type="text"
                               name="source_version"
                               class="form-control"
                               value="{{ old('source_version') }}"
                               placeholder="Örn: ATP 23">

                    </div>


                    <div class="col-md-2 mb-3">

                        <label class="form-label fw-bold">
                            Kaynak Tarihi
                        </label>

                        <input type="date"
                               name="source_date"
                               class="form-control"
                               value="{{ old('source_date') }}">

                    </div>


                    <div class="col-md-12 mb-3">

                        <label class="form-label fw-bold">
                            Veri Dosyası *
                        </label>

                        <input type="file"
                               name="import_file"
                               class="form-control"
                               accept=".csv,.txt,.xlsx,.xls"
                               required>

                        <small class="text-muted">

                            Desteklenecek formatlar:
                            CSV, TXT, XLSX, XLS

                        </small>

                    </div>

                </div>


                <div class="text-end">

                    <button type="submit"
                            class="btn btn-primary">

                        🔍 Dosyayı Kontrol Et

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection