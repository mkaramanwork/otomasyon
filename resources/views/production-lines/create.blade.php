@extends('layouts.admin')

@section('title', 'Üretim Hattı Ekle')

@section('content')
@if(session('success'))
    <div id="success-alert" class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">×</button>
    </div>
@endif

<div class="row mb-3">
    <div class="col-md-6">
        <h4>Yeni Üretim Hattı Ekle</h4>
    </div>
    <div class="col-md-6 text-right">
        <a href="{{ route('production-lines.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('production-lines.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name">Hat Adı</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label for="type">Hat Tipi</label>
                <select name="type" id="type" class="form-control" required>
                    <option value="">Seçiniz</option>
                    <option value="harmanlama" {{ old('type') == 'harmanlama' ? 'selected' : '' }}>Harmanlama</option>
                    <option value="dolum" {{ old('type') == 'dolum' ? 'selected' : '' }}>Dolum</option>
                </select>
            </div>

            <div class="form-group">
                <label for="capacity_litre">Kapasite (Litre)</label>
                <input type="number" name="capacity_litre" id="capacity_litre" class="form-control" value="{{ old('capacity_litre') }}" min="0">
            </div>

            <div class="form-group">
                <label for="description">Açıklama</label>
                <textarea name="description" id="description" class="form-control" rows="2">{{ old('description') }}</textarea>
            </div>

            <div class="text-right">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Kaydet
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    setTimeout(function () {
        const alertBox = document.getElementById('success-alert');
        if (alertBox) {
            alertBox.classList.remove('show');
            alertBox.classList.add('fade');
            alertBox.style.display = 'none';
        }
    }, 3000);
</script>
@endpush