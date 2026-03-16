@extends('layouts.admin')

@section('title','Numune Güncelle')

@section('content')
<div class="container">
    <h4>✏️ Numune Güncelle</h4>

    <form action="{{ route('samples.update',$sample->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Numune bilgileri (readonly) -->
        <div class="mb-3">
            <label class="form-label">Firma</label>
            <input type="text" class="form-control" value="{{ $sample->company_name }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Batch No</label>
            <input type="text" class="form-control" value="{{ $sample->batch_no }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Talep No</label>
            <input type="text" class="form-control" value="{{ $sample->request_no }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Numune Türü</label>
            <input type="text" class="form-control" value="{{ $sample->sample_type }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Alındığı Yer</label>
            <input type="text" class="form-control" value="{{ $sample->taken_place }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Alan Kişi</label>
            <input type="text" class="form-control" value="{{ $sample->taken_by }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Alınma Tarihi</label>
            <input type="text" class="form-control" value="{{ $sample->taken_at }}" readonly>
        </div>

        <!-- Parametreler -->
        <h5 class="mt-4">📊 Parametreler</h5>
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

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Parametreleri Güncelle
            </button>
            <a href="{{ route('samples.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Geri Dön
            </a>
        </div>
    </form>
</div>
@endsection