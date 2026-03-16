@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Onaylı Tedarikçiler</h4>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
            + Onaylı Tedarikçi Ekle
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Sıra No</th>
                <th>Firma Adı</th>
                <th>İletişim</th>
                <th>Akreditasyon</th>
                <th>Durum</th>
                <th>Performans Puanı</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suppliers as $supplier)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $supplier->name }}</td>
                <td>{{ $supplier->contact }}</td>
                <td>{{ $supplier->accreditation }}</td>
                <td>{{ $supplier->status }}</td>
                <td>{{ $supplier->performance_score }}</td>
                <td>
                    <a href="{{ route('suppliers.show',$supplier->id) }}" class="btn btn-info btn-sm">Detay</a>
                    <a href="{{ route('suppliers.edit',$supplier->id) }}" class="btn btn-warning btn-sm">Düzenle</a>
                    <a href="{{ route('suppliers.evaluate',$supplier->id) }}" class="btn btn-primary btn-sm">Değerlendir</a>
                    <form action="{{ route('suppliers.destroy',$supplier->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal: Yeni Tedarikçi -->
<div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('suppliers.store') }}" method="POST">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSupplierModalLabel">Yeni Tedarikçi Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Firma Adı</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>İletişim</label>
                    <input type="text" name="contact" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Adres</label>
                    <input type="text" name="address" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Akreditasyon</label>
                    <input type="text" name="accreditation" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Durum</label>
                    <select name="status" class="form-select">
                        <option value="Aktif">Aktif</option>
                        <option value="Pasif">Pasif</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Performans Puanı</label>
                    <input type="number" name="performance_score" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Notlar</label>
                    <textarea name="notes" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Kaydet</button>
            </div>
        </div>
    </form>
  </div>
</div>
@endsection