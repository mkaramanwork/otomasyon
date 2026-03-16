@extends('layouts.admin')

@section('content')
<div class="container">
    <h4>Satınalma Talepleri</h4>
    <a href="{{ route('purchase-requests.create') }}" class="btn btn-primary float-end">Yeni Talep</a>

    <table class="table mt-3">
        <thead>
            <tr>
                <th>Talep Kodu</th>
                <th>Ürün Sayısı</th>
                <th>Durum</th>
                <th>Onaylayanlar</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $req)
            <tr>
                <td>{{ $req->request_code }}</td>
                <td>{{ $req->items->count() }}</td>
                <td>{{ $req->status }}</td>
                <td>
                    @if($req->lab_manager_name) 
                        <span class="badge bg-info">Lab: {{ $req->lab_manager_name }}</span> 
                    @endif
                    @if($req->general_manager_name) 
                        <span class="badge bg-info">GM: {{ $req->general_manager_name }}</span> 
                    @endif
                    @if($req->procurement_name) 
                        <span class="badge bg-info">Satınalma: {{ $req->procurement_name }}</span> 
                    @endif
                    @if($req->order_name) 
                        <span class="badge bg-info">Sipariş: {{ $req->order_name }}</span> 
                    @endif
                    @if($req->control_name) 
                        <span class="badge bg-info">Kontrol: {{ $req->control_name }}</span> 
                    @endif
                    @if($req->acceptance_name) 
                        <span class="badge bg-success">Kabul: {{ $req->acceptance_name }}</span> 
                    @endif
                </td>
                <td>
                    <a href="{{ route('purchase-requests.show',$req->id) }}" class="btn btn-sm btn-info">Detay</a>
                    <a href="{{ route('purchase-requests.edit',$req->id) }}" class="btn btn-sm btn-warning">Düzenle</a>

                    @if($req->lab_manager_name) 
                        <a href="{{ route('purchase-requests.mail',$req->id) }}" class="btn btn-sm btn-success">
                            Mail İlet
                        </a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection