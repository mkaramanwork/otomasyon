@extends('layouts.admin')

@section('title','Numune Kabul')

@section('content')
<div class="container">
    <h4>📄 Numune Kabul</h4>

    <!-- Numune bilgileri -->
    <div class="mb-3">
        <strong>Batch No:</strong> {{ $sample->batch_no }} <br>
        <strong>Firma:</strong> {{ $sample->company_name }} <br>
        <strong>Rapor No:</strong> {{ $sample->report_no ?? '-' }}
    </div>

    <!-- Parametre seçimi formu -->
    <form action="{{ route('samples.acceptStore',$sample->id) }}" method="POST">
        @csrf

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Ülke</label>
                <input type="text" name="country" class="form-control" required value="{{ old('country', $sample->country) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Belirsizlik</label>
                <select name="uncertainty" class="form-select" required>
                    <option value="0" @selected(old('uncertainty', $sample->uncertainty)==0)>Hayır</option>
                    <option value="1" @selected(old('uncertainty', $sample->uncertainty)==1)>Evet</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Rapor Talebi</label>
                <select name="report_request" class="form-select" required>
                    <option value="0" @selected(old('report_request', $sample->report_request)==0)>Hayır</option>
                    <option value="1" @selected(old('report_request', $sample->report_request)==1)>Evet</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Rapor No</label>
                <input type="text" name="report_no" class="form-control" value="{{ old('report_no', $sample->report_no) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Miktar</label>
                <input type="number" name="amount" class="form-control" min="1" value="{{ old('amount', $sample->amount ?? 300) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Koruma</label>
                <input type="text" name="protection" class="form-control" required value="{{ old('protection', $sample->protection) }}">
            </div>
            <div class="col-12">
                <label class="form-label">Açıklama</label>
                <textarea name="description" class="form-control" rows="2">{{ old('description', $sample->description) }}</textarea>
            </div>

            <!-- Parametre seçimi -->
            <div class="col-12">
                <label class="form-label">Analiz Parametreleri</label>
                <table class="table table-sm table-bordered">
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
                                    <input type="checkbox" name="parameters[]" value="{{ $param->id }}">
                                </td>
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

        <button type="submit" class="btn btn-success mt-3">Kabul Et</button>
        <a href="{{ route('samples.index') }}" class="btn btn-secondary mt-3">Geri Dön</a>
    </form>
</div>
@endsection