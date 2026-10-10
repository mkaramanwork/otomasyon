@extends('layouts.admin')

@section('title', 'SDS Formül Analizi')

@section('content')
@php
    $summary = $analysis['summary'] ?? [];
    $formulaInfo = $analysis['formula'] ?? [];
    $rawMaterials = $analysis['raw_materials'] ?? [];
    $components = $analysis['mixture_components'] ?? [];
    $issues = $analysis['issues'] ?? [];
@endphp

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h3 class="mb-1">🧪 SDS Formül Analizi</h3>
            <div class="text-muted">
                {{ $formula->name }} / Formula ID: {{ $formula->id }}
            </div>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('sds.formula-analysis.profile', [
                    'formula' => $formula->id,
                    'regulation_profile' => $regulationProfile
                ]) }}"
               class="btn btn-success">
                ⚙️ SDS Ek Verileri
            </a>

            @if(!empty($sdsDraft['summary']['publish_ready']))
                <a href="{{ route('sds.formula-analysis.print', [
                        'formula' => $formula->id,
                        'regulation_profile' => $regulationProfile
                    ]) }}"
                   target="_blank"
                   class="btn btn-dark">
                    🖨️ 16 Bölüm SDS Bas
                </a>
            @else
                <button type="button"
                        class="btn btn-dark"
                        disabled
                        title="Önce tüm bölümler Hazır olmalı">
                    🖨️ 16 Bölüm SDS Bas
                </button>
            @endif

            <a href="{{ route('sds.formula-analysis.index') }}"
               class="btn btn-outline-secondary">
                ← Formül Listesi
            </a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header bg-dark text-white">
            <strong>Analiz Profili</strong>
        </div>

        <div class="card-body">
            <form method="GET"
                  action="{{ route('sds.formula-analysis.show', $formula->id) }}">
                <div class="row align-items-end">
                    <div class="col-md-5">
                        <label class="form-label fw-bold">
                            Mevzuat Profili
                        </label>

                        <select name="regulation_profile"
                                class="form-select">
                            <option value="TR_KKDIK_SEA"
                                @selected($regulationProfile === 'TR_KKDIK_SEA')>
                                Türkiye - KKDİK / SEA
                            </option>

                            <option value="EU_REACH_CLP"
                                @selected($regulationProfile === 'EU_REACH_CLP')>
                                Avrupa Birliği - REACH / CLP
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <button type="submit"
                                class="btn btn-primary w-100">
                            Yeniden Analiz Et
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-2">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="fs-3 fw-bold">
                        {{ $summary['raw_material_count'] ?? 0 }}
                    </div>
                    <div class="small text-muted">
                        Hammadde
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="fs-3 fw-bold">
                        {{ $summary['raw_materials_with_current_sds'] ?? 0 }}
                    </div>
                    <div class="small text-muted">
                        Güncel SDS
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="fs-3 fw-bold">
                        {{ $summary['sds_component_count'] ?? 0 }}
                    </div>
                    <div class="small text-muted">
                        SDS Bileşeni
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="fs-3 fw-bold">
                        {{ $summary['components_with_regulatory_link'] ?? 0 }}
                    </div>
                    <div class="small text-muted">
                        Mevzuat Eşleşmesi
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card h-100 border-danger">
                <div class="card-body text-center">
                    <div class="fs-3 fw-bold text-danger">
                        {{ $summary['error_count'] ?? 0 }}
                    </div>
                    <div class="small text-muted">
                        Hata
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card h-100 border-warning">
                <div class="card-body text-center">
                    <div class="fs-3 fw-bold text-warning">
                        {{ $summary['warning_count'] ?? 0 }}
                    </div>
                    <div class="small text-muted">
                        Uyarı
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!empty($summary['ready_for_classification']))
        <div class="alert alert-success">
            <strong>Hazır:</strong>
            Formül verileri sınıflandırma motorunun bir sonraki aşamasına geçmeye uygun.
        </div>
    @else
        <div class="alert alert-warning">
            <strong>Kontrol gerekli:</strong>
            Önce aşağıdaki hata veya eksik SDS kayıtlarını tamamlayalım.
        </div>
    @endif


    @php
        $engineStatus = $classification['status'] ?? [];
        $engineResult = $classification['classification'] ?? [];
        $engineIssues = $classification['issues'] ?? [];
        $ruleDecisions = $classification['rule_decisions'] ?? [];
        $hazardClasses = $engineResult['hazard_classes'] ?? [];
        $hazardStatements = $engineResult['hazard_statements'] ?? [];
        $pictograms = $engineResult['pictograms'] ?? [];
        $signalWord = $engineResult['signal_word'] ?? null;
    @endphp

    <div class="card mb-3">
        <div class="card-header bg-dark text-white">
            <strong>Otomatik Karışım Sınıflandırması</strong>
        </div>

        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted">Durum</div>
                        @if(!empty($engineStatus['ready_for_section_2_3']))
                            <span class="badge text-bg-success">
                                Bölüm 2-3 Hazır
                            </span>
                        @else
                            <span class="badge text-bg-warning">
                                Kontrol Gerekli
                            </span>
                        @endif
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted">Signal Word</div>
                        <strong>{{ $signalWord ?? 'Yok / Belirlenmedi' }}</strong>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted">Piktogramlar</div>
                        @forelse($pictograms as $pictogram)
                            <span class="badge text-bg-danger me-1">
                                {{ $pictogram }}
                            </span>
                        @empty
                            <span class="text-muted">Yok</span>
                        @endforelse
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted">H Kodları</div>
                        @forelse($hazardStatements as $h)
                            <span class="badge text-bg-secondary me-1">
                                {{ $h }}
                            </span>
                        @empty
                            <span class="text-muted">Yok</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Tehlike Sınıfı</th>
                            <th>Kategori</th>
                            <th>H Kodu</th>
                            <th>Hesaplama Yöntemi</th>
                            <th>Sonuç</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($hazardClasses as $hazard)
                        <tr>
                            <td>
                                <strong>
                                    {{ $hazard['hazard_class'] ?? '-' }}
                                </strong>
                            </td>
                            <td>{{ $hazard['category'] ?? '-' }}</td>
                            <td>{{ $hazard['h_statement'] ?? '-' }}</td>
                            <td>{{ $hazard['method'] ?? '-' }}</td>
                            <td>
                                @if(isset($hazard['calculated_value']))
                                    {{ $hazard['calculated_value'] }}
                                    {{ $hazard['unit'] ?? '' }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5"
                                class="text-center text-muted py-3">
                                Hesaplanan bir tehlike sınıfı bulunmadı.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if(count($ruleDecisions))
        <div class="card mb-3">
            <div class="card-header bg-secondary text-white">
                <strong>Annex VI Özel Not Kararları</strong>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>Kimyasal</th>
                                <th style="width:90px;">Not</th>
                                <th style="width:180px;">Durum</th>
                                <th>Açıklama / Kanıt</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($ruleDecisions as $decision)
                            <tr>
                                <td>
                                    @if(!empty($decision['raw_material_name']))
                                        <div class="fw-bold">
                                            {{ $decision['raw_material_name'] }}
                                        </div>
                                    @endif
                                    <div>
                                        {{ $decision['chemical_name'] ?? '-' }}
                                    </div>
                                    <div class="small text-muted">
                                        CAS:
                                        {{ $decision['cas_no'] ?? '-' }}
                                    </div>
                                </td>

                                <td>
                                    <span class="badge text-bg-dark">
                                        Note {{ $decision['note'] ?? '-' }}
                                    </span>
                                </td>

                                <td>
                                    @if(($decision['status'] ?? '') === 'condition_met')
                                        <span class="badge text-bg-success">
                                            Koşul Sağlandı
                                        </span>
                                    @elseif(($decision['status'] ?? '') === 'condition_not_met')
                                        <span class="badge text-bg-danger">
                                            Koşul Sağlanmadı
                                        </span>
                                    @else
                                        <span class="badge text-bg-warning">
                                            Kanıt Gerekli
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    {{ $decision['message'] ?? '-' }}

                                    @if(($decision['status'] ?? '') === 'review_required')
                                        @php
                                            $noteRawMaterialId = $decision['raw_material_id'] ?? null;
                                            $noteComponentId = $decision['sds_component_id'] ?? null;
                                            $noteMaterial = collect($rawMaterials)->first(
                                                fn ($row) => (int)($row['raw_material_id'] ?? 0) === (int)$noteRawMaterialId
                                            );
                                            $noteProfileId = $noteMaterial['sds_profile_id'] ?? null;
                                        @endphp
                                        @if($noteRawMaterialId && $noteProfileId && $noteComponentId)
                                            <div class="mt-2">
                                                <a class="btn btn-sm btn-outline-primary"
                                                   href="{{ route('sds.raw-materials.show', [$noteRawMaterialId, $noteProfileId]) }}?evidence_component={{ $noteComponentId }}">
                                                    Hammadde SDS / Kanıtı Düzenle
                                                </a>
                                            </div>
                                        @elseif($noteRawMaterialId)
                                            <div class="mt-2">
                                                <a class="btn btn-sm btn-outline-secondary"
                                                   href="{{ route('sds.raw-materials.index', ['q' => $decision['raw_material_name'] ?? '']) }}">
                                                    Hammadde SDS Kaydına Git
                                                </a>
                                            </div>
                                        @endif
                                    @endif

                                    @if(!empty($decision['evidence']['source']))
                                        <div class="small text-muted mt-1">
                                            Kaynak:
                                            {{ $decision['evidence']['source'] }}

                                            @if(array_key_exists('value', $decision['evidence']))
                                                |
                                                Değer:
                                                {{ $decision['evidence']['value'] }}
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if(count($engineIssues))
        <div class="card mb-3">
            <div class="card-header bg-info">
                <strong>Sınıflandırma Motoru Kontrolleri</strong>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th style="width:110px;">Seviye</th>
                                <th style="width:280px;">Kod</th>
                                <th>Açıklama</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($engineIssues as $issue)
                            <tr>
                                <td>
                                    @if(($issue['severity'] ?? '') === 'error')
                                        <span class="badge text-bg-danger">HATA</span>
                                    @else
                                        <span class="badge text-bg-warning">UYARI</span>
                                    @endif
                                </td>
                                <td>
                                    <code>{{ $issue['code'] ?? '-' }}</code>
                                </td>
                                <td>
                                    {{ $issue['message'] ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

{{-- resources/views/sds/formula-analysis/show.blade.php --}}

@php
    $sdsDraftSummary = $sdsDraft['summary'] ?? [];
    $sdsDraftSections = $sdsDraft['sections'] ?? [];
@endphp

@if(!empty($sdsDraftSummary['publish_ready']))
    <div class="alert alert-success">
        <strong>Yayına Hazır:</strong>
        16 bölümün tamamı için zorunlu sistem kontrolleri tamamlandı. Word/PDF öncesi son insan kontrolü yapılmalıdır.
    </div>
@else
    <div class="alert alert-warning">
        <strong>SDS Taslağı Henüz Tamamlanmadı:</strong>
        "Veri Gerekli" veya "Bloke" görünen bölümleri tamamla.
        <a href="{{ route('sds.formula-analysis.profile', [
                'formula' => $formula->id,
                'regulation_profile' => $regulationProfile
            ]) }}"
           class="alert-link">
            SDS Ek Verilerini aç
        </a>.
    </div>
@endif

<div class="card mb-3">
    <div class="card-header bg-dark text-white">
        <strong>SDS 16 Bölüm Hazırlık Durumu</strong>
    </div>

    <div class="card-body">
        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <div class="border rounded p-3 text-center">
                    <div class="fs-4 fw-bold">
                        {{ $sdsDraftSummary['total_sections'] ?? 16 }}
                    </div>
                    <div class="small text-muted">
                        Toplam Bölüm
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="border rounded p-3 text-center">
                    <div class="fs-4 fw-bold text-success">
                        {{ $sdsDraftSummary['ready'] ?? 0 }}
                    </div>
                    <div class="small text-muted">
                        Hazır
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="border rounded p-3 text-center">
                    <div class="fs-4 fw-bold text-warning">
                        {{ $sdsDraftSummary['needs_data'] ?? 0 }}
                    </div>
                    <div class="small text-muted">
                        Veri Gerekli
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="border rounded p-3 text-center">
                    <div class="fs-4 fw-bold text-danger">
                        {{ $sdsDraftSummary['blocked'] ?? 0 }}
                    </div>
                    <div class="small text-muted">
                        Bloke
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped mb-0">
                <thead>
                    <tr>
                        <th style="width:80px;">Bölüm</th>
                        <th>Başlık</th>
                        <th style="width:150px;">Durum</th>
                        <th>Eksik / Sonraki Veri</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($sdsDraftSections as $section)
                    <tr>
                        <td>
                            <strong>{{ $section['number'] }}</strong>
                        </td>

                        <td>
                            {{ $section['title'] }}
                        </td>

                        <td>
                            @if($section['status'] === 'ready')
                                <span class="badge text-bg-success">
                                    Hazır
                                </span>
                            @elseif($section['status'] === 'blocked')
                                <span class="badge text-bg-danger">
                                    Bloke
                                </span>
                            @else
                                <span class="badge text-bg-warning">
                                    Veri Gerekli
                                </span>
                            @endif
                        </td>

                        <td>
                            @forelse($section['missing'] ?? [] as $missing)
                                <div>
                                    • {{ $missing }}
                                </div>
                            @empty
                                <span class="text-success">
                                    Otomatik üretilebilir.
                                </span>
                            @endforelse
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>



<div class="card mb-3">
    <div class="card-header bg-secondary text-white">
        <strong>SDS 16 Bölüm Taslak İçeriği</strong>
    </div>

    <div class="card-body">
        <div class="accordion"
             id="sdsSectionDraftAccordion">

            @foreach($sdsDraftSections as $section)
                <div class="accordion-item">
                    <h2 class="accordion-header"
                        id="draftHeading{{ $section['number'] }}">

                        <button class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#draftSection{{ $section['number'] }}">

                            <strong class="me-2">
                                {{ $section['number'] }}.
                                {{ $section['title'] }}
                            </strong>

                            @if($section['status'] === 'ready')
                                <span class="badge text-bg-success">
                                    Hazır
                                </span>
                            @elseif($section['status'] === 'blocked')
                                <span class="badge text-bg-danger">
                                    Bloke
                                </span>
                            @else
                                <span class="badge text-bg-warning">
                                    Veri Gerekli
                                </span>
                            @endif
                        </button>
                    </h2>

                    <div id="draftSection{{ $section['number'] }}"
                         class="accordion-collapse collapse"
                         data-bs-parent="#sdsSectionDraftAccordion">

                        <div class="accordion-body">

                            @if(!empty($section['missing']))
                                <div class="alert alert-warning">
                                    @foreach($section['missing'] as $missing)
                                        <div>• {{ $missing }}</div>
                                    @endforeach
                                </div>
                            @endif

                            <pre class="bg-light border rounded p-3 mb-0"
                                 style="white-space: pre-wrap; font-size: 13px;">{{ json_encode(
                                    $section['content'] ?? [],
                                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                                ) }}</pre>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</div>

    <div class="card mb-3">
        <div class="card-header">
            <strong>Formül Özeti</strong>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Formula ID</strong><br>
                    {{ $formulaInfo['id'] ?? '-' }}
                </div>

                <div class="col-md-3">
                    <strong>Formül</strong><br>
                    {{ $formulaInfo['name'] ?? '-' }}
                </div>

                <div class="col-md-3">
                    <strong>Yüzde Toplamı</strong><br>
                    %{{ number_format((float) ($formulaInfo['percentage_total'] ?? 0), 4, ',', '.') }}
                </div>

                <div class="col-md-3">
                    <strong>Profil</strong><br>
                    {{ $formulaInfo['regulation_profile'] ?? '-' }}
                </div>
            </div>
        </div>
    </div>

    @if(count($issues))
        <div class="card mb-3">
            <div class="card-header bg-warning">
                <strong>Kontrol Listesi</strong>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th style="width:110px;">Seviye</th>
                                <th style="width:260px;">Kod</th>
                                <th>Açıklama</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($issues as $issue)
                            <tr>
                                <td>
                                    @if(($issue['severity'] ?? '') === 'error')
                                        <span class="badge text-bg-danger">
                                            HATA
                                        </span>
                                    @else
                                        <span class="badge text-bg-warning">
                                            UYARI
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <code>{{ $issue['code'] ?? '-' }}</code>
                                </td>
                                <td>
                                    {{ $issue['message'] ?? '-' }}

                                    @if(!empty($issue['context']['raw_material_name']))
                                        <div class="small text-muted">
                                            Hammadde:
                                            {{ $issue['context']['raw_material_name'] }}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-header bg-dark text-white">
            <strong>Formül Hammaddeleri</strong>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Hammadde</th>
                            <th>Miktar</th>
                            <th>Formül %</th>
                            <th>SDS</th>
                            <th>Tedarikçi</th>
                            <th>Bileşen</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($rawMaterials as $row)
                        <tr>
                            <td>
                                <strong>
                                    {{ $row['raw_material_name'] ?? '-' }}
                                </strong>
                            </td>

                            <td>
                                {{ $row['formula_amount'] ?? '-' }}
                                {{ $row['formula_unit'] ?? '' }}
                            </td>

                            <td>
                                @if(($row['formula_percentage'] ?? null) !== null)
                                    %{{ number_format((float) $row['formula_percentage'], 6, ',', '.') }}
                                @else
                                    <span class="text-danger">Eksik</span>
                                @endif
                            </td>

                            <td>
                                @if(!empty($row['sds_profile_id']))
                                    <span class="badge text-bg-success">
                                        Var
                                    </span>
                                @else
                                    <span class="badge text-bg-danger">
                                        Yok
                                    </span>
                                @endif
                            </td>

                            <td>
                                {{ $row['sds_supplier_name'] ?? '-' }}
                            </td>

                            <td>
                                {{ $row['component_count'] ?? 0 }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6"
                                class="text-center text-muted py-4">
                                Hammadde kaydı bulunamadı.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-dark text-white">
            <strong>Bitmiş Üründeki Efektif Kimyasal Bileşenler</strong>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Hammadde</th>
                            <th>Kimyasal</th>
                            <th>CAS</th>
                            <th>EC</th>
                            <th>Hammadde %</th>
                            <th>SDS Bileşen %</th>
                            <th>Bitmiş Ürün %</th>
                            <th>Mevzuat</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($components as $row)
                        @php
                            $requestedRecords =
                                $row['regulatory']['requested_profile_records'] ?? [];
                        @endphp

                        <tr>
                            <td>
                                {{ $row['raw_material_name'] ?? '-' }}
                            </td>

                            <td>
                                <strong>
                                    {{ $row['official_preferred_name']
                                        ?? $row['chemical_name']
                                        ?? '-' }}
                                </strong>
                            </td>

                            <td>
                                {{ $row['official_cas_no']
                                    ?? $row['cas_no']
                                    ?? '-' }}
                            </td>

                            <td>
                                {{ $row['official_ec_no']
                                    ?? $row['ec_no']
                                    ?? '-' }}
                            </td>

                            <td>
                                @if(($row['formula_percentage'] ?? null) !== null)
                                    %{{ number_format((float) $row['formula_percentage'], 6, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                @if(($row['component_concentration_min'] ?? null) !== null)
                                    %{{ number_format((float) $row['component_concentration_min'], 6, ',', '.') }}
                                    -
                                    %{{ number_format((float) $row['component_concentration_max'], 6, ',', '.') }}
                                @else
                                    <span class="text-danger">Eksik</span>
                                @endif
                            </td>

                            <td>
                                @if(($row['effective_concentration_min'] ?? null) !== null)
                                    <strong>
                                        %{{ number_format((float) $row['effective_concentration_min'], 6, ',', '.') }}
                                        -
                                        %{{ number_format((float) $row['effective_concentration_max'], 6, ',', '.') }}
                                    </strong>
                                @else
                                    <span class="text-danger">
                                        Hesaplanamadı
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if(count($requestedRecords))
                                    <span class="badge text-bg-success">
                                        {{ count($requestedRecords) }} kayıt
                                    </span>
                                @elseif(!empty($row['regulatory_substance_id']))
                                    <span class="badge text-bg-warning">
                                        Profil kaydı yok
                                    </span>
                                @else
                                    <span class="badge text-bg-danger">
                                        Eşleşmemiş
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8"
                                class="text-center text-muted py-4">
                                Efektif bileşen bulunamadı.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
