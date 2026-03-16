@extends('layouts.admin')

@section('title', 'Dolum Raporu')

@section('content')
<div class="container">
  {{-- 🔍 Filtre --}}
<form method="GET" class="row g-3 mb-4">
  <div class="col-md-3">
    <label for="start_date" class="form-label">Başlangıç Tarihi</label>
    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
  </div>
  <div class="col-md-3">
    <label for="end_date" class="form-label">Bitiş Tarihi</label>
    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
  </div>
  <div class="col-md-3">
    <label for="batch" class="form-label">Batch Numarası</label>
    <input type="text" name="batch" id="batch" class="form-control" value="{{ request('batch') }}" placeholder="örn: BATCH-2025">
  </div>
  <div class="col-md-3 align-self-end">
    <button type="submit" class="btn btn-primary">Filtrele</button>
    <a href="{{ route('reports.fillings') }}" class="btn btn-outline-secondary">Temizle</a>
  </div>
</form>


  {{-- 📋 Ana Tablo --}}
  <h4 class="mb-4">Dolum Raporu :          <tr>
          <td colspan="8" class="text-end fw-bold">Toplam Dolum:</td>
          <td class="fw-bold">{{ number_format($fillings->sum('total_kg'), 2) }} kg</td>
          <td colspan="4"></td>
        </tr></h4>

  @if($fillings->isEmpty())
    <div class="alert alert-info">Henüz dolum kaydı bulunmamaktadır.</div>
  @else
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Batch</th>
          <th>Ürün</th>
          <th>Dolum Hattı</th>
          <th>Reçete</th>
          <th>Adet</th>
          <th>Hacim (L)</th>
          <th>Yoğunluk</th>
          <th>Toplam (kg)</th>
          <th>Durum</th>
          <th>Tarih</th>
          <th>Detay</th>
        </tr>
      </thead>
      <tbody>
        @foreach($fillings as $filling)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $filling->productionOrder->batch_number }}</td>
            <td>{{ $filling->productionOrder->product->name ?? '-' }}</td>
            <td>{{ $filling->fillingLine->name ?? '-' }}</td>
            <td>{{ $filling->physicalFormula->product_name ?? '-' }}</td>
            <td>{{ $filling->filled_quantity }}</td>
            <td>{{ $filling->volume }}</td>
            <td>{{ $filling->density }}</td>
            <td><strong>{{ $filling->total_kg }} kg</strong></td>
            <td>
              <span class="badge bg-{{ $filling->statusColor() }}">
                {{ ucfirst(str_replace('_', ' ', $filling->status)) }}
              </span>
            </td>
            <td>{{ $filling->created_at->format('d.m.Y H:i') }}</td>
            <td>
              <div class="d-flex flex-wrap gap-1">

              <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#actualModal-{{ $filling->id }}">📦</button>
              <button class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#chemicalModal-{{ $filling->id }}">🔬</button>
              <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#logModal-{{ $filling->id }}">📜</button>
              </div>

            </td>
          </tr>

          {{-- 🧪 Modal: Fiziksel Reçete --}}
          <tr>
            <td colspan="12">
              <div class="modal fade" id="plannedModal-{{ $filling->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">🧪 Fiziksel Reçete – Batch: {{ $filling->productionOrder->batch_number }}</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <table class="table table-sm table-bordered">
                        <thead><tr><th>Parametre</th><th>Etiket</th><th>Yüzde (%)</th></tr></thead>
                        <tbody>
                          @foreach($filling->physicalFormula->details ?? [] as $detail)
                            <tr>
                              <td>{{ $detail->parameter->name ?? '-' }}</td>
                              <td>{{ $detail->selection_label ?? '-' }}</td>
                              <td>{{ $detail->quantity }}%</td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </td>
          </tr>

          {{-- 📦 Modal: Gerçek Kullanım --}}
          <tr>
            <td colspan="12">
              <div class="modal fade" id="actualModal-{{ $filling->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">📦 Gerçek Kullanım – Batch: {{ $filling->productionOrder->batch_number }}</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <table class="table table-sm table-bordered">
                        <thead><tr><th>Parametre</th><th>Etiket</th><th>Yüzde (%)</th><th>Miktar (kg)</th></tr></thead>
                        <tbody>
                          @foreach($filling->calculated_materials ?? [] as $material)
                            <tr>
                              <td>{{ $material['parameter'] }}</td>
                              <td>{{ $material['label'] }}</td>
                              <td>{{ $material['percentage'] }}%</td>
                              <td>{{ $material['calculated_quantity'] }} kg</td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </td>
          </tr>

          {{-- 🔬 Modal: Kimyasal Reçete --}}
          <tr>
            <td colspan="12">
              <div class="modal fade" id="chemicalModal-{{ $filling->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">🔬 Kimyasal Reçete – {{ $filling->productionOrder->product->name }}</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <table class="table table-sm table-bordered">
                        <thead><tr><th>Ham Madde</th><th>Yüzde (%)</th><th>Hesaplanan Miktar (kg)</th><th>Birim</th></tr></thead>
                        <tbody>
                          @foreach($filling->productionOrder->formula->details ?? [] as $detail)
                            @php $kg = ($detail->percentage / 100) * $filling->total_kg; @endphp
                            <tr>
                              <td>{{ $detail->rawMaterial->name ?? '-' }}</td>
                              <td>{{ number_format($detail->percentage, 2) }}</td>
                              <td>{{ number_format($kg, 2) }}</td>
                              <td>{{ $detail->unit ?? '-' }}</td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </td>
          </tr>

          {{-- 📜 Modal: Log Kayıtları --}}
          <tr>
            <td colspan="12">
              <div class="modal fade" id="logModal-{{ $filling->id }}" tabindex="-1">
                <div class="modal-dialog <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">📜 Üretim Logları – Batch: {{ $filling->productionOrder->batch_number }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    @if($filling->productionOrder->logs->isNotEmpty())
                      <ul class="list-group">
                        @foreach($filling->productionOrder->logs->sortByDesc('created_at') as $log)
                          <li class="list-group-item">
                            <strong>{{ $log->event }}</strong> – {{ $log->description }}<br>
                            <small>
                              Log Zamanı: {{ optional($log->created_at)->format('d.m.Y H:i') }} |
                              Kullanıcı: {{ $log->user->name ?? 'Sistem' }}
                            </small>
                          </li>
                        @endforeach
                      </ul>
                    @else
                      <div class="alert alert-warning mb-0">Bu batch için log kaydı bulunamadı.</div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan="8" class="text-end fw-bold">Toplam Dolum:</td>
          <td class="fw-bold">{{ number_format($fillings->sum('total_kg'), 2) }} kg</td>
          <td colspan="4"></td>
        </tr>
      </tfoot>
    </table>
  @endif
</div>
@endsection
