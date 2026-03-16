@extends('layouts.admin')
@section('title','Cihaz Yönetimi')
@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>🔧 Cihaz Listesi</h4>
        <a href="{{ route('equipment.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Cihaz Ekle
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- ✅ Kritik Kalibrasyon Tablosu -->
    @if($criticalEquipments->count())
    <div class="card mb-4">
        <div class="card-header bg-danger text-white">⚠️ Kalibrasyon Süresi 30 Gün Altında Olan Cihazlar</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kod</th>
                        <th>Adı</th>
                        <th>Model</th>
                        <th>Seri No</th>
                        <th>Kalan Gün</th>
                        <th>Detay</th>
                        <th>İşlemler</th>
                        <th>QR Kod</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($criticalEquipments as $eq)
                    <tr>
                        <td>{{ $eq->tag }}</td>
                        <td>{{ $eq->name }}</td>
                        <td>{{ $eq->model }}</td>
                        <td>{{ $eq->serial_no }}</td>
                        <td>
                            <span class="badge bg-danger">{{ intval($eq->days_left) }} gün</span>
                            <small class="text-muted"> ({{ $eq->calibration_valid_until }})</small>
                        </td>
                        <td><a href="{{ route('equipment.show',$eq->id) }}" class="btn btn-info btn-sm">Detay</a></td>
                        <td>
                            <a href="{{ route('equipment.edit',$eq->id) }}" class="btn btn-warning btn-sm">Güncelle</a>
                            <form action="{{ route('equipment.destroy',$eq->id) }}" method="POST" style="display:inline;" 
                                  onsubmit="return confirm('Bu cihazı silmek istediğine emin misin?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                            </form>
                        </td>
                        <td>
                            {!! QrCode::size(80)->generate(route('equipment.show',$eq->id)) !!}
                            <a href="{{ route('equipment.show',$eq->id) }}" download class="btn btn-sm btn-outline-secondary mt-1">İndir</a>
                            @if($eq->calibrations->last()?->certificate_file)
                                <a href="{{ asset('storage/'.$eq->calibrations->last()->certificate_file) }}" 
                                   target="_blank" class="btn btn-sm btn-outline-primary mt-1">PDF Sertifika</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- ✅ Genel Cihaz Listesi -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Kod</th>
                <th>Adı</th>
                <th>Model</th>
                <th>Seri No</th>
                <th>Durum</th>
                <th>Kalan Kalibrasyon</th>
                <th>Detay</th>
                <th>İşlemler</th>
                <th>QR Kod</th>
            </tr>
        </thead>
        <tbody>
            @forelse($equipments as $eq)
            <tr>
                <td>{{ $eq->tag }}</td>
                <td>{{ $eq->name }}</td>
                <td>{{ $eq->model }}</td>
                <td>{{ $eq->serial_no }}</td>
                <td>{{ $eq->status }}</td>
                <td>
                    @if($eq->days_left !== null)
                        @if($eq->days_left > 60)
                            <span class="badge bg-success">{{ intval($eq->days_left) }} gün</span>
                        @elseif($eq->days_left > 30)
                            <span class="badge bg-warning">{{ intval($eq->days_left) }} gün</span>
                        @else
                            <span class="badge bg-danger">{{ intval($eq->days_left) }} gün</span>
                        @endif
                        <small class="text-muted"> ({{ $eq->calibration_valid_until }})</small>
                    @else
                        <span class="text-muted">Kalibrasyon kaydı yok</span>
                    @endif
                </td>
                <td><a href="{{ route('equipment.show',$eq->id) }}" class="btn btn-info btn-sm">Detay</a></td>
                <td>
                    <a href="{{ route('equipment.edit',$eq->id) }}" class="btn btn-warning btn-sm">Güncelle</a>
                    <form action="{{ route('equipment.destroy',$eq->id) }}" method="POST" style="display:inline;" 
                          onsubmit="return confirm('Bu cihazı silmek istediğine emin misin?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                    </form>
                </td>
                <td>
                    {!! QrCode::size(80)->generate(route('equipment.show',$eq->id)) !!}
                    <a href="{{ route('equipment.show',$eq->id) }}" download class="btn btn-sm btn-outline-secondary mt-1">İndir</a>
                    @if($eq->calibrations->last()?->certificate_file)
                        <a href="{{ asset('storage/'.$eq->calibrations->last()->certificate_file) }}" 
                           target="_blank" class="btn btn-sm btn-outline-primary mt-1">PDF Sertifika</a>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">Henüz cihaz tanımlı değil</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection