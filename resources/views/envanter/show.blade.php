@extends('layouts.admin')

@section('content')
<div class="container">
    <h4>Envanter Detayı</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Ürün Kodu:</strong> {{ $inventory->product_code }}</p>
            <p><strong>Ürün Adı:</strong> {{ $inventory->product_name }}</p>
            <p><strong>Tür:</strong> {{ $inventory->type }}
                @if($inventory->type === 'Diğer')
                    ({{ $inventory->type_other }})
                @endif
            </p>
            <p><strong>Açıklama:</strong> {{ $inventory->description }}</p>
        </div>
    </div>

    <h5>Teknik Şartname</h5>
    @if($inventory->technicalSpecification)
        <div class="card">
            <div class="card-body">
                <p><strong>Başlık:</strong> {{ $inventory->technicalSpecification->spec_title }}</p>
                <p><strong>Detay:</strong> {{ $inventory->technicalSpecification->spec_detail }}</p>
                <p><strong>Standartlar:</strong> {{ $inventory->technicalSpecification->standards }}</p>
                <p><strong>Uygunluk:</strong> {{ $inventory->technicalSpecification->compliance }}</p>
                <p><strong>Doküman Kodu:</strong> {{ $inventory->technicalSpecification->document_code }}</p>

                <a href="{{ route('technical-spec.edit',$inventory->technicalSpecification->id) }}" 
                   class="btn btn-sm btn-warning mt-2">
                    Şartnameyi Düzenle
                </a>
            </div>
        </div>
    @else
        <a href="{{ route('technical-spec.create',$inventory->id) }}" class="btn btn-primary mt-2">
            Şartname Ekle
        </a>
    @endif

    <div class="mt-3">
        <a href="{{ route('inventories.edit',$inventory->id) }}" class="btn btn-warning">Düzenle</a>
        <a href="{{ route('inventories.index') }}" class="btn btn-secondary">Listeye Dön</a>
    </div>
</div>
@endsection