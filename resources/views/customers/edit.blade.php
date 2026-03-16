@extends('layouts.admin')

@section('title', 'Müşteri Düzenle')

@section('content')
@if(session('success'))
    <div id="success-alert" class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">×</button>
    </div>
@endif

<div class="row mb-3">
    <div class="col-md-6">
        <h4>Müşteri Düzenle</h4>
    </div>
    <div class="col-md-6 text-right">
        <a href="{{ route('customers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('customers.update', $customer->id) }}" method="POST">
            @csrf @method('PUT')

            <div class="form-group">
                <label for="name">Firma Adı</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $customer->name) }}" required>
            </div>

            <div class="form-group">
                <label for="contact_person">Yetkili Kişi</label>
                <input type="text" name="contact_person" id="contact_person" class="form-control" value="{{ old('contact_person', $customer->contact_person) }}">
            </div>

            <div class="form-group">
                <label for="email">E-posta</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $customer->email) }}">
            </div>

            <div class="form-group">
                <label for="phone">Telefon</label>
                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $customer->phone) }}">
            </div>

            <div class="form-group">
                <label for="address">Adres</label>
                <textarea name="address" id="address" class="form-control" rows="2">{{ old('address', $customer->address) }}</textarea>
            </div>

            <div class="text-right">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Güncelle
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