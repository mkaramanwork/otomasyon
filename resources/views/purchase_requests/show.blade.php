@extends('layouts.admin')

@section('content')
<div class="container">
    <h4>Satınalma Talebi Detayı</h4>

    <p><strong>Talep Kodu:</strong> {{ $request->request_code }}</p>
    <p><strong>Durum:</strong> {{ $request->status }}</p>

    <h5>Ürünler</h5>
    <table class="table">
        <thead>
            <tr>
                <th>Ürün</th>
                <th>Adet</th>
                <th>Birim</th>
                <th>Açıklama</th>
                <th>Tedarikçi</th>
                <th>Şartname</th>
            </tr>
        </thead>
        <tbody>
            @foreach($request->items as $item)
            <tr>
                <td>{{ $item->inventory->product_name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->unit }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->supplier->name ?? '-' }}</td>
                <td>
                    <strong>{{ $item->inventory->technicalSpecification->spec_title ?? '-' }}</strong><br>
                    {!! $item->inventory->technicalSpecification->spec_content ?? '' !!}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h5>Onaylayanlar</h5>
    <ul class="list-group">
        @if($request->lab_manager_name)
            <li class="list-group-item">Laboratuvar Müdürü: {{ $request->lab_manager_name }}</li>
        @endif
        @if($request->general_manager_name)
            <li class="list-group-item">Genel Müdür: {{ $request->general_manager_name }}</li>
        @endif
        @if($request->procurement_name)
            <li class="list-group-item">Satınalma: {{ $request->procurement_name }}</li>
        @endif
        @if($request->order_name)
            <li class="list-group-item">Sipariş: {{ $request->order_name }}</li>
        @endif
        @if($request->control_name)
            <li class="list-group-item">Kontrol: {{ $request->control_name }}</li>
        @endif
        @if($request->acceptance_name)
            <li class="list-group-item">Kabul: {{ $request->acceptance_name }}</li>
        @endif
    </ul>

    <div class="mt-3">
        <a href="{{ route('purchase-requests.approve',['request'=>$request->id,'step'=>'lab']) }}" class="btn btn-primary">Laboratuvar Müdürü Onayı</a>
        <a href="{{ route('purchase-requests.approve',['request'=>$request->id,'step'=>'gm']) }}" class="btn btn-primary">Genel Müdür Onayı</a>
        <a href="{{ route('purchase-requests.approve',['request'=>$request->id,'step'=>'procurement']) }}" class="btn btn-primary">Satınalma</a>
        <a href="{{ route('purchase-requests.approve',['request'=>$request->id,'step'=>'order']) }}" class="btn btn-primary">Sipariş</a>
        <a href="{{ route('purchase-requests.approve',['request'=>$request->id,'step'=>'control']) }}" class="btn btn-primary">Kontrol</a>
        <a href="{{ route('purchase-requests.approve',['request'=>$request->id,'step'=>'acceptance']) }}" class="btn btn-success">Kabul</a>
    </div>

    <div class="mt-3">
        <a href="{{ route('purchase-requests.index') }}" class="btn btn-secondary">Listeye Dön</a>
    </div>
</div>
@endsection