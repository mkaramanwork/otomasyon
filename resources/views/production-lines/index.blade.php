@extends('layouts.admin')

@section('title', 'Üretim Hatları')

@section('content')
@if(session('success'))
    <div id="success-alert" class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">×</button>
    </div>
@endif

<div class="row mb-3">
    <div class="col-md-6">
        <h4>Üretim Hatları</h4>
    </div>
    <div class="col-md-6 text-right">
        <a href="{{ route('production-lines.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Hat Ekle
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table id="datatable" class="table table-striped table-hover mb-0">
            <thead class="thead-dark">
                <tr>
                    <th>Hat Adı</th>
                    <th>Tip</th>
                    <th>Kapasite (L)</th>
                    <th>Ekleyen</th>
                    <th class="text-right">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productionLines as $line)
                    <tr>
                        <td>{{ $line->name }}</td>
                        <td>{{ ucfirst($line->type) }}</td>
                        <td>{{ $line->capacity_litre ?? '-' }}</td>
                        <td>{{ $line->addedBy->name ?? '-' }}</td>
                        <td class="text-right">
                            <a href="{{ route('production-lines.edit', $line->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('production-lines.destroy', $line->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Silmek istediğine emin misin?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Kayıtlı üretim hattı bulunamadı.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

