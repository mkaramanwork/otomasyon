@extends('layouts.admin')

@section('title','Manuel Numune Kabul')

@section('content')
<div class="container">
    <h4>📄 Manuel Numune Kabul (Yeni Kayıt)</h4>

    <form action="{{ route('samples.accept-manual-store') }}" method="POST">
        @csrf

        <div class="row g-3">

            <!-- ✅ Ürün (Select2 Aramalı) -->
            <div class="col-md-4">
                <label class="form-label">Ürün</label>
                <select name="product_id" id="productSelect" class="form-select" required>
                    <option value="">Ürün seçiniz...</option>
                    @foreach(\App\Models\Product::orderBy('name')->get() as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Firma -->
            <div class="col-md-4">
                <label class="form-label">Firma</label>
                <select name="customer_id" class="form-select" required>
                    @foreach(\App\Models\Customer::all() as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Batch No -->
            <div class="col-md-4">
                <label class="form-label">Batch No</label>
                <input type="text" name="batch_no" class="form-control" required>
            </div>

            <!-- Ülke -->
            <div class="col-md-4">
                <label class="form-label">Ülke</label>
                <input type="text" name="country" class="form-control" required>
            </div>

            <!-- Belirsizlik -->
            <div class="col-md-4">
                <label class="form-label">Belirsizlik</label>
                <select name="uncertainty" class="form-select" required>
                    <option value="0">Hayır</option>
                    <option value="1">Evet</option>
                </select>
            </div>

            <!-- Rapor Talebi -->
            <div class="col-md-4">
                <label class="form-label">Rapor Talebi</label>
                <select name="report_request" class="form-select" required>
                    <option value="0">Hayır</option>
                    <option value="1">Evet</option>
                </select>
            </div>

            <!-- Rapor No -->
            <div class="col-md-4">
                <label class="form-label">Rapor No</label>
                <input type="text" name="report_no" class="form-control">
            </div>

            <!-- Miktar -->
            <div class="col-md-4">
                <label class="form-label">Miktar</label>
                <input type="number" name="amount" class="form-control" min="1" value="300">
            </div>

            <!-- Numune Türü -->
            <div class="col-md-4">
                <label class="form-label">Numune Türü</label>
                <select name="sample_type" class="form-select" required>
                    <option value="madeni yağ">Madeni Yağ</option>
                    <option value="antifriz">Antifriz</option>
                    <option value="gres">Gres</option>
                </select>
            </div>

            <!-- Alındığı Yer -->
            <div class="col-md-4">
                <label class="form-label">Alındığı Yer</label>
                <select name="taken_place" class="form-select" required>
                    <option value="üretim hattı">Üretim Hattı</option>
                    <option value="ham madde">Ham Madde</option>
                    <option value="harmanlama">Harmanlama</option>
                    <option value="dolum numunesi">Dolum Numunesi</option>
                    <option value="müşteri">Müşteri</option>
                </select>
            </div>

            <!-- Alan Kişi -->
            <div class="col-md-4">
                <label class="form-label">Alan Kişi</label>
                <input type="text" name="sampling_person" class="form-control">
            </div>

            <!-- Alan Kişi Tipi -->
            <div class="col-md-4">
                <label class="form-label">Alan Kişi Tipi</label>
                <select name="taken_by" class="form-select" required>
                    <option value="üretim çalışanı">Üretim Çalışanı</option>
                    <option value="müşteri">Müşteri</option>
                </select>
            </div>

            <!-- Alınma Tarihi -->
            <div class="col-md-4">
                <label class="form-label">Alınma Tarihi</label>
                <input type="datetime-local" name="taken_at" class="form-control" required>
            </div>

            <!-- Koruma -->
            <div class="col-md-4">
                <label class="form-label">Koruma</label>
                <select name="protection" class="form-select" required>
                    <option value="korumasız">Korumasız</option>
                    <option value="bilinmiyor">Bilinmiyor</option>
                    <option value="soğuk taşıma">Soğuk Taşıma</option>
                    <option value="mühürlü">Mühürlü</option>
                </select>
            </div>

            <!-- Açıklama -->
            <div class="col-12">
                <label class="form-label">Açıklama</label>
                <textarea name="description" class="form-control" rows="2"></textarea>
            </div>

            <!-- Parametre Seçimi -->
            <div class="col-12 mt-3">
                <label class="form-label">Analiz Parametreleri</label>
                <input type="text" id="parameterSearch" class="form-control mb-2" placeholder="Numune türü veya parametreye göre ara...">

                <table class="table table-sm table-bordered" id="parametersTable">
                    <thead>
                        <tr>
                            <th>Seç</th>
                            <th>Numune Türü</th>
                            <th>Parametre</th>
                            <th>Metot (TR)</th>
                            <th>Birim</th>
                            <th>Kapsam</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($parameters as $param)
                            <tr>
                                <td><input type="checkbox" name="parameters[]" value="{{ $param->id }}"></td>
                                <td>{{ $param->sample_type ?? '-' }}</td>
                                <td>{{ $param->parameter ?? '-' }}</td>
                                <td>{{ $param->method_tr ?? '-' }}</td>
                                <td>{{ $param->unit ?? '-' }}</td>
                                <td>{{ $param->scope ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

        <button type="submit" class="btn btn-primary mt-3">Kaydet ve Kabul Et</button>
        <a href="{{ route('samples.index') }}" class="btn btn-secondary mt-3">Geri Dön</a>
    </form>
</div>

{{-- ✅ Select2 CSS & JS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

{{-- ✅ Ürün Arama Aktif --}}
<script>
$('#productSelect').select2({
    placeholder: "Ürün seçiniz...",
    allowClear: true,
    width: '100%'
});
</script>

{{-- ✅ Parametre Arama --}}
<script>
document.getElementById('parameterSearch').addEventListener('keyup', function() {
    let searchValue = this.value.toLowerCase();
    let rows = document.querySelectorAll('#parametersTable tbody tr');

    rows.forEach(row => {
        let sampleType = row.cells[1].innerText.toLowerCase();
        let parameter  = row.cells[2].innerText.toLowerCase();

        row.style.display = (sampleType.includes(searchValue) || parameter.includes(searchValue))
            ? '' : 'none';
    });
});
</script>
@endsection