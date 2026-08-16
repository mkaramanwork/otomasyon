@extends('layouts.admin')

@section('title','Toplu Mail Gönderimi')

@section('content')
<div class="container">
    <h4>📧 Toplu Mail Gönderimi</h4>
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
    <!-- Ürün + Tarih Aralığı -->
    <form method="GET" action="{{ route('samples.toplumail') }}" class="row mb-3">
        <div class="col-md-4">
            <label for="product_id">Ürün Seç</label>
            <select id="product_id" name="product_id" class="form-control">
                <option value="">Tümü</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                        {{ $product->product_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="start_date">Başlangıç Tarihi</label>
            <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
        <div class="col-md-3">
            <label for="end_date">Bitiş Tarihi</label>
            <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-search"></i> Ara
            </button>
        </div>
    </form>

    <!-- ✅ Rapor Listesi -->
    @if($samples->count())
    <form method="POST" action="{{ route('samples.sendBulk') }}">
        @csrf
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all"> Tümünü Seç</th>
                    <th>Firma</th>
                    <th>Ürün</th>
                    <th>Rapor No</th>
                    <th>Rapor Tarihi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($samples as $sample)
                <tr>
                    <td><input type="checkbox" name="sample_ids[]" value="{{ $sample->id }}"></td>
                    <td>{{ $sample->company_name }}</td>
                    <td>{{ $sample->labProduct->product_name }}</td>
                    <td>{{ $sample->report_no }}</td>
                    <td>{{ $sample->report_date?->format('d.m.Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Ekstra Mail Adresleri -->
        <label for="emails">Ekstra Mail Adresleri</label>
        <textarea id="emails" name="emails" class="form-control mb-2" placeholder="Mail adreslerini virgülle ayırın (boş bırakabilirsiniz)"></textarea>

        <!-- Gönderim seçenekleri -->
        <button type="submit" name="mode" value="selected" class="btn btn-success">
            <i class="fas fa-envelope"></i> Seçilenleri Gönder
        </button>
        <button type="submit" name="mode" value="all" class="btn btn-warning">
            <i class="fas fa-envelope"></i> Tümünü Gönder
        </button>
    </form>
    @else
        <div class="alert alert-info">Seçilen ürün ve tarihlerde rapor bulunamadı.</div>
    @endif

    <!-- Sabit Gönderilen Mailler -->
    <h5 class="mt-4">📌 Sabit Gönderilen Mailler</h5>
    <ul>
        @foreach($defaultEmails as $mail)
            <li>{{ $mail }}</li>
        @endforeach
    </ul>
</div>

<script>
document.getElementById('select-all')?.addEventListener('click', function(e) {
    const checkboxes = document.querySelectorAll('input[name="sample_ids[]"]');
    checkboxes.forEach(cb => cb.checked = e.target.checked);
});
</script>
@endsection
