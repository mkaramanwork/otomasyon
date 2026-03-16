@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Tedarikçi Detayı</h4>
        <!-- Word'e Aktar Butonu -->
        <a href="{{ route('suppliers.export',$supplier->id) }}" class="btn btn-success">
            Word'e Aktar
        </a>
    </div>

    <table class="table table-bordered mb-4">
        <tr><th>Firma Adı</th><td>{{ $supplier->name }}</td></tr>
        <tr><th>İletişim</th><td>{{ $supplier->contact }}</td></tr>
        <tr><th>Adres</th><td>{{ $supplier->address }}</td></tr>
        <tr><th>Akreditasyon</th><td>{{ $supplier->accreditation }}</td></tr>
        <tr><th>Durum</th><td>{{ $supplier->status }}</td></tr>
        <tr><th>Performans Puanı</th><td>{{ $supplier->performance_score }}</td></tr>
        <tr><th>Notlar</th><td>{{ $supplier->notes }}</td></tr>
    </table>

    <div class="mb-3">
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Geri</a>
        <a href="{{ route('suppliers.edit',$supplier->id) }}" class="btn btn-warning">Düzenle</a>
        <a href="{{ route('suppliers.evaluate',$supplier->id) }}" class="btn btn-primary">Değerlendir</a>
    </div>

    <h5>Geçmiş Değerlendirmeler</h5>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Tarih</th>
                <th>Değerlendiren</th>
                <th>Unvan</th>
                <th>Puan</th>
                <th>Durum</th>
            </tr>
        </thead>
        <tbody>
            @forelse($evaluations as $eval)
            <tr>
                <td>{{ $eval->evaluation_date }}</td>
                <td>{{ $eval->user->name }}</td>
                <td>{{ $eval->title }}</td>
                <td>{{ $eval->score }}</td>
                <td>{{ $eval->status }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Henüz değerlendirme yapılmamış.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection