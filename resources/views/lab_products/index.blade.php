@extends('layouts.admin')
@section('title','Laboratuvar Ürünleri')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Laboratuvar Ürünleri Listesi</h4>
        <a href="{{ route('lab_products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Ürün Ekle
        </a>
    </div>

    @if($labProducts->isEmpty())
        <div class="alert alert-info">Henüz ürün kaydı bulunmamaktadır.</div>
    @else
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Ürün Kodu</th>
                            <th>Ürün Adı</th>
                            <th>Parametre Sayısı</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($labProducts as $lp)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $lp->product_code }}</td>
                                <td>{{ $lp->product_name }}</td>
                                <td>{{ $lp->parameters->count() }}</td>
                                <td>
                                    <a href="{{ route('lab_products.show',$lp->id) }}" class="btn btn-info btn-sm">Göster</a>
                                    <a href="{{ route('lab_products.edit',$lp->id) }}" class="btn btn-warning btn-sm">Düzenle</a>
                                    <form action="{{ route('lab_products.destroy',$lp->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Silmek istediğine emin misin?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Sil</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
