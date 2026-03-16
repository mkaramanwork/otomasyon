@extends('layouts.admin')

@section('title','TDS Detay')

@section('content')
<div class="container">

    <h4>📄 TDS Detay</h4>

    <div class="row g-3">

        <div class="col-md-6">
            <strong>Ürün Adı:</strong> {{ $doc->product_name }}
        </div>
        <div class="col-md-6">
            <strong>Ürün Kodu:</strong> {{ $doc->product_code }}
        </div>

        <div class="col-md-6">
            <strong>Ürün Grubu:</strong> {{ $doc->productGroup->name ?? '-' }}
        </div>
        <div class="col-md-6">
            <strong>Ürün:</strong> {{ $doc->product->name ?? '-' }}
        </div>

        <div class="col-md-12 mt-3">
            <strong>Açıklama:</strong>
            <p>{{ $doc->description }}</p>
        </div>

        {{-- ✅ Özellikler --}}
        <div class="col-md-12 mt-3">
            <strong>Özellikler:</strong>
            <ul>
                @foreach($doc->features ?? [] as $f)
                    <li>{{ $f['title'] ?? '' }}: {{ $f['value'] ?? '' }}</li>
                @endforeach
            </ul>
        </div>

        {{-- ✅ Parametreler --}}
        <div class="col-md-12 mt-4">
            <strong>Parametreler:</strong>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Parametre</th>
                        <th>Birim</th>
                        <th>Metot</th>
                        <th>Değer</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($doc->parameters ?? [] as $p)
                    <tr>
                        <td>{{ $p['parameter'] ?? '' }}</td>
                        <td>{{ $p['unit'] ?? '' }}</td>
                        <td>{{ $p['method_tr'] ?? '' }}</td>
                        <td>{{ $p['value'] ?? '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    <div class="mt-4">
        <a href="{{ route('tds.index') }}" class="btn btn-secondary">⬅️ Listeye Dön</a>
        <a href="{{ route('tds.exportWord',$doc->id) }}" class="btn btn-primary">📥 Word Olarak İndir</a>
    </div>

</div>
@endsection