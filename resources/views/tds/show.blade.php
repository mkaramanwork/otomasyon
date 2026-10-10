@extends('layouts.admin')

@section('title','TDS Detay')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">📄 TDS Detay</h4>
            <small class="text-muted">{{ $doc->product_name }}</small>
        </div>

        <a href="{{ route('tds.index') }}" class="btn btn-secondary">
            ⬅️ TDS Listesi
        </a>
    </div>

    <div class="card mb-3">

        <div class="card-header">
            <strong>📦 Ürün Bilgileri</strong>
        </div>

        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-6">
                    <strong>Marka:</strong>
                    {{ $doc->brand?->name ?? '-' }}
                </div>

                <div class="col-md-6">
                    <strong>Ürün:</strong>
                    {{ $doc->product->name ?? '-' }}
                </div>

                <div class="col-md-6">
                    <strong>Ürün Adı:</strong>
                    {{ $doc->product_name ?? '-' }}
                </div>

                <div class="col-md-6">
                    <strong>Ürün Kodu:</strong>
                    {{ $doc->product_code ?? '-' }}
                </div>

                <div class="col-md-6">
                    <strong>Ürün Grubu:</strong>
                    {{ $doc->product_group ?? '-' }}
                </div>

                <div class="col-md-6">
                    <strong>Oluşturulma:</strong>
                    {{ $doc->created_at?->format('d.m.Y H:i') ?? '-' }}
                </div>

            </div>

        </div>

    </div>

    <div class="card mb-3">

        <div class="card-header">
            <strong>📝 TDS İçeriği</strong>
        </div>

        <div class="card-body">

            <div class="mb-4">
                <strong>Açıklama:</strong>

                <div class="mt-2">
                    {!! nl2br(e($doc->description ?? '-')) !!}
                </div>
            </div>

            <div>
                <strong>Onaylar / Performans Seviyeleri:</strong>

                <div class="mt-2">
                    {!! nl2br(e($doc->approvals ?? '-')) !!}
                </div>
            </div>

        </div>

    </div>

    <div class="card mb-3">

        <div class="card-header">
            <strong>⭐ Özellikler</strong>
        </div>

        <div class="card-body">

            @if(!empty($doc->features) && count($doc->features) > 0)

                <ul class="mb-0">

                    @foreach($doc->features as $f)

                        <li class="mb-1">

                            @if(!empty($f['title']))
                                <strong>{{ $f['title'] }}</strong>
                            @endif

                            @if(!empty($f['title']) && !empty($f['value']))
                                :
                            @endif

                            {{ $f['value'] ?? '' }}

                        </li>

                    @endforeach

                </ul>

            @else

                <span class="text-muted">
                    Özellik girilmemiş.
                </span>

            @endif

        </div>

    </div>

    <div class="card mb-3">

        <div class="card-header">
            <strong>🧪 Teknik Parametreler</strong>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-bordered table-hover mb-0">

                    <thead class="table-light">

                        <tr>
                            <th>Parametre</th>
                            <th>Birim</th>
                            <th>Metot</th>
                            <th>Değer</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($doc->parameters ?? [] as $p)

                            <tr>

                                <td>
                                    {{ $p['parameter'] ?? '' }}
                                </td>

                                <td>
                                    {{ $p['unit'] ?? '' }}
                                </td>

                                <td>
                                    {{ $p['method_tr'] ?? '' }}
                                </td>

                                <td>
                                    <strong>
                                        {{ $p['value'] ?? '' }}
                                    </strong>
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="4"
                                    class="text-center text-muted py-3">

                                    Parametre girilmemiş.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">

        <a href="{{ route('tds.index') }}"
           class="btn btn-secondary">

            ⬅️ Listeye Dön

        </a>

        <a href="{{ route('tds.edit',$doc->id) }}"
           class="btn btn-warning">

            ✏️ Düzenle

        </a>

    </div>

</div>

@endsection