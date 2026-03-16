@extends('layouts.admin')

@section('content')
<div class="container">
    <h4>Yeni Envanter Ekle</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inventories.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Ürün Kodu</label>
            <input type="text" name="product_code" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Ürün Adı</label>
            <input type="text" name="product_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Tür</label>
            <select name="type" class="form-select" required>
                <option value="Kimyasal">Kimyasal</option>
                <option value="CRM">CRM</option>
                <option value="Cihaz">Cihaz</option>
                <option value="Sarf Malzeme">Sarf Malzeme</option>
                <option value="Diğer">Diğer</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Diğer Tür</label>
            <input type="text" name="type_other" class="form-control">
        </div>

        <div class="mb-3">
            <label>Açıklama</label>
            <textarea name="description" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Kaydet</button>
        <a href="{{ route('inventories.index') }}" class="btn btn-secondary">Geri</a>
    </form>
</div>
@endsection