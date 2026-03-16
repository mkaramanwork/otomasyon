@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Envanter Listesi</h4>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addInventoryModal">
            + Envanter Ekle
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Sıra No</th>
                <th>Ürün Kodu</th>
                <th>Ürün Adı</th>
                <th>Tür</th>
                <th>Açıklama</th>
                <th>Teknik Şartname</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventories as $inv)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $inv->product_code }}</td>
                <td>{{ $inv->product_name }}</td>
                <td>
                    {{ $inv->type }}
                    @if($inv->type === 'Diğer')
                        ({{ $inv->type_other }})
                    @endif
                </td>
                <td>{{ $inv->description }}</td>
                <td>
                    @if($inv->technicalSpecification)
                        {{ $inv->technicalSpecification->spec_title }}
                        <br>Kod: {{ $inv->technicalSpecification->document_code }}
                    @else
                        <a href="{{ route('technical-spec.create',$inv->id) }}" class="btn btn-sm btn-primary">
                            Şartname Ekle
                        </a>
                    @endif
                </td>
                <td>
                    <a href="{{ route('inventories.show',$inv->id) }}" class="btn btn-info btn-sm">Detay</a>
                    <a href="{{ route('inventories.edit',$inv->id) }}" class="btn btn-warning btn-sm">Düzenle</a>
                    <form action="{{ route('inventories.destroy',$inv->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal: Yeni Envanter -->
<div class="modal fade" id="addInventoryModal" tabindex="-1" aria-labelledby="addInventoryModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('inventories.store') }}" method="POST">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addInventoryModalLabel">Yeni Envanter Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Ürün Kodu</label>
                    <input type="text" name="product_code" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Ürün Adı</label>
                    <input type="text" name="product_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Tür</label>
                    <select name="type" class="form-select" required>
                        <option value="Kimyasal">Kimyasal</option>
                        <option value="CRM">CRM</option>
                        <option value="Cihaz">Cihaz</option>
                        <option value="Sarf Malzeme">Sarf Malzeme</option>
                        <option value="Diğer">Diğer</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Diğer Tür</label>
                    <input type="text" name="type_other" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Açıklama</label>
                    <textarea name="description" class="form-control"></textarea>
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