@extends('layouts.admin')
@section('title','Teklifler')

@section('content')
<div class="container">
    <h4 class="mb-3">📑 Teklifler</h4>

    <!-- Yeni Teklif Oluştur Butonu -->
    <a href="{{ route('offers.create') }}" class="btn btn-success mb-3">
        ➕ Yeni Teklif Oluştur
    </a>

    <!-- Teklifler Tablosu -->
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Teklif No</th>
                <th>Firma</th>
                <th>Başlık</th>
                <th>Tarih</th>
                <th>Durum</th>
                <th>Toplam</th>
                <th>Net</th>
                <th>Numune İlerleme</th> <!-- ✅ kolon -->
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @forelse($offers as $offer)
            <tr>
                <td>{{ $offer->offer_no }}</td>
                <td>{{ $offer->customer->name }}</td>
                <td>{{ $offer->title }}</td>
                <td>{{ $offer->offer_date }}</td>
                <td>
                    @if($offer->status === 'approved')
                        <span class="badge bg-success">Onaylı</span>
                    @else
                        <span class="badge bg-secondary">{{ ucfirst($offer->status) }}</span>
                    @endif
                </td>
                <td>{{ number_format($offer->total_amount,2) }} ₺</td>
                <td>{{ number_format($offer->net_amount,2) }} ₺</td>
                <td>
                    @php
                        $used  = $offer->samples()->count();   // bağlanan numune
                        $total = $used + $offer->sample_count; // başlangıçtaki toplam hak
                    @endphp
                    <span class="badge bg-info">{{ $used }}/{{ $total }}</span>
                </td>
                <td>
                    <a href="{{ route('offers.show',$offer->id) }}" class="btn btn-info btn-sm">Detay</a>
                    <a href="{{ route('offers.edit',$offer->id) }}" class="btn btn-warning btn-sm">Güncelle</a>
                    <form action="{{ route('offers.destroy',$offer->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Silmek istediğine emin misin?')">Sil</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">Henüz teklif bulunmamaktadır.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
