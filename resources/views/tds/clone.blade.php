@extends('layouts.admin')

@section('title','TDS Klonla')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">📄 TDS Klonla</h4>
            <small class="text-muted">Mevcut TDS verisini yeni ürün ve marka için kopyala</small>
        </div>

        <a href="{{ route('tds.index') }}" class="btn btn-secondary">
            ⬅️ TDS Listesi
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Klonlama yapılamadı.</strong>

            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('tds.cloneStore') }}" method="POST">

        @csrf

        <input type="hidden" name="source_tds_id" id="source_tds_id">

        <div class="card mb-3">

            <div class="card-header">
                <strong>📌 Kaynak TDS</strong>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-12">

                        <label class="form-label">Klonlanacak TDS</label>

                        <select id="source_tds"
                                class="form-select"
                                required>

                            <option value="">Kaynak TDS Seçiniz</option>

                            @foreach($documents as $doc)

                                <option value="{{ $doc->id }}">
                                    {{ $doc->brand?->name ?? 'Markasız' }}
                                    -
                                    {{ $doc->product_name }}
                                    @if($doc->product_code)
                                        ({{ $doc->product_code }})
                                    @endif
                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

            </div>

        </div>


        <div id="cloneContent" style="display:none;">

            <div class="card mb-3">

                <div class="card-header">
                    <strong>📦 Hedef Ürün Bilgileri</strong>
                </div>

                <div class="card-body">

                    <div class="row g-3">

                        <div class="col-md-3">

                            <label class="form-label">Hedef Marka</label>

                            <select name="brand_id"
                                    id="brand_id"
                                    class="form-select"
                                    required>

                                <option value="">Marka Seçiniz</option>

                                @foreach($brands as $brand)

                                    <option value="{{ $brand->id }}">
                                        {{ $brand->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <div class="col-md-3">

                            <label class="form-label">Hedef Ürün</label>

                            <select name="product_id"
                                    id="product_id"
                                    class="form-select"
                                    required>

                                <option value="">Ürün Seçiniz</option>

                                @foreach($products as $product)

                                    <option value="{{ $product->id }}"
                                            data-name="{{ $product->name }}"
                                            data-code="{{ $product->code }}">

                                        {{ $product->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <div class="col-md-3">

                            <label class="form-label">Ürün Grubu</label>

                            <select name="product_group"
                                    id="product_group"
                                    class="form-select"
                                    required>

                                <option value="">Ürün Grubu Seçiniz</option>

                                <option value="Binek Araç">Binek Araç</option>
                                <option value="Ağır Hizmet">Ağır Hizmet</option>
                                <option value="Endüstriyel">Endüstriyel</option>
                                <option value="Antifriz">Antifriz</option>
                                <option value="Gres">Gres</option>
                                <option value="AdBlue">AdBlue</option>

                            </select>

                        </div>


                        <div class="col-md-3">

                            <label class="form-label">Ürün Kodu</label>

                            <input type="text"
                                   name="product_code"
                                   id="product_code"
                                   class="form-control">

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">TDS Ürün Adı</label>

                            <input type="text"
                                   name="product_name"
                                   id="product_name"
                                   class="form-control"
                                   required>

                        </div>

                    </div>

                </div>

            </div>


            <div class="card mb-3">

                <div class="card-header">
                    <strong>📝 TDS İçeriği</strong>
                </div>

                <div class="card-body">

                    <div class="row g-3">

                        <div class="col-md-12">

                            <label class="form-label">Açıklama</label>

                            <textarea name="description"
                                      id="description"
                                      class="form-control"
                                      rows="4"></textarea>

                        </div>


                        <div class="col-md-12">

                            <label class="form-label">Onaylar / Performans Seviyeleri</label>

                            <textarea name="approvals"
                                      id="approvals"
                                      class="form-control"
                                      rows="3"></textarea>

                        </div>

                    </div>

                </div>

            </div>


            <div class="card mb-3">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <strong>⭐ Özellikler</strong>

                    <button type="button"
                            id="addFeature"
                            class="btn btn-secondary btn-sm">

                        ➕ Özellik Ekle

                    </button>

                </div>

                <div class="card-body">

                    <div id="featureContainer"></div>

                </div>

            </div>


            <div class="card mb-3">

                <div class="card-header">
                    <strong>🧪 Teknik Parametreler</strong>
                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover">

                            <thead class="table-light">

                                <tr>
                                    <th style="width:70px;">Seç</th>
                                    <th>Parametre</th>
                                    <th>Birim</th>
                                    <th>Metot</th>
                                    <th style="width:220px;">Değer</th>
                                </tr>

                            </thead>

                            <tbody>

                                @foreach($params as $p)

                                    <tr>

                                        <td class="text-center">

                                            <input type="checkbox"
                                                   class="form-check-input param-check"
                                                   name="param_select[{{ $p->id }}]"
                                                   id="param_select_{{ $p->id }}"
                                                   value="1">

                                        </td>

                                        <td>{{ $p->parameter }}</td>

                                        <td>{{ $p->unit }}</td>

                                        <td>{{ $p->method_tr }}</td>

                                        <td>

                                            <input type="text"
                                                   name="param_value[{{ $p->id }}]"
                                                   id="param_value_{{ $p->id }}"
                                                   class="form-control">

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
                        class="btn btn-success">

                    📄 Klonla ve Kaydet

                </button>

            </div>

        </div>

    </form>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const sourceSelect = document.getElementById('source_tds');
    const sourceTdsId = document.getElementById('source_tds_id');

    const cloneContent = document.getElementById('cloneContent');

    const brandSelect = document.getElementById('brand_id');
    const productSelect = document.getElementById('product_id');

    const productGroup = document.getElementById('product_group');
    const productCode = document.getElementById('product_code');
    const productName = document.getElementById('product_name');

    const description = document.getElementById('description');
    const approvals = document.getElementById('approvals');

    const featureContainer = document.getElementById('featureContainer');

    let featureIndex = 0;


    sourceSelect.addEventListener('change', function () {

        const id = this.value;

        if (!id) {

            cloneContent.style.display = 'none';
            sourceTdsId.value = '';

            return;
        }


        fetch(`{{ url('/tds/clone/data') }}/${id}`)

            .then(response => {

                if (!response.ok) {
                    throw new Error('TDS verisi alınamadı.');
                }

                return response.json();

            })

            .then(data => {

                sourceTdsId.value = data.id;

                brandSelect.value = data.brand_id || '';

                productSelect.value = data.product_id || '';

                productGroup.value = data.product_group || '';

                productCode.value = data.product_code || '';

                productName.value = data.product_name || '';

                description.value = data.description || '';

                approvals.value = data.approvals || '';


                featureContainer.innerHTML = '';

                featureIndex = 0;


                if (data.features && data.features.length > 0) {

                    data.features.forEach(function (feature) {

                        addFeatureRow(
                            feature.title || '',
                            feature.value || ''
                        );

                    });

                } else {

                    addFeatureRow('', '');

                }


                document
                    .querySelectorAll('.param-check')
                    .forEach(function (checkbox) {

                        checkbox.checked = false;

                    });


                document
                    .querySelectorAll('[id^="param_value_"]')
                    .forEach(function (input) {

                        input.value = '';

                    });


                if (data.parameters && data.parameters.length > 0) {

                    data.parameters.forEach(function (param) {

                        const check =
                            document.getElementById(
                                `param_select_${param.id}`
                            );

                        const value =
                            document.getElementById(
                                `param_value_${param.id}`
                            );


                        if (check) {
                            check.checked = true;
                        }

                        if (value) {
                            value.value = param.value || '';
                        }

                    });

                }


                cloneContent.style.display = 'block';

            })

            .catch(error => {

                alert(error.message);

                cloneContent.style.display = 'none';

            });

    });



    productSelect.addEventListener('change', function () {

        const selected =
            this.options[this.selectedIndex];

        if (!selected.value) {
            return;
        }

        productName.value =
            selected.dataset.name || '';

        productCode.value =
            selected.dataset.code || '';

    });



    function addFeatureRow(title = '', value = '') {

        const html = `

            <div class="row mb-2 feature-row">

                <div class="col-md-5">

                    <input type="text"
                           name="features[${featureIndex}][title]"
                           class="form-control"
                           value="${escapeHtml(title)}"
                           placeholder="Özellik Adı">

                </div>


                <div class="col-md-5">

                    <input type="text"
                           name="features[${featureIndex}][value]"
                           class="form-control"
                           value="${escapeHtml(value)}"
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

        featureContainer.insertAdjacentHTML(
            'beforeend',
            html
        );

        featureIndex++;

    }



    document
        .getElementById('addFeature')
        .addEventListener('click', function () {

            addFeatureRow('', '');

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



    function escapeHtml(value) {

        const div =
            document.createElement('div');

        div.textContent =
            value ?? '';

        return div.innerHTML;

    }

});

</script>

@endsection