@extends('layouts.admin')

@section('title', 'Ham Maddeler Listesi')

@section('content')
@if(session('success'))
    <div id="success-alert" class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

<div class="row mb-3">
    <div class="col-md-6">
        <h4>Ham Madde Listesi</h4>
    </div>
    <div class="col-md-6 text-right">
        <a href="{{ route('raw-materials.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Ekle
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-striped table-hover mb-0">
            <thead class="thead-dark">
                <tr>
                    <th>Ad</th>
                    <th>Birim</th>
                    <th>Stok (kg)</th>
                    <th>Fiyat (TL)</th>
                    <th>Fiyat (USD)</th>
                    <th>Fiyat (EUR)</th>
                    <th>Aktif Para Birimi</th>
                    <th>Ekleyen</th>
                    <th class="text-right">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rawMaterials as $material)
                    <tr>
                        <td>{{ $material->name }}</td>
                        <td>{{ $material->unit }}</td>
                        <td>{{ number_format($material->stock_kg, 2, ',', '.') }}</td>
                        <td>{{ number_format($material->unit_price_tl, 2, ',', '.') }}</td>
                        <td>{{ number_format($material->unit_price_usd, 2, ',', '.') }}</td>
                        <td>{{ number_format($material->unit_price_eur, 2, ',', '.') }}</td>
                        <td>{{ $material->currency }}</td>
                        <td>{{ $material->addedBy->name ?? '-' }}</td>
                        <td class="text-right">
                            <a href="{{ route('raw-materials.edit', $material->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('raw-materials.destroy', $material->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Silmek istediğine emin misin?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">Kayıtlı ham madde bulunamadı.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $rawMaterials->links() }}
</div>
@endsection