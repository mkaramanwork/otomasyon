@extends('layouts.admin')

@section('title','Laboratuvar Raporlama')

@section('content')
<div class="container">
    <h4>📑 Laboratuvar Raporlama</h4>

    <!-- ✅ Analizi Tamamlanan Numuneler -->
    <h5>✔ Analizi Tamamlanan Numuneler</h5>

    <!-- Arama kutusu -->
    <input type="text" id="sampleSearch" class="form-control mb-2" placeholder="Firma, Ürün Adı veya Batch No ile ara...">

    <table class="table table-bordered table-striped" id="samplesTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Firma</th>
                <th>Ürün Adı</th>
                <th>Batch No</th>
                <th>Rapor No</th>
                <th>Rapor Tarihi</th>
                <th>Durum</th>
                <th class="text-center">İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @forelse($samples as $sample)
                @php
                    $total   = $sample->parameters->count();
                    $done    = $sample->parameters->whereNotNull('pivot.result')->count();
                    $percent = $total > 0 ? round(($done / $total) * 100) : 0;
                @endphp
                @if($percent == 100)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $sample->company_name }}</td>
                    <td>{{ $sample->labProduct->product_name ?? '-' }}</td>
                    <td>{{ $sample->batch_no }}</td>
                    <td>{{ $sample->report_no ?? '-' }}</td>
                    <td>{{ $sample->report_date ? $sample->report_date->format('d.m.Y') : '-' }}</td>
                    <td><span class="badge bg-success">Tamamlandı</span></td>
                    <td class="text-center">
                        <!-- Görüntüle -->
                        <a href="{{ route('samples.show',$sample->id) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i> Görüntüle
                        </a>

                        <!-- Rapor Yaz Butonları -->
                        @if($sample->report_date)
                            <!-- Word -->
                            <a href="{{ route('samples.report.generate',['sample'=>$sample->id,'lang'=>'TR']) }}" class="btn btn-sm btn-primary">TR</a>
                            <a href="{{ route('samples.report.generate',['sample'=>$sample->id,'lang'=>'EN']) }}" class="btn btn-sm btn-secondary">EN</a>
                            <a href="{{ route('samples.report.generate',['sample'=>$sample->id,'lang'=>'US']) }}" class="btn btn-sm btn-dark">US</a>

                            <!-- Mail Gönder -->
                            <form action="{{ route('samples.sendMail',$sample->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="fas fa-envelope"></i> Mail Gönder
                                </button>
                            </form>
                        @else
                            <span class="badge bg-warning">Önce rapor kaydedilmeli</span>
                        @endif
                    </td>
                </tr>
                @endif
            @empty
                <tr>
                    <td colspan="8" class="text-center">Henüz tamamlanan numune yok.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- ✅ Toast Bildirimi -->
@if(session('success'))
    <div class="alert alert-success mt-3">
        {{ session('success') }}
    </div>
@endif

<!-- JS: Firma, Ürün Adı ve Batch No arama -->
<script>
document.getElementById('sampleSearch').addEventListener('keyup', function() {
    let searchValue = this.value.toLowerCase();
    let rows = document.querySelectorAll('#samplesTable tbody tr');
    rows.forEach(row => {
        let firma   = row.cells[1]?.innerText.toLowerCase();
        let urun    = row.cells[2]?.innerText.toLowerCase();
        let batch   = row.cells[3]?.innerText.toLowerCase();
        if (firma.includes(searchValue) || urun.includes(searchValue) || batch.includes(searchValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
@endsection
