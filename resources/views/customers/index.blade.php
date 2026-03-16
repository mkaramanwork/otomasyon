@extends('layouts.admin')

@section('title', 'Müşteri Listesi')

@section('content')
@if(session('success'))
    <div id="success-alert" class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">×</button>
    </div>
@endif

<div class="row mb-3">
    <div class="col-md-6">
        <h4>Müşteri Listesi</h4>
    </div>
    <div class="col-md-6 text-right">
        <a href="{{ route('customers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Ekle
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table id="datatable" class="table table-striped table-hover mb-0">
            <thead class="thead-dark">
                <tr>
                    <th>Firma Adı</th>
                    <th>Yetkili</th>
                    <th>E-posta</th>
                    <th>Telefon</th>
                    <th>Ekleyen</th>
                    <th class="text-right">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->contact_person }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->phone }}</td>
                        <td>{{ $customer->user->name ?? '-' }}</td>
                        <td class="text-right">
                            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Silmek istediğine emin misin?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Kayıtlı müşteri bulunamadı.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

