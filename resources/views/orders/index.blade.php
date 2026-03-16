@extends('layouts.admin')

@section('title', 'Sipariş & Teklifler')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
  <h4>📑 Sipariş & Teklif Listesi</h4>
  <div>
    <a href="{{ route('orders.create') }}" class="btn btn-success">
      <i class="fas fa-plus"></i> Yeni Sipariş/Teklif
    </a>

  </div>
</div>


  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if($orders->isEmpty())
    <div class="alert alert-info">Henüz sipariş veya teklif bulunamadı.</div>
  @else
    <div class="card card-outline card-primary">
      <div class="card-body p-0">
        <table class="table table-bordered table-striped align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Müşteri</th>
              <th>Ekleyen</th>
              <th>Tip</th>
              <th>Durum</th>
              <th>İlerleme</th> <!-- yeni sütun -->
              <th>Oluşturulma</th>
              <th class="text-center">İşlem</th>
            </tr>
          </thead>
          <tbody>
            @foreach($orders as $order)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $order->customer_name }}</td>
                <td>{{ $order->user->name ?? '-' }}</td>
                <td>{{ ucfirst($order->type) }}</td>
                <td>{{ ucfirst($order->status) }}</td>
                <td>
                  {{ $order->items->where('status','tamamlandı')->count() }} / {{ $order->items->count() }}
                </td>
                <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                <td class="text-center">
                  <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}">
                    <i class="fas fa-info-circle"></i> Detay
                  </button>
                  <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-edit"></i> Düzenle
                  </a>
                  <form action="{{ route('orders.destroy', $order->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Bu kaydı silmek istediğinize emin misiniz?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                      <i class="fas fa-trash"></i> Sil
                    </button>
                  </form>
                </td>
              </tr>

          <!-- Modal -->
          <div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1"
              aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Sipariş Detayı - {{ $order->customer_name }}</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <p><strong>Müşteri:</strong> {{ $order->customer_name }}</p>
                  <p><strong>Email:</strong> {{ $order->customer_email }}</p>
                  <p><strong>Tip:</strong> {{ ucfirst($order->type) }}</p>
                  <p><strong>Durum:</strong> {{ ucfirst($order->status) }}</p>
                  <p><strong>Ekleyen:</strong> {{ $order->user->name ?? '-' }}</p>
                  <p><strong>Oluşturulma:</strong> {{ $order->created_at->format('d.m.Y H:i') }}</p>

                  <hr>
                  <h6>Ürünler</h6>
                  @foreach($order->items as $item)
                    <div class="row mb-2">
                      <div class="col-md-4">{{ $item->product->name }}</div>
                      <div class="col-md-2">{{ $item->quantity }} adet</div>
                      <div class="col-md-2">Durum: {{ ucfirst($item->status) }}</div>
                      <div class="col-md-2">
                        @if($item->status == 'beklemede')
                          <form action="{{ route('orders.items.complete',$item->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning">Üretildi</button>
                          </form>
                        @else
                          <span class="badge bg-success">Tamamlandı</span>
                        @endif
                      </div>
                    </div>
                  @endforeach

                  <hr>
                  <h6>Toplam Bilgiler</h6>
                  <p><strong>Genel İskonto:</strong> {{ $order->global_discount ?? 0 }} %</p>
                  <p><strong>Toplam TL:</strong> {{ number_format($order->total_price,2) }} ₺</p>
                  <p><strong>Toplam USD:</strong> {{ number_format($order->total_price_usd,2) }} $</p>
                </div>
                <div class="modal-footer">
                  <a href="{{ route('orders.export', $order->id) }}" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Excel Aktar
                  </a>
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                </div>
              </div>
            </div>
          </div>

            @endforeach
          </tbody>
        </table>
      </div>
      <div class="card-footer text-muted">
        Toplam Kayıt: {{ $orders->count() }}
      </div>
    </div>
  @endif
</div>
@endsection