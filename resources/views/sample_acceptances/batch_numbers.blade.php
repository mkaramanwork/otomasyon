@extends('layouts.admin')

@section('title','Batch Numaraları')

@section('content')
<div class="container">
    <h4>📦 Batch Numaraları Listesi</h4>

    <!-- Toplam batch sayısı -->
    <p><strong>Toplam Batch Sayısı:</strong> {{ $batches->count() }}</p>

    <!-- Tekrar eden batch listesi -->
    @if($duplicates->count() > 0)
        <div class="alert alert-warning">
            <strong>⚠ Tekrar Eden Batch Numaraları:</strong> {{ $duplicates->implode(', ') }}
        </div>
    @endif

    <!-- Arama kutusu -->
    <input type="text" id="batchSearch" class="form-control mb-2" placeholder="Batch No ile ara...">

    <table class="table table-bordered table-striped" id="batchTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Batch No</th>
            </tr>
        </thead>
        <tbody>
            @forelse($batches as $batch)
                <tr @if($duplicates->contains($batch->batch_no)) class="table-danger" @endif>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $batch->batch_no }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center">Henüz batch kaydı yok.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- JS: Batch No arama -->
<script>
document.getElementById('batchSearch').addEventListener('keyup', function() {
    let searchValue = this.value.toLowerCase();
    let rows = document.querySelectorAll('#batchTable tbody tr');
    rows.forEach(row => {
        let batch = row.cells[1]?.innerText.toLowerCase();
        if (batch.includes(searchValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
@endsection