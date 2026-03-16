@extends('layouts.admin')
@section('title', 'Formülasyonlar')

@section('content')
<div class="row mb-3">
    <div class="col-md-6">
        <h4>Formülasyonlar</h4>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('formulas.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Yeni Formülasyon
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row mb-3">
    <div class="col-md-4">
        <input type="text" id="searchInput" class="form-control" placeholder="Formülasyon adına göre ara...">
    </div>
    <div class="col-md-4">
        <select id="typeFilter" class="form-control">
            <option value="">Tüm Tipler</option>
            <option value="karışım">Karışım</option>
            <option value="sıvı">Sıvı</option>
            <option value="toz">Toz</option>
        </select>
    </div>
    <div class="col-md-4">
        <select id="ownerFilter" class="form-control">
            <option value="">Tüm Ekleyenler</option>
            <option value="{{ auth()->id() }}">Sadece Benimkiler</option>
        </select>
    </div>
</div>

<table class="table table-bordered table-striped" id="formulaTable">
    <thead>
        <tr>
            <th>#</th>
            <th>Adı</th>
            <th>Tip</th>
            <th>Açıklama</th>
            <th>Ekleyen</th>
            <th>Ham Madde Sayısı / Tutar</th> <!-- ✅ revize -->
            <th>İşlemler</th>
        </tr>
    </thead>
    <tbody>
        @forelse($formulas as $i => $formula)
        <tr data-name="{{ strtolower($formula->name) }}" data-type="{{ strtolower($formula->type) }}" data-owner="{{ $formula->added_by }}">
            <td>{{ $i + 1 }}</td>
            <td class="searchable">{{ $formula->name }}</td>
            <td>{{ ucfirst($formula->type) }}</td>
            <td>{{ Str::limit($formula->description, 50) }}</td>
            <td>{{ $formula->addedBy->name ?? 'Sistem' }}</td>
            <td>
                {{ $formula->details->count() }} adet  
                <br>
                <strong>{{ number_format($formula->total_cost, 2, ',', '.') }} TL</strong>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modal-{{ $formula->id }}">
                    <i class="fas fa-eye"></i> Reçeteyi Gör
                </button>
                <a href="{{ route('formulas.edit', $formula->id) }}" class="btn btn-sm btn-warning">
                    <i class="fas fa-edit"></i> Güncelle
                </a>
                <form action="{{ route('formulas.destroy', $formula->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash"></i> Sil
                    </button>
                </form>
            </td>
        </tr>

        <!-- Modal -->
        <div class="modal fade" id="modal-{{ $formula->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $formula->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel{{ $formula->id }}">Reçete: {{ $formula->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Tip:</strong> {{ ucfirst($formula->type) }}</p>
                        <p><strong>Açıklama:</strong> {{ $formula->description }}</p>
                        <hr>
                        <h6>Ham Maddeler:</h6>
                        <ul>
                            @php $total = 0; @endphp
                            @foreach($formula->details as $detail)
                                @php $total += $detail->percentage; @endphp
                                <li>
                                    {{ $detail->rawMaterial->name }} - {{ $detail->percentage }}% {{ $detail->unit }}
                                </li>
                            @endforeach
                        </ul>
                        @if(round($total, 2) !== 100)
                            <div class="alert alert-warning mt-2">
                                Toplam oran %{{ round($total, 2) }} — %100 olmalı!
                            </div>
                        @endif

                        <hr>
                        <h6>Reçete Tutarı:</h6>
                        <p><strong>{{ number_format($formula->total_cost, 2, ',', '.') }} TL</strong></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <tr>
            <td colspan="7" class="text-center">Henüz formülasyon tanımlanmadı.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection

@push('javascript')
<script>
    function applyFilters() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const type = document.getElementById('typeFilter').value.toLowerCase();
        const owner = document.getElementById('ownerFilter').value;

        document.querySelectorAll('#formulaTable tbody tr').forEach(row => {
            const name = row.dataset.name;
            const rowType = row.dataset.type;
            const rowOwner = row.dataset.owner;

            const matchName = name.includes(search);
            const matchType = !type || rowType === type;
            const matchOwner = !owner || rowOwner === owner;

            row.style.display = (matchName && matchType && matchOwner) ? '' : 'none';
        });
    }

    document.getElementById('searchInput').addEventListener('keyup', applyFilters);
    document.getElementById('typeFilter').addEventListener('change', applyFilters);
    document.getElementById('ownerFilter').addEventListener('change', applyFilters);
</script>
@endpush