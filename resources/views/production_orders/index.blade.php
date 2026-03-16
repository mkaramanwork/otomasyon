@extends('layouts.admin')
@section('title', 'İş Emirleri')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">İş Emirleri Listesi</h4>
        <a href="{{ route('production_orders.create') }}" class="btn btn-primary">
            + Yeni İş Emri Oluştur
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Batch No</th>
                    <th>Ürün</th>
                    <th>Hat</th>
                    <th>Planlanan Tarih</th>
                    <th>Tonaj</th>
                    <th>Durum</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $order->batch_number }}</td>
                        <td>{{ $order->product->name ?? '-' }}</td>
                        <td>{{ $order->line->name ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($order->planned_at)->format('d.m.Y H:i') }}</td>
                        <td>{{ $order->quantity_kg }} {{ $order->unit }}</td>
                        <td>
                            @php
                                $statusColors = [
                                    'planned' => 'secondary',
                                    'in_progress' => 'primary',
                                    'completed' => 'success',
                                    'cancelled' => 'danger',
                                ];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$order->status] ?? 'dark' }}">
                                {{ strtoupper($order->status) }}
                            </span>
                        </td>
                        <td class="text-nowrap">
                            <a href="{{ route('production_orders.show', $order->id) }}" class="btn btn-sm btn-outline-info">
                                Detay
                            </a>
                            <a href="{{ route('production_orders.edit', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                Düzenle
                            </a>
                            <form action="{{ route('production_orders.destroy', $order->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('İş emrini silmek istediğine emin misin?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Sil</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">Henüz iş emri oluşturulmamış.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection