@extends('layouts.admin')
@section('title','Yeni Ürün Ekle')

@section('content')
<div class="container">
    <h4 class="mb-4">Yeni Ürün Ekle</h4>

    <form action="{{ route('lab_products.store') }}" method="POST">
        @csrf
        <div class="form-group mb-3">
            <label>Ürün Kodu</label>
            <input type="text" name="product_code" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label>Ürün Adı</label>
            <input type="text" name="product_name" class="form-control" required>
        </div>

        <h5 class="mt-4">Parametreler</h5>
        @foreach($parameters as $parameter)
            <div class="card mb-2">
                <div class="card-header">
                    {{ $parameter->parameter }} <small class="text-muted">({{ $parameter->method_tr }})</small>
                </div>
                <div class="card-body row">
                    <div class="col-md-4">
                        <input type="text" name="parameters[{{ $parameter->id }}][min]" placeholder="Min" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="parameters[{{ $parameter->id }}][max]" placeholder="Max" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="parameters[{{ $parameter->id }}][net]" placeholder="Net" class="form-control">
                    </div>
                </div>
            </div>
        @endforeach

        <button type="submit" class="btn btn-success mt-3">Kaydet</button>
        <a href="{{ route('lab_products.index') }}" class="btn btn-secondary mt-3">Listeye Dön</a>
    </form>
</div>
@endsection
