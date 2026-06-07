@extends('layouts.admin')

@section('title','Numune Güncelle')

@section('content')
<div class="container">
    <h4>✏️ Numune Güncelle</h4>

    <form action="{{ route('samples.update',$sample->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3">

            <!-- Ürün -->
            <div class="col-md-4">
                <label class="form-label">Ürün</label>
                <select name="product_id" id="labProductSelect" class="form-select" required>
                    @foreach($products as $lp)
                        <option value="{{ $lp->id }}" @if($sample->product_id == $lp->id) selected @endif>
                            {{ $lp->product_code }} - {{ $lp->product_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Firma -->
            <div class="col-md-4">
                <label class="form-label">Firma</label>
                <input type="text" name="company_name" class="form-control" value="{{ old('company_name',$sample->company_name) }}">
            </div>

            <!-- Batch No -->
            <div class="col-md-4">
                <label class="form-label">Batch No</label>
                <input type="text" name="batch_no" class="form-control" value="{{ old('batch_no',$sample->batch_no) }}">
            </div>

            <!-- Talep No -->
            <div class="col-md-4">
                <label class="form-label">Talep No</label>
                <input type="text" name="request_no" class="form-control" value="{{ old('request_no',$sample->request_no) }}">
            </div>

            <!-- Numune Türü -->
            <div class="col-md-4">
                <label class="form-label">Numune Türü</label>
                <select name="sample_type" class="form-select">
                    <option value="madeni yağ" @if($sample->sample_type=="madeni yağ") selected @endif>Madeni Yağ</option>
                    <option value="antifriz" @if($sample->sample_type=="antifriz") selected @endif>Antifriz</option>
                    <option value="gres" @if($sample->sample_type=="gres") selected @endif>Gres</option>
                </select>
            </div>

            <!-- Alındığı Yer (ENUM) -->
            <div class="col-md-4">
                <label class="form-label">Alındığı Yer</label>
                <select name="taken_place" class="form-select">
                    <option value="üretim hattı" @if($sample->taken_place=="üretim hattı") selected @endif>Üretim Hattı</option>
                    <option value="ham madde" @if($sample->taken_place=="ham madde") selected @endif>Ham Madde</option>
                    <option value="harmanlama" @if($sample->taken_place=="harmanlama") selected @endif>Harmanlama</option>
                    <option value="dolum numunesi" @if($sample->taken_place=="dolum numunesi") selected @endif>Dolum Numunesi</option>
                    <option value="müşteri" @if($sample->taken_place=="müşteri") selected @endif>Müşteri</option>
                    <option value="kantar" @if($sample->taken_place=="kantar") selected @endif>Kantar</option>
                </select>
            </div>

            <!-- Alan Kişi -->
            <div class="col-md-4">
                <label class="form-label">Alan Kişi</label>
                <input type="text" name="sampling_person" class="form-control" value="{{ old('sampling_person',$sample->sampling_person) }}">
            </div>

            <!-- Alan Kişi Tipi (ENUM) -->
            <div class="col-md-4">
                <label class="form-label">Alan Kişi Tipi</label>
                <select name="taken_by" class="form-select">
                    <option value="üretim çalışanı" @if($sample->taken_by=="üretim çalışanı") selected @endif>Üretim Çalışanı</option>
                    <option value="müşteri" @if($sample->taken_by=="müşteri") selected @endif>Müşteri</option>
                    <option value="kantarsorumlusu" @if($sample->taken_by=="kantarsorumlusu") selected @endif>Kantar Sorumlusu</option>
                </select>
            </div>

            <!-- Alınma Tarihi -->
            <div class="col-md-4">
                <label class="form-label">Alınma Tarihi</label>
                <input type="datetime-local" name="taken_at" class="form-control"
                       value="{{ old('taken_at',$sample->taken_at ? $sample->taken_at->format('Y-m-d\TH:i') : '') }}">
            </div>

            <!-- Ülke -->
            <div class="col-md-4">
                <label class="form-label">Ülke</label>
                <input type="text" name="country" class="form-control" value="{{ old('country',$sample->country) }}">
            </div>

            <!-- Rapor No -->
            <div class="col-md-4">
                <label class="form-label">Rapor No</label>
                <input type="text" name="report_no" class="form-control" value="{{ old('report_no',$sample->report_no) }}">
            </div>

            <!-- Miktar -->
            <div class="col-md-4">
                <label class="form-label">Miktar</label>
                <input type="number" name="amount" class="form-control" value="{{ old('amount',$sample->amount) }}">
            </div>

            <!-- Koruma -->
            <div class="col-md-4">
                <label class="form-label">Koruma</label>
                <input type="text" name="protection" class="form-control" value="{{ old('protection',$sample->protection) }}">
            </div>

            <!-- Açıklama -->
            <div class="col-12">
                <label class="form-label">Açıklama</label>
                <textarea name="description" class="form-control" rows="2">{{ old('description',$sample->description) }}</textarea>
            </div>

            <!-- Parametreler -->
            <div class="col-12 mt-3">
                <h5>📊 Parametreler</h5>
                <table class="table table-bordered">
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
                                <td>
                                    <input type="checkbox" name="selected_parameters[]" value="{{ $param->id }}"
                                           @if($sample->parameters->contains($param->id)) checked @endif>
                                </td>
                                <td>{{ $param->sample_type }}</td>
                                <td>{{ $param->parameter }}</td>
                                <td>{{ $param->method_tr }}</td>
                                <td>{{ $param->unit }}</td>
                                <td>{{ $param->scope }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Güncelle
            </button>
            <a href="{{ route('samples.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Geri Dön
            </a>
        </div>
    </form>
</div>

{{-- ✅ Select2 --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$('#labProductSelect').select2({
    placeholder: "Ürün seçiniz...",
    allowClear: true,
    width: '100%'
});
</script>
@endsection
