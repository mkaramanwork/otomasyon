@extends('layouts.admin')
@section('title', 'Formülasyon Düzenle')

@section('content')
<div class="row mb-3">
    <div class="col-md-6"><h4>Formülasyon Düzenle</h4></div>
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

<form id="formula-form" action="{{ route('formulas.update', $formula->id) }}" method="POST">
    @csrf @method('PUT')
    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <label>Formülasyon Adı</label>
                <input type="text" name="name" class="form-control" value="{{ $formula->name }}" required>
            </div>

            <div class="form-group">
                <label>Tip</label>
                <select name="type" class="form-control" required>
                    <option value="karışım" {{ $formula->type == 'karışım' ? 'selected' : '' }}>Karışım</option>
                    <option value="sıvı" {{ $formula->type == 'sıvı' ? 'selected' : '' }}>Sıvı</option>
                    <option value="toz" {{ $formula->type == 'toz' ? 'selected' : '' }}>Toz</option>
                    <option value="OEM" {{ $formula->type == 'OEM' ? 'selected' : '' }}>OEM</option>
                    <option value="orta_segment" {{ $formula->type == 'orta_segment' ? 'selected' : '' }}>Orta Segment</option>
                    <option value="meet_requirement" {{ $formula->type == 'meet_requirement' ? 'selected' : '' }}>Meet Requirement</option>
                    <option value="eco" {{ $formula->type == 'eco' ? 'selected' : '' }}>Eco</option>
                </select>
            </div>

            <div class="form-group">
                <label>Açıklama</label>
                <textarea name="description" class="form-control" rows="3">{{ $formula->description }}</textarea>
            </div>

            <hr>
            <h5>Ham Madde Oranları</h5>
            <div id="material-container">
                @foreach($formula->details as $i => $detail)
                <div class="row material-row mb-2">
                    <div class="col-md-5">
                        <select name="materials[{{ $i }}][raw_material_id]" class="form-control" required>
                            <option value="">Ham madde seç</option>
                            @foreach($materials as $mat)
                                <option value="{{ $mat->id }}" {{ $detail->raw_material_id == $mat->id ? 'selected' : '' }}>
                                    {{ $mat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="number" step="0.01" name="materials[{{ $i }}][percentage]" class="form-control"
                            value="{{ $detail->percentage }}" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="materials[{{ $i }}][unit]" class="form-control"
                            value="{{ $detail->unit }}">
                    </div>
                    <div class="col-md-1 text-right">
                        <button type="button" class="btn btn-danger btn-sm remove-material">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            <button type="button" id="add-material" class="btn btn-sm btn-info mt-2">
                <i class="fas fa-plus"></i> Ham Madde Ekle
            </button>
        </div>

        <div class="card-footer text-right">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Güncelle
            </button>
        </div>
    </div>
</form>
@endsection

@push('javascript')
<script>
    let index = {{ count($formula->details) }};
    const rawMaterials = @json($materials);

    document.getElementById('add-material').addEventListener('click', function () {
        const container = document.getElementById('material-container');
        const row = document.createElement('div');
        row.classList.add('row', 'material-row', 'mb-2');

        let options = '<option value="">Ham madde seç</option>';
        rawMaterials.forEach(mat => {
            options += `<option value="${mat.id}">${mat.name}</option>`;
        });

        row.innerHTML = `
            <div class="col-md-5">
                <select name="materials[${index}][raw_material_id]" class="form-control" required>
                    ${options}
                </select>
            </div>
            <div class="col-md-3">
                <input type="number" step="0.01" name="materials[${index}][percentage]" class="form-control" required>
            </div>
            <div class="col-md-3">
                <input type="text" name="materials[${index}][unit]" class="form-control">
            </div>
            <div class="col-md-1 text-right">
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
