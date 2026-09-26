@extends('layouts.admin')
@section('title','Teklif Güncelle')

@section('content')
<div class="container">
    <h4 class="mb-3">✏️ Teklif Güncelle - {{ $offer->offer_no }}</h4>

    <form action="{{ route('offers.update',$offer->id) }}" method="POST">
        @csrf @method('PUT')

        <!-- Firma -->
        <div class="mb-3">
            <label class="form-label">Firma</label>
            <select name="customer_id" class="form-control" required>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" @if($offer->customer_id==$customer->id) selected @endif>
                        {{ $customer->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Başlık -->
        <div class="mb-3">
            <label class="form-label">Başlık</label>
            <input type="text" name="title" class="form-control" value="{{ $offer->title }}">
        </div>

        <!-- Teklif Metni -->
        <div class="mb-3">
            <label class="form-label">Teklif Metni</label>
            <textarea name="text" class="form-control" rows="4">{{ $offer->text }}</textarea>
        </div>

        <!-- Teklif Tarihi -->
        <div class="mb-3">
            <label class="form-label">Teklif Tarihi</label>
            <input type="date" name="offer_date" class="form-control" value="{{ $offer->offer_date }}">
        </div>

        <!-- Numune Adedi -->
        <div class="mb-3">
            <label class="form-label">Numune Adedi</label>
            <input type="number" name="sample_count" class="form-control" value="{{ $offer->sample_count }}">
        </div>

        <!-- Parametreler -->
        <h5 class="mt-4">Parametreler</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Seç</th>
                    <th>Parametre</th>
                    <th>Metot</th>
                    <th>Fiyat (₺)</th>
                    <th>Kapsam</th>
                </tr>
            </thead>
            <tbody>
                @foreach($parameters as $param)
                @php
                    // OfferParameter tablosundaki kayıtları kontrol et
                    $existing = $offer->parameters->firstWhere('parameter_id',$param->id);
                @endphp
                <tr>
                    <td>
                        <input type="checkbox" name="parameters[{{ $param->id }}][selected]" value="1"
                               @if($existing) checked @endif>
                        <input type="hidden" name="parameters[{{ $param->id }}][id]" value="{{ $param->id }}">
                    </td>
                    <td>{{ $param->parameter }}</td>
                    <td>
                        <input type="text" name="parameters[{{ $param->id }}][method]" class="form-control"
                               value="{{ $existing->method ?? $param->method_tr }}">
                    </td>
                    <td>
                        <input type="number" step="0.01" name="parameters[{{ $param->id }}][price]" class="form-control"
                               value="{{ $existing->price ?? '' }}">
                    </td>
                    <td>
                        <input type="text" name="parameters[{{ $param->id }}][scope]" class="form-control"
                               value="{{ $existing->scope ?? '' }}">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- İndirim Oranı -->
        <div class="mb-3">
            <label class="form-label">İndirim Oranı (%)</label>
            <input type="number" step="0.01" name="discount_rate" class="form-control" value="{{ $offer->discount_rate }}">
        </div>

        <!-- Kaydet -->
        <button type="submit" class="btn btn-primary">💾 Güncelle</button>
    </form>
</div>
@endsection
