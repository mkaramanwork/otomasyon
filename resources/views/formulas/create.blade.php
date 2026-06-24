@extends('layouts.admin')
@section('title', 'Yeni Formülasyon Ekle')

@section('content')
<div class="row mb-3">
    <div class="col-md-6"><h4>Yeni Formülasyon Tanımı</h4></div>
    <div class="col-md-6 text-right">
        <a href="{{ route('formulas.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form id="formula-form" action="{{ route('formulas.store') }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-body">
            <input type="hidden" name="product_id" value="{{ $productId }}">

            <div class="form-group">
                <label>Formülasyon Adı</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Tip</label>
                <select name="type" class="form-control" required>
                    <option value="OEM">OEM</option>
                    <option value="orta_segment">Orta Segment</option>
                    <option value="meet_requirement">Meet Requirement</option>
                    <option value="eco">Eco</option>
                    <option value="karışım">Karışım</option>
                    <option value="sıvı">Sıvı</option>
                    <option value="toz">Toz</option>
                </select>
            </div>

            <div class="form-group">
                <label>Açıklama</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>

            <hr>
            <h5>Ham Maddeler</h5>
            <div id="material-container">
                <div class="row material-row mb-2">
                    <div class="col-md-6">
                        <select name="materials[0][raw_material_id]" class="form-control" required>
                            <option value="">Ham madde seç</option>
                            @foreach($materials as $mat)
                                <option value="{{ $mat->id }}">{{ $mat->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted price-info"></small>
                    </div>
                    <div class="col-md-4">
                        <input type="number" step="0.01" name="materials[0][percentage]" class="form-control" placeholder="Oran (%)" required>
                    </div>
                    <div class="col-md-2 text-right">
                        <button type="button" class="btn btn-danger btn-sm remove-material">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <button type="button" id="add-material" class="btn btn-sm btn-info mt-2">
                <i class="fas fa-plus"></i> Ham Madde Ekle
            </button>

            <hr>
            <h5>Ek Maliyet Alanları</h5>
            <div class="form-group">
                <label>Ambalaj</label>
                <input type="number" step="0.01" name="packaging_cost" class="form-control" value="0">
            </div>
            <div class="form-group">
                <label>İşçilik</label>
                <input type="number" step="0.01" name="labor_cost" class="form-control" value="0">
            </div>
            <div class="form-group">
                <label>Sevkiyat</label>
                <input type="number" step="0.01" name="shipping_cost" class="form-control" value="0">
            </div>
            <div class="form-group">
                <label>Vergi</label>
                <input type="number" step="0.01" name="tax" class="form-control" value="1">
            </div>
            <div class="form-group">
                <label>Kar Marjı (%)</label>
                <input type="number" step="0.01" name="profit_margin" class="form-control" value="1">
            </div>
            <div class="form-group">
                <label>KDV (%)</label>
                <input type="number" step="0.01" name="vat" class="form-control" value="20">
            </div>

        </div>

        <div class="card-footer text-right">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Kaydet
            </button>
        </div>
    </div>
</form>
@endsection

@push('javascript')
<script>
    let index = 1;
    const rawMaterials = @json($materials);

    document.getElementById('add-material').addEventListener('click', function () {
        const container = document.getElementById('material-container');

        let options = '<option value="">Ham madde seç</option>';
        rawMaterials.forEach(mat => {
            options += `<option value="${mat.id}">${mat.name}</option>`;
        });

        const row = document.createElement('div');
        row.classList.add('row', 'material-row', 'mb-2');
        row.innerHTML = `
            <div class="col-md-6">
                <select name="materials[${index}][raw_material_id]" class="form-control" required>
                    ${options}
                </select>
                <small class="text-muted price-info"></small>
            </div>
            <div class="col-md-4">
                <input type="number" step="0.01" name="materials[${index}][percentage]" class="form-control" placeholder="Oran (%)" required>
            </div>
            <div class="col-md-2 text-right">
                <button type="button" class="btn btn-danger btn-sm remove-material">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
        index++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.closest('.remove-material')) {
            e.target.closest('.material-row').remove();
        }
    });

    // fiyat bilgilerini göster
    document.addEventListener('change', function(e) {
        if (e.target.name.includes('[raw_material_id]')) {
            const selectedId = e.target.value;
            const mat = rawMaterials.find(m => m.id == selectedId);
            if (mat) {
                const priceInfo = `TL: ${mat.unit_price_tl}, USD: ${mat.unit_price_usd}, EUR: ${mat.unit_price_eur}`;
                e.target.closest('.material-row').querySelector('.price-info').innerText = priceInfo;
            }
        }
    });

    // yüzde kontrolü
    document.getElementById('formula-form').addEventListener('submit', function (e) {
        const percentages = document.querySelectorAll('input[name^="materials"][name$="[percentage]"]');
        let total = 0;

        percentages.forEach(input => {
            const val = parseFloat(input.value);
            if (!isNaN(val)) total += val;
        });

        total = Math.round(total * 100) / 100;

        if (total !== 100) {
            e.preventDefault();
            alert("Ham madde oranlarının toplamı %100 olmalıdır.");
        }
    });
</script>
@endpush