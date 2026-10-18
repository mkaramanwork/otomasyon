@extends('layouts.admin')

@section('title', 'Erişim Yetkisi Yok')

@section('content')
<div class="container py-5">
    <div class="card shadow-sm border-0 mx-auto"
         style="max-width:600px;">
        <div class="card-body text-center p-5">
            <i class="bi bi-shield-lock text-danger"
               style="font-size:60px;"></i>

            <h4 class="mt-3">
                Bu Alana Erişim Yetkiniz Bulunmamaktadır
            </h4>

            <p class="text-muted mt-3">
                İlgili modüle erişim izniniz bulunmuyor.
                Yetki talebi için sistem yöneticinizle
                iletişime geçebilirsiniz. Murat KARAMAN
            </p>

            <a href="{{ route('home') }}"
               class="btn btn-primary mt-3">
                <i class="bi bi-house"></i>
                Ana Sayfaya Dön
            </a>
        </div>
    </div>
</div>
@endsection