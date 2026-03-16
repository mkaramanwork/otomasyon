<div class="card card-outline card-success mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">📊 Kalite Kontrol Kartı</h5>
        <span class="badge bg-{{ $equipment->iqcRecords->last()?->rule_status == 'OK' ? 'success' : 'danger' }}">
            {{ $equipment->iqcRecords->last()?->rule_status ?? 'Durum Yok' }}
        </span>
    </div>
    <div class="card-body">
        <!-- IQC Tablosu -->
        <h6>🧪 İç Kalite Kontrol (IQC)</h6>
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>Tarih</th>
                    <th>Materyal</th>
                    <th>Sonuç</th>
                    <th>Kural</th>
                </tr>
            </thead>
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
                    <tr>
                        <td colspan="4" class="text-center text-muted">Henüz IQC kaydı yok</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- EQC Tablosu -->
        <h6>🌍 Dış Kalite Kontrol (EQC/PT)</h6>
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>Program</th>
                    <th>Round</th>
                    <th>Sonuç</th>
                    <th>Z-Score</th>
                </tr>
            </thead>
            <tbody>
                @forelse($equipment->eqcRecords as $eqc)
                    <tr>
                        <td>{{ $eqc->provider }}</td>
                        <td>{{ $eqc->round_no }}</td>
                        <td>{{ $eqc->result }}</td>
                        <td>
                            <span class="badge bg-{{ $eqc->z_score <= 2 ? 'success' : 'danger' }}">
                                {{ $eqc->z_score }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">Henüz EQC kaydı yok</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>