@extends('layouts.admin')
@section('title','Ürün Detayı')

@section('content')
<div class="container">
    <h4 class="mb-4">Ürün Detayı</h4>

    <ul class="list-group mb-4">
        <li class="list-group-item"><strong>Ürün Kodu:</strong> {{ $labProduct->product_code }}</li>
        <li class="list-group-item"><strong>Ürün Adı:</strong> {{ $labProduct->product_name }}</li>
    </ul>

    <h5>Parametreler</h5>
    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>Parametre</th>
                <th>Metot (TR)</th>
                <th>Min</th>
                <th>Max</th>
                <th>Net</th>
            </tr>
        </thead>
        <tbody>
            @foreach($labProduct->parameters as $param)
                <tr>
                    <td>{{ $param->parameter->parameter ?? '-' }}</td>
                    <td>{{ $param->parameter->method_tr ?? '-' }}</td>
                    <td>{{ $param->min_value }}</td>
                    <td>{{ $param->max_value }}</td>
                    <td>{{ $param->net_value }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-3">
        <a href="{{ route('lab_products.edit',$labProduct->id) }}" class="btn btn-warning">Düzenle</a>
        <a href="{{ route('lab_products.index') }}" class="btn btn-secondary">Listeye Dön</a>
    </div>
</div>
@endsection
