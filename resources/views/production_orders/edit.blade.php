@extends('layouts.admin')
@section('title', 'İş Emrini Düzenle')

@section('content')
<div class="container">
    <h4 class="mb-4">İş Emrini Düzenle</h4>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ✅ ÜRETİM EMRİ GÜNCELLEME FORMU --}}
    <form action="{{ route('production_orders.update', $productionOrder->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3">

            {{-- Ürün --}}
            <div class="col-md-6">
                <label class="form-label">Ürün</label>
                <select name="product_id" class="form-select" required>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ $productionOrder->product_id == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Üretim Hattı --}}
            <div class="col-md-6">
                <label class="form-label">Üretim Hattı</label>
                <select name="production_line_id" class="form-select" required>
                    @foreach($lines as $line)
                        <option value="{{ $line->id }}" {{ $productionOrder->production_line_id == $line->id ? 'selected' : '' }}>
                            {{ $line->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Reçete --}}
            <div class="col-md-6">
                <label class="form-label">Reçete</label>
                <select name="formula_id" class="form-select" required>
                    @foreach($formulas as $formula)
                        <option value="{{ $formula->id }}" {{ $productionOrder->formula_id == $formula->id ? 'selected' : '' }}>
                            {{ $formula->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Müşteri --}}
            <div class="col-md-6">
                <label class="form-label">Müşteri</label>
                <select name="customer_id" class="form-select" required>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ $productionOrder->customer_id == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Planlanan Tarih --}}
            <div class="col-md-6">
                <label class="form-label">Planlanan Tarih</label>
                <input type="datetime-local" name="planned_at" class="form-control"
                       value="{{ $productionOrder->planned_at->format('Y-m-d\TH:i') }}" required>
            </div>
            {{-- Plan Bitiş Tarihi --}}
            <div class="col-md-6">
                <label class="form-label">Plan Bitiş Tarihi</label>
                <input type="datetime-local" name="planned_end_at" class="form-control"
                    value="{{ optional($productionOrder->planned_end_at)->format('Y-m-d\TH:i') }}">
            </div>

            {{-- Tonaj --}}
            <div class="col-md-6">
                <label class="form-label">Tonaj (kg)</label>
                <input type="number" name="quantity_kg" class="form-control" step="0.01"
                       value="{{ $productionOrder->quantity_kg }}" required>
            </div>

            {{-- Durum --}}
            <div class="col-md-6">
                <label class="form-label">Durum</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="planned" {{ $productionOrder->status == 'planned' ? 'selected' : '' }}>Planlandı</option>
                    <option value="in_production" {{ $productionOrder->status == 'in_production' ? 'selected' : '' }}>Üretimde</option>
                    <option value="produced" {{ $productionOrder->status == 'produced' ? 'selected' : '' }}>Üretim Tamamlandı</option>
                    <option value="in_filling" {{ $productionOrder->status == 'in_filling' ? 'selected' : '' }}>Doluma Gönderildi</option>
                    <option value="filled" {{ $productionOrder->status == 'filled' ? 'selected' : '' }}>Dolum Tamamlandı</option>
                    <option value="addition" {{ $productionOrder->status == 'addition' ? 'selected' : '' }}>İlave Alındı</option>
                    <option value="cancelled" {{ $productionOrder->status == 'cancelled' ? 'selected' : '' }}>İptal</option>
                </select>
            </div>

            {{-- Açıklama --}}
            <div class="col-12">
                <label class="form-label">Açıklama</label>
                <textarea name="notes" class="form-control" rows="3">{{ $productionOrder->notes }}</textarea>
            </div>

            {{-- Güncelle Butonu --}}
            <div class="col-12 text-end">
                <button class="btn btn-success">Güncelle</button>
            </div>

        </div>
    </form>

    <hr class="my-5">

    {{-- ✅ İLAVE REVİZYON FORMU (SADECE addition DURUMUNDA) --}}
    <div id="addition-section" class="d-none">
        <h5 class="mb-3">İlave Reçete Revizyonu</h5>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Hammadde</th>
                    <th>Eski %</th>
                    <th>Yeni %</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productionOrder->formula->details as $detail)
                    <tr>
                        <td>{{ $detail->rawMaterial->name }}</td>
                        <td>{{ $detail->percentage }}%</td>
                        <td>
                            <input type="number" class="form-control new-percent-input"
                                   name="new_percentages[{{ $detail->id }}]"
                                   value="{{ $detail->percentage }}" step="0.01">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div id="revised-tonaj-box" class="alert alert-info d-none">
            <strong>Revize Tonaj:</strong> <span id="revised-tonaj-value">0</span> kg
        </div>
    </div>

    {{-- ✅ DOLUM FORMU (SADECE in_filling DURUMUNDA) --}}
    <div id="filling-section" class="d-none mt-5">
        <h5 class="mb-3">Doluma Gönder</h5>

        <form action="{{ route('production_orders.send_to_filling.store', $productionOrder->id) }}" method="POST">
            @csrf

            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Fiziksel Reçete</label>
                    <select name="physical_formula_id" id="physical_formula_id" class="form-select">
                        <option value="">Seçiniz</option>
                        @foreach($physicalFormulas as $formula)
                            <option value="{{ $formula->id }}"
                                data-details="@json($formula->details->load('parameter'))">
                                {{ $formula->product_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Dolum Adedi</label>
                    <input type="number" name="filled_quantity" id="filled_quantity" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Yoğunluk (kg/L)</label>
                    <input type="number" name="density" id="density" class="form-control" step="0.01">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Hacim (L)</label>
                    <input type="number" name="volume" id="volume" class="form-control" step="0.01">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Dolum Hattı</label>
                    <select name="filling_line_id" class="form-select">
                        @foreach($lines as $line)
                            <option value="{{ $line->id }}">{{ $line->name }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="mt-4">
                <h6>Hesaplanan Malzemeler</h6>
                <div id="material-summary" class="d-none">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Parametre</th>
                                <th>Etiket</th>
                                <th>Oran</th>
                                <th>Miktar (kg)</th>
                            </tr>
                        </thead>
                        <tbody id="material-rows"></tbody>
                    </table>

                    <div id="match-alert" class="alert alert-success d-none">Tonaj uyumlu.</div>
                    <div id="mismatch-alert" class="alert alert-danger d-none">Tonaj aşıldı!</div>
                </div>
            </div>

            <div class="text-end mt-3">
                <button class="btn btn-warning">Doluma Gönder</button>
            </div>

        </form>
    </div>

</div>

{{-- ✅ TÜM SCRIPT BLOĞU --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const statusSelect = document.getElementById('status');
    const additionSection = document.getElementById('addition-section');
    const fillingSection = document.getElementById('filling-section');

    /* ✅ FORM AÇMA KAPAMA */
    function toggleForms() {
        additionSection.classList.toggle('d-none', statusSelect.value !== 'addition');
        fillingSection.classList.toggle('d-none', statusSelect.value !== 'in_filling');
    }
    statusSelect.addEventListener('change', toggleForms);
    toggleForms();

    /* ✅ REVİZYON TONAJ HESAPLAMA */
    const oldTonaj = {{ $productionOrder->quantity_kg }};
    const percentInputs = document.querySelectorAll('.new-percent-input');
    const revisedTonajBox = document.getElementById('revised-tonaj-box');
    const revisedTonajValue = document.getElementById('revised-tonaj-value');

    function calculateRevisedTonaj() {
        let total = 0;
        percentInputs.forEach(i => total += parseFloat(i.value) || 0);
        revisedTonajValue.textContent = (oldTonaj * (total / 100)).toFixed(2);
        revisedTonajBox.classList.remove('d-none');
    }
    percentInputs.forEach(i => i.addEventListener('input', calculateRevisedTonaj));

    /* ✅ DOLUM HESAPLAMA */
    const formulaSelect = document.getElementById('physical_formula_id');
    const filledInput = document.getElementById('filled_quantity');
    const densityInput = document.getElementById('density');
    const volumeInput = document.getElementById('volume');
    const materialTable = document.getElementById('material-summary');
    const materialRows = document.getElementById('material-rows');
    const matchAlert = document.getElementById('match-alert');
    const mismatchAlert = document.getElementById('mismatch-alert');
    const productionTonaj = {{ $productionOrder->quantity_kg }};

    function calculateMaterials() {
        if (!formulaSelect.value) return;

        const selected = formulaSelect.options[formulaSelect.selectedIndex];
        const details = JSON.parse(selected.dataset.details || "[]");

        const adet = parseFloat(filledInput.value);
        const density = parseFloat(densityInput.value);
        const volume = parseFloat(volumeInput.value);

        if (!adet || !density || !volume) return;

        const totalKg = adet * density * volume;
        materialRows.innerHTML = '';

        details.forEach(d => {
            const miktar = (d.quantity / 100) * totalKg;
            materialRows.innerHTML += `
                <tr>
                    <td>${d.parameter?.name ?? '-'}</td>
                    <td>${d.selection_label ?? '-'}</td>
                    <td>${d.quantity}%</td>
                    <td>${miktar.toFixed(2)}</td>
                </tr>
            `;
        });

        materialTable.classList.remove('d-none');

        if (totalKg <= productionTonaj) {
            matchAlert.classList.remove('d-none');
            mismatchAlert.classList.add('d-none');
        } else {
            matchAlert.classList.add('d-none');
            mismatchAlert.classList.remove('d-none');
        }
    }

    [formulaSelect, filledInput, densityInput, volumeInput].forEach(el => {
        if (el) el.addEventListener('input', calculateMaterials);
    });

});
</script>

@endsection