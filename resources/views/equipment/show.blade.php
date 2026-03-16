@extends('layouts.admin')
@section('title','Cihaz Detay')
@section('content')
<div class="container">
    <h4>{{ $equipment->name }} ({{ $equipment->tag }})</h4>

    <!-- QR Kod -->
    <div class="mb-3">
        {!! QrCode::size(150)->generate(route('equipment.show',$equipment->id)) !!}
    </div>

    <!-- Genel Bilgiler -->
    <ul class="list-group mb-3">
        <li class="list-group-item"><strong>Model:</strong> {{ $equipment->model }}</li>
        <li class="list-group-item"><strong>Seri No:</strong> {{ $equipment->serial_no }}</li>
        <li class="list-group-item"><strong>Durum:</strong> {{ $equipment->status }}</li>
        <li class="list-group-item"><strong>Kalibrasyon Tarihi:</strong> {{ $equipment->calibration_due_at }}</li>
    </ul>

    <!-- Bakım Kayıtları -->
    <div class="card mb-4">
        <div class="card-header">🔧 Bakım Kayıtları</div>
        <div class="card-body">
            <table class="table table-sm table-bordered">
                <thead><tr><th>Tarih</th><th>Tür</th><th>Sonuç</th></tr></thead>
                <tbody>
                    @forelse($equipment->maintenances as $m)
                        <tr><td>{{ $m->performed_at }}</td><td>{{ $m->type }}</td><td>{{ $m->result }}</td></tr>
                    @empty
                        <tr><td colspan="3" class="text-center">Kayıt yok</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Kalibrasyonlar -->
    <div class="card mb-4">
        <div class="card-header">📏 Kalibrasyonlar</div>
        <div class="card-body">
            <table class="table table-sm table-bordered">
                <thead><tr><th>Tarih</th><th>Sertifika No</th><th>Sağlayıcı</th><th>Geçerlilik</th><th>PDF Sertifika</th></tr></thead>
                <tbody>
                    @forelse($equipment->calibrations as $c)
                        <tr>
                            <td>{{ $c->performed_at }}</td>
                            <td>{{ $c->certificate_no }}</td>
                            <td>{{ $c->provider }}</td>
                            <td>{{ $c->valid_until }}</td>
                            <td>
                                @if($c->certificate_file)
                                    <a href="{{ asset('storage/'.$c->certificate_file) }}" target="_blank" class="btn btn-sm btn-outline-primary">Görüntüle</a>
                                @else
                                    <span class="text-muted">Yok</span>
                                @endif
                                <!-- Güncelleme Formu -->
                                <form action="{{ route('equipment.calibration.update', [$equipment->id, $c->id]) }}" 
                                      method="POST" enctype="multipart/form-data" class="mt-1">
                                    @csrf @method('PUT')
                                    <input type="file" name="certificate_file" class="form-control form-control-sm" accept="application/pdf">
                                    <button type="submit" class="btn btn-sm btn-warning mt-1">Güncelle</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">Kayıt yok</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- İç Kalite Kontrol -->
    <div class="card mb-4">
        <div class="card-header">🧪 İç Kalite Kontrol (IQC)</div>
        <div class="card-body">
            <table class="table table-sm table-bordered">
                <thead><tr><th>Tarih</th><th>Materyal</th><th>Sonuç</th><th>Kural</th></tr></thead>
                <tbody>
                    @forelse($equipment->iqcRecords as $iqc)
                        <tr>
                            <td>{{ $iqc->date }}</td>
                            <td>{{ $iqc->material }}</td>
                            <td>{{ $iqc->value }}</td>
                            <td>
                                <span class="badge bg-{{ $iqc->rule_status == 'OK' ? 'success' : 'danger' }}">
                                    {{ $iqc->rule_status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center">Kayıt yok</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Dış Kalite Kontrol -->
    <div class="card mb-4">
        <div class="card-header">🌍 Dış Kalite Kontrol (EQC/PT)</div>
        <div class="card-body">
            <table class="table table-sm table-bordered">
                <thead><tr><th>Program</th><th>Round</th><th>Sonuç</th><th>Z-Score</th></tr></thead>
                <tbody>
                    @forelse($equipment->eqcRecords as $eqc)
                        <tr><td>{{ $eqc->provider }}</td><td>{{ $eqc->round_no }}</td><td>{{ $eqc->result }}</td><td>{{ $eqc->z_score }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="text-center">Kayıt yok</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Dinamik Kayıt Formları -->
    <div class="card mb-4">
        <div class="card-header">➕ Yeni Kayıt Ekle</div>
        <div class="card-body">
            <div class="mb-3">
                <label>Kayıt Türü</label>
                <select id="recordType" class="form-select">
                    <option value="">Seçiniz...</option>
                    <option value="maintenance">Bakım</option>
                    <option value="calibration">Kalibrasyon</option>
                    <option value="iqc">IQC</option>
                    <option value="eqc">EQC</option>
                </select>
            </div>

            <!-- Bakım Formu -->
            <form id="maintenanceForm" action="{{ route('equipment.maintenance.store',$equipment->id) }}" method="POST" style="display:none;">
                @csrf
                <div class="mb-2"><label>Tür</label><input type="text" name="type" class="form-control" required></div>
                <div class="mb-2"><label>Tarih</label><input type="date" name="performed_at" class="form-control" required></div>
                <div class="mb-2"><label>Sonuç</label><input type="text" name="result" class="form-control"></div>
                <button type="submit" class="btn btn-success">Kaydet</button>
            </form>

            <!-- Kalibrasyon Formu -->
            <form id="calibrationForm" action="{{ route('equipment.calibration.store',$equipment->id) }}" method="POST" enctype="multipart/form-data" style="display:none;">
                @csrf
                <div class="mb-2"><label>Sertifika No</label><input type="text" name="certificate_no" class="form-control" required></div>
                <div class="mb-2"><label>Sağlayıcı</label><input type="text" name="provider" class="form-control" required></div>
                <div class="mb-2"><label>Tarih</label><input type="date" name="performed_at" class="form-control" required></div>
                <div class="mb-2"><label>Geçerlilik</label><input type="date" name="valid_until" class="form-control" required></div>
                <div class="mb-2"><label>Sertifika PDF</label><input type="file" name="certificate_file" class="form-control" accept="application/pdf"></div>
                <button type="submit" class="btn btn-success">Kaydet</button>
            </form>

            <!-- IQC Formu -->
            <form id="iqcForm" action="{{ route('equipment.iqc.store',$equipment->id) }}" method="POST" style="display:none;">
                @csrf
                <div class="mb-2"><label>Tarih</label><input type="date" name="date" class="form-control" required></div>
                <div class="mb-2"><label>Materyal</label><input type="text" name="material" class="form-control" required></div>
                <div class="mb-2"><label>Sonuç</label><input type="text" name="value" class="form-control" required></div>
                <div class="mb-2"><label>Kural</label>
                    <select name="rule_status" class="form-select">
                        <option value="OK">OK</option>
                        <option value="Fail">Fail</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Kaydet</button>
            </form>

            <!-- EQC Formu -->
            <form id="eqcForm" action="{{ route('equipment.eqc.store',$equipment->id) }}" method="POST" style="display:none;">
                @csrf
                <div class="mb-2"><label>Program</label><input type="text" name="provider" class="form-control" required></div>
                <div class="mb-2"><label>Round</label><input type="text" name="round_no" class="form-control" required></div>
                <div class="mb-2"><label>Sonuç</label><input type="text" name="result" class="form-control" required></div>
                <div class="mb-2"><label>Z-Score</label><input type="text" name="z_score" class="form-control"></div>
                <div class="mb-2"><label>Aksiyon Planı</label><textarea name="action_plan" class="form-control"></textarea></div>
                <button type="submit" class="btn btn-success">Kaydet</button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('recordType').addEventListener('change', function() {
    // tüm formları gizle
    document.querySelectorAll('#maintenanceForm,#calibrationForm,#iqcForm,#eqcForm')
        .forEach(f => f.style.display='none');
    // seçilen formu göster
    if(this.value) {
        document.getElementById(this.value+'Form').style.display='block';
    }
});
</script>
@endsection