@extends('layouts.admin')

@section('title','Yeni TDS')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
      rel="stylesheet">

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>

            <h4 class="mb-0">
                📄 Yeni TDS Oluştur
            </h4>

            <small class="text-muted">
                Teknik Bilgi Formu oluşturma ekranı
            </small>

        </div>

        <a href="{{ route('tds.index') }}"
           class="btn btn-secondary">

            ⬅️ TDS Listesi

        </a>

    </div>


    @if($errors->any())

        <div class="alert alert-danger">

            <strong>
                Kayıt yapılamadı.
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


    <form action="{{ route('tds.store') }}"
          method="POST">

        @csrf


        <div class="card mb-3">

            <div class="card-header">

                <strong>
                    📦 Ürün Bilgileri
                </strong>

            </div>


            <div class="card-body">

                <div class="row g-3">


                    <div class="col-md-3">

                        <label class="form-label">
                            Ürün
                        </label>

                        <select name="product_id"
                                id="product_id"
                                class="form-select product-search"
                                required>

                            <option value="">
                                Ürün Seçiniz
                            </option>

                            @foreach($products as $p)

                                <option value="{{ $p->id }}"
                                        data-name="{{ $p->name }}"
                                        data-code="{{ $p->code }}"
                                        @selected(old('product_id') == $p->id)>

                                    {{ $p->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-3">

                        <label class="form-label">
                            Marka
                        </label>

                        <select name="brand_id"
                                id="brand_id"
                                class="form-select"
                                required>

                            <option value="">
                                Marka Seçiniz
                            </option>

                            @foreach($brands as $brand)

                                <option value="{{ $brand->id }}"
                                        @selected(old('brand_id') == $brand->id)>

                                    {{ $brand->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-3">

                        <label class="form-label">
                            Ürün Grubu
                        </label>

                        <select name="product_group"
                                class="form-select"
                                required>

                            <option value="">
                                Ürün Grubu Seçiniz
                            </option>

                            <option value="Binek Araç"
                                    @selected(old('product_group') == 'Binek Araç')>

                                Binek Araç

                            </option>

                            <option value="Ağır Hizmet"
                                    @selected(old('product_group') == 'Ağır Hizmet')>

                                Ağır Hizmet

                            </option>

                            <option value="Endüstriyel"
                                    @selected(old('product_group') == 'Endüstriyel')>

                                Endüstriyel

                            </option>

                            <option value="Antifriz"
                                    @selected(old('product_group') == 'Antifriz')>

                                Antifriz

                            </option>

                            <option value="Gres"
                                    @selected(old('product_group') == 'Gres')>

                                Gres

                            </option>

                            <option value="AdBlue"
                                    @selected(old('product_group') == 'AdBlue')>

                                AdBlue

                            </option>

                        </select>

                    </div>


                    <div class="col-md-3">

                        <label class="form-label">
                            Ürün Kodu
                        </label>

                        <input type="text"
                               name="product_code"
                               id="product_code"
                               class="form-control"
                               value="{{ old('product_code') }}">

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            TDS Ürün Adı
                        </label>

                        <input type="text"
                               name="product_name"
                               id="product_name"
                               class="form-control"
                               value="{{ old('product_name') }}"
                               placeholder="TDS üzerinde görünecek ürün adı">

                        <small class="text-muted">

                            Ürün seçiminden otomatik gelir, istersen değiştirebilirsin.

                        </small>

                    </div>


                </div>

            </div>

        </div>


        <div class="card mb-3">

            <div class="card-header">

                <strong>
                    📝 TDS İçeriği
                </strong>

            </div>


            <div class="card-body">

                <div class="row g-3">


                    <div class="col-md-12">

                        <label class="form-label">
                            Açıklama
                        </label>

                        <textarea name="description"
                                  class="form-control"
                                  rows="4">{{ old('description') }}</textarea>

                    </div>


                    <div class="col-md-12">

                        <label class="form-label">
                            Onaylar / Performans Seviyeleri
                        </label>

                        <textarea name="approvals"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Örn: API SN, ACEA A3/B4, OEM Approval">{{ old('approvals') }}</textarea>

                    </div>


                </div>

            </div>

        </div>


        <div class="card mb-3">

            <div class="card-header d-flex justify-content-between align-items-center">

                <strong>
                    ⭐ Özellikler
                </strong>

                <button type="button"
                        id="addFeature"
                        class="btn btn-secondary btn-sm">

                    ➕ Özellik Ekle

                </button>

            </div>


            <div class="card-body">

                <div id="featureContainer">

                    @if(old('features'))

                        @foreach(old('features') as $i => $feature)

                            <div class="row mb-2 feature-row">

                                <div class="col-md-5">

                                    <input type="text"
                                           name="features[{{ $i }}][title]"
                                           class="form-control"
                                           value="{{ $feature['title'] ?? '' }}"
                                           placeholder="Özellik Adı">

                                </div>


                                <div class="col-md-5">

                                    <input type="text"
                                           name="features[{{ $i }}][value]"
                                           class="form-control"
                                           value="{{ $feature['value'] ?? '' }}"
                                           placeholder="Değer">

                                </div>


                                <div class="col-md-2">

                                    <button type="button"
                                            class="btn btn-danger removeFeature">

                                        🗑️ Sil

                                    </button>

                                </div>

                            </div>

                        @endforeach

                    @else

                        <div class="row mb-2 feature-row">

                            <div class="col-md-5">

                                <input type="text"
                                       name="features[0][title]"
                                       class="form-control"
                                       placeholder="Özellik Adı">

                            </div>


                            <div class="col-md-5">

                                <input type="text"
                                       name="features[0][value]"
                                       class="form-control"
                                       placeholder="Değer">

                            </div>


                            <div class="col-md-2">

                                <button type="button"
                                        class="btn btn-danger removeFeature">

                                    🗑️ Sil

                                </button>

                            </div>

                        </div>

                    @endif

                </div>

            </div>

        </div>


        <div class="card mb-3">

            <div class="card-header">

                <strong>
                    🧪 Teknik Parametreler
                </strong>

            </div>


            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover">

                        <thead class="table-light">

                            <tr>

                                <th style="width:70px;">
                                    Seç
                                </th>

                                <th>
                                    Parametre
                                </th>

                                <th>
                                    Birim
                                </th>

                                <th>
                                    Metot
                                </th>

                                <th style="width:220px;">
                                    Değer
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($params as $p)

                                <tr>

                                    <td class="text-center">

                                        <input type="checkbox"
                                               class="form-check-input"
                                               name="param_select[{{ $p->id }}]"
                                               value="1"
                                               @checked(old("param_select.$p->id"))>

                                    </td>


                                    <td>

                                        {{ $p->parameter }}

                                    </td>


                                    <td>

                                        {{ $p->unit }}

                                    </td>


                                    <td>

                                        {{ $p->method_en }}

                                    </td>


                                    <td>

                                        <input type="text"
                                               name="param_value[{{ $p->id }}]"
                                               class="form-control"
                                               value="{{ old("param_value.$p->id") }}"
                                               placeholder="Değer">

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        <div class="d-flex justify-content-end gap-2 mb-4">

            <a href="{{ route('tds.index') }}"
               class="btn btn-secondary">

                İptal

            </a>

            <button type="submit"
                    class="btn btn-primary">

                💾 TDS Kaydet

            </button>

        </div>


    </form>

</div>


<style>

.select2-container {
    width: 100% !important;
}

.select2-container .select2-selection--single {
    height: 38px;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
}

.select2-container .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
    padding-left: 12px;
}

.select2-container .select2-selection--single .select2-selection__arrow {
    height: 36px;
}

</style>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script>

document.addEventListener('DOMContentLoaded', function () {

    $('.product-search').select2({
        placeholder: 'Ürün ara veya seç...',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function () {
                return 'Ürün bulunamadı';
            },
            searching: function () {
                return 'Aranıyor...';
            }
        }
    });


    const productSelect =
        document.getElementById('product_id');

    const productName =
        document.getElementById('product_name');

    const productCode =
        document.getElementById('product_code');


    productSelect.addEventListener('change', function () {

        const selectedOption =
            this.options[this.selectedIndex];

        if (!selectedOption.value) {

            return;

        }

        productName.value =
            selectedOption.dataset.name || '';

        productCode.value =
            selectedOption.dataset.code || '';

    });


    let index =
        document.querySelectorAll('.feature-row').length;


    document
        .getElementById('addFeature')
        .addEventListener('click', function () {

            const container =
                document.getElementById('featureContainer');

            const html = `

                <div class="row mb-2 feature-row">

                    <div class="col-md-5">

                        <input type="text"
                               name="features[${index}][title]"
                               class="form-control"
                               placeholder="Özellik Adı">

                    </div>


                    <div class="col-md-5">

                        <input type="text"
                               name="features[${index}][value]"
                               class="form-control"
                               placeholder="Değer">

                    </div>


                    <div class="col-md-2">

                        <button type="button"
                                class="btn btn-danger removeFeature">

                            🗑️ Sil

                        </button>

                    </div>

                </div>

            `;

            container.insertAdjacentHTML(
                'beforeend',
                html
            );

            index++;

        });


    document.addEventListener('click', function (e) {

        if (
            e.target.classList.contains('removeFeature')
        ) {

            e.target
                .closest('.feature-row')
                .remove();

        }

    });

});

</script>

@endsection