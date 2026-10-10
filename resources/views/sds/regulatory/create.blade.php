@extends('layouts.admin')

@section('title','Mevzuat Kimyasalı Ekle')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>

            <h4 class="mb-0">
                ➕ Mevzuat Kimyasalı Ekle
            </h4>

            <small class="text-muted">
                CAS / EC / Index bazlı kimyasal madde ana kaydı
            </small>

        </div>

        <div>

            <a href="{{ route('sds.regulatory.index') }}"
               class="btn btn-outline-secondary">

                ← Mevzuat Veritabanı

            </a>

        </div>

    </div>


    @if($errors->any())

        <div class="alert alert-danger">

            <strong>
                Kayıt oluşturulamadı.
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


    <div class="alert alert-warning">

        <strong>Not:</strong>

        Bu ekrandan girilen kayıt henüz resmî ECHA / KKDİK doğrulaması
        yapılmış kabul edilmez.

        Kayıt ilk olarak
        <strong>Kontrol Gerekli</strong>
        durumunda oluşturulur.

    </div>


    <form method="POST"
          action="{{ route('sds.regulatory.store') }}">

        @csrf


        <div class="card mb-3">

            <div class="card-header bg-dark text-white">

                <strong>
                    Kimyasal Kimliği
                </strong>

            </div>


            <div class="card-body">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">
                            Kimyasal Madde Adı *
                        </label>

                        <input type="text"
                               name="preferred_name"
                               class="form-control @error('preferred_name') is-invalid @enderror"
                               value="{{ old('preferred_name') }}"
                               required>

                        @error('preferred_name')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    <div class="col-md-2 mb-3">

                        <label class="form-label fw-bold">
                            CAS No *
                        </label>

                        <input type="text"
                               name="cas_no"
                               class="form-control @error('cas_no') is-invalid @enderror"
                               value="{{ old('cas_no') }}"
                               placeholder="Örn: 64742-54-7"
                               required>

                        @error('cas_no')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    <div class="col-md-2 mb-3">

                        <label class="form-label fw-bold">
                            EC No
                        </label>

                        <input type="text"
                               name="ec_no"
                               class="form-control @error('ec_no') is-invalid @enderror"
                               value="{{ old('ec_no') }}">

                        @error('ec_no')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    <div class="col-md-2 mb-3">

                        <label class="form-label fw-bold">
                            Index No
                        </label>

                        <input type="text"
                               name="index_no"
                               class="form-control @error('index_no') is-invalid @enderror"
                               value="{{ old('index_no') }}">

                        @error('index_no')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    <div class="col-md-4 mb-3">

                        <label class="form-label fw-bold">
                            Moleküler Formül
                        </label>

                        <input type="text"
                               name="molecular_formula"
                               class="form-control"
                               value="{{ old('molecular_formula') }}">

                    </div>


                    <div class="col-md-4 mb-3">

                        <label class="form-label fw-bold">
                            Madde Türü
                        </label>

                        <input type="text"
                               name="substance_type"
                               class="form-control"
                               value="{{ old('substance_type') }}"
                               placeholder="Örn: Substance / UVCB">

                    </div>

                </div>

            </div>

        </div>


        <div class="card mb-3">

            <div class="card-header">

                <strong>
                    ECHA Referansı
                </strong>

            </div>


            <div class="card-body">

                <div class="alert alert-info">

                    Bu alanlar şimdilik isteğe bağlıdır.

                    Daha sonra Mevzuat Güncelleme sistemi üzerinden
                    ECHA verileri otomatik veya toplu olarak güncellenecek.

                </div>


                <div class="row">

                    <div class="col-md-4 mb-3">

                        <label class="form-label fw-bold">
                            ECHA UUID
                        </label>

                        <input type="text"
                               name="echa_uuid"
                               class="form-control"
                               value="{{ old('echa_uuid') }}">

                    </div>


                    <div class="col-md-8 mb-3">

                        <label class="form-label fw-bold">
                            ECHA Madde URL
                        </label>

                        <input type="text"
                               name="echa_substance_url"
                               class="form-control"
                               value="{{ old('echa_substance_url') }}">

                    </div>

                </div>

            </div>

        </div>


        <div class="card">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <a href="{{ route('sds.regulatory.index') }}"
                       class="btn btn-outline-secondary">

                        İptal

                    </a>

                    <button type="submit"
                            class="btn btn-success">

                        💾 Kimyasalı Kaydet

                    </button>

                </div>

            </div>

        </div>

    </form>

</div>

@endsection