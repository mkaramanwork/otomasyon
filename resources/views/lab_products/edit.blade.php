@extends('layouts.admin')
@section('title','Ürün Düzenle')

@section('content')
<div class="container">
    <h4 class="mb-4">Ürün Düzenle</h4>

    <form action="{{ route('lab_products.update',$labProduct->id) }}" method="POST">
        @csrf @method('PUT')

        <div class="form-group mb-3">
            <label>Ürün Kodu</label>
            <input type="text" name="product_code" value="{{ $labProduct->product_code }}" class="form-control">
        </div>

        <div class="form-group mb-3">
            <label>Ürün Adı</label>
            <input type="text" name="product_name" value="{{ $labProduct->product_name }}" class="form-control">
        </div>

        <h5 class="mt-4">Parametreler</h5>
        @foreach($labProduct->parameters as $param)
            <div class="card mb-2">
                <div class="card-header">
                    {{ $param->parameter->parameter }} <small class="text-muted">({{ $param->parameter->method_tr }})</small>
                </div>
                <div class="card-body row">
                    <div class="col-md-4">
                        <input type="text" name="parameters[{{ $param->id }}][min]" value="{{ $param->min_value }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="parameters[{{ $param->id }}][max]" value="{{ $param->max_value }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="parameters[{{ $param->id }}][net]" value="{{ $param->net_value }}" class="form-control">
                    </div>
                </div>
            </div>
        @endforeach

        <button type="submit" class="btn btn-success mt-3">Güncelle</button>
        <a href="{{ route('lab_products.index') }}" class="btn btn-secondary mt-3">Listeye Dön</a>
    </form>
</div>
@endsection
