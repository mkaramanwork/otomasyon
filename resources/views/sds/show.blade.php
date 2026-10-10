@extends('layouts.admin')



@section('title','Hammadde SDS Detay')



@section('content')



<div class="container-fluid">



    <div class="d-flex gap-2 mb-3 flex-wrap">
 @if($profile->is_current)
 <a class="btn btn-warning btn-sm" href="{{ route('sds.raw-materials.edit',[$rawMaterial->id,$profile->id]) }}">✏️ Güncel SDS Yönet</a>
 @endif
 <a class="btn btn-outline-secondary btn-sm" href="{{ route('sds.raw-materials.history',$rawMaterial->id) }}">🕘 Revizyon Geçmişi</a>
 <a class="btn btn-outline-primary btn-sm" href="{{ route('sds.formula-analysis.index') }}">🔄 Reçeteleri Yeniden Analiz Et</a>
</div>
<div class="d-flex justify-content-between align-items-center mb-3">



        <div>

            <h4 class="mb-0">

                🧪 SDS Detay

            </h4>



            <small class="text-muted">

                {{ $rawMaterial->name }}

            </small>

        </div>



        <div>

            <a href="{{ route('sds.raw-materials.index') }}"

               class="btn btn-outline-secondary">

                ← Listeye Dön

            </a>

        </div>



    </div>





    @if(session('success'))



        <div class="alert alert-success alert-dismissible fade show">



            {{ session('success') }}



            <button type="button"

                    class="btn-close"

                    data-bs-dismiss="alert">

            </button>



        </div>



    @endif





    @if(session('error'))



        <div class="alert alert-danger alert-dismissible fade show">



            {{ session('error') }}



            <button type="button"

                    class="btn-close"

                    data-bs-dismiss="alert">

            </button>



        </div>



    @endif





    @if($errors->any())



        <div class="alert alert-danger">



            <strong>

                İşlem gerçekleştirilemedi.

            </strong>



            <ul class="mb-0 mt-2">



                @foreach($errors->all() as $error)



                    <li>

                        {{ $error }}

                    </li>



                @endforeach



            </ul>



        </div>



    @endif





    <div class="card mb-3">



        <div class="card-header bg-dark text-white">



            <div class="d-flex justify-content-between align-items-center">



                <strong>

                    SDS Ana Kartı

                </strong>



                <div>



                    @if($profile->is_current)



                        <span class="badge text-bg-success">

                            Güncel SDS

                        </span>



                    @else



                        <span class="badge text-bg-secondary">

                            Eski SDS

                        </span>



                    @endif





                    @if($profile->is_verified)



                        <span class="badge text-bg-success">

                            ✓ Doğrulandı

                        </span>



                    @else



                        <span class="badge text-bg-warning">

                            ⚠ Kontrol Bekliyor

                        </span>



                    @endif



                </div>



            </div>



        </div>





        <div class="card-body">



            <div class="row">



                <div class="col-md-4 mb-3">



                    <label class="fw-bold">

                        Hammadde

                    </label>



                    <div>

                        {{ $rawMaterial->name }}

                    </div>



                </div>





                <div class="col-md-4 mb-3">



                    <label class="fw-bold">

                        Tedarikçi

                    </label>



                    <div>

                        {{ $profile->supplier_name ?? '-' }}

                    </div>



                </div>





                <div class="col-md-4 mb-3">



                    <label class="fw-bold">

                        Tedarikçi Ürün Adı

                    </label>



                    <div>

                        {{ $profile->supplier_product_name ?? '-' }}

                    </div>



                </div>





                <div class="col-md-3 mb-3">



                    <label class="fw-bold">

                        SDS Tarihi

                    </label>



                    <div>

                        {{ $profile->supplier_sds_date?->format('d.m.Y') ?? '-' }}

                    </div>



                </div>





                <div class="col-md-3 mb-3">



                    <label class="fw-bold">

                        Revizyon

                    </label>



                    <div>

                        {{ $profile->supplier_sds_revision ?? '-' }}

                    </div>



                </div>





                <div class="col-md-3 mb-3">



                    <label class="fw-bold">

                        Versiyon

                    </label>



                    <div>

                        {{ $profile->supplier_sds_version ?? '-' }}

                    </div>



                </div>





                <div class="col-md-3 mb-3">



                    <label class="fw-bold">

                        Dil

                    </label>



                    <div>

                        <span class="badge text-bg-dark">

                            {{ strtoupper($profile->sds_language ?? '-') }}

                        </span>

                    </div>



                </div>





                <div class="col-md-3 mb-3">



                    <label class="fw-bold">

                        Mevzuat Profili

                    </label>



                    <div>

                        {{ $profile->regulation_profile ?? '-' }}

                    </div>



                </div>





                <div class="col-md-3 mb-3">



                    <label class="fw-bold">

                        Madde / Karışım

                    </label>



                    <div>



                        @if($profile->substance_or_mixture === 'substance')



                            <span class="badge text-bg-primary">

                                Madde

                            </span>



                        @elseif($profile->substance_or_mixture === 'mixture')



                            <span class="badge text-bg-info">

                                Karışım

                            </span>



                        @else



                            -



                        @endif



                    </div>



                </div>





                <div class="col-md-3 mb-3">



                    <label class="fw-bold">

                        Kaynak

                    </label>



                    <div>

                        {{ $profile->source_name ?? '-' }}

                    </div>



                </div>





                <div class="col-md-3 mb-3">



                    <label class="fw-bold">

                        Kaynak Tarihi

                    </label>



                    <div>

                        {{ $profile->source_date?->format('d.m.Y') ?? '-' }}

                    </div>



                </div>





                <div class="col-md-6 mb-3">



                    <label class="fw-bold">

                        Tedarikçi SDS Dosyası

                    </label>



                    <div class="mt-1">



                        @if($profile->supplier_sds_file)



                            <a href="{{ asset('storage/' . $profile->supplier_sds_file) }}"

                               target="_blank"

                               class="btn btn-outline-primary btn-sm">



                                📄 SDS Dosyasını Aç



                            </a>



                        @else



                            <span class="text-muted">

                                Dosya yüklenmemiş.

                            </span>



                        @endif



                    </div>



                </div>



            </div>



        </div>



    </div>





    <div class="card mb-3">



        <div class="card-header bg-dark text-white">



            <div class="d-flex justify-content-between align-items-center">



                <div>



                    <strong>

                        Bölüm 3 - Bileşim / İçindekiler Hakkında Bilgi

                    </strong>



                    <div class="small text-white-50">

                        Tedarikçi SDS Bölüm 3'te bildirilen bileşenler

                    </div>



                </div>



                <div class="d-flex align-items-center gap-2">



                    <span class="badge text-bg-light">

                        {{ $profile->components->count() }} Bileşen

                    </span>



                    <button type="button"

                            class="btn btn-success btn-sm"

                            data-bs-toggle="collapse"

                            data-bs-target="#componentForm">



                        ➕ Yeni Bileşen Ekle



                    </button>



                </div>



            </div>



        </div>





        <div class="card-body p-0">



            <div class="table-responsive">



                <table class="table table-striped table-hover table-bordered mb-0">



                    <thead>



                        <tr>



                            <th style="min-width:220px;">

                                Kimyasal

                            </th>



                            <th style="min-width:200px;">

                                Kimlik

                            </th>



                            <th style="min-width:120px;">

                                Konsantrasyon

                            </th>



                            <th style="min-width:220px;">

                                Tedarikçi Sınıflandırması

                            </th>



                            <th style="min-width:160px;">

                                H Kodları

                            </th>



                            <th style="min-width:220px;">

                                Mevzuat

                            </th>



                            <th style="min-width:140px;">

                                İşlem

                            </th>



                        </tr>



                    </thead>





                    <tbody>



                        @forelse($profile->components as $component)



                            @php

                                $componentRegulatory =

                                    $component->regulatory_substance_id

                                    ? $regulatoryBySubstance->get(

                                        $component->regulatory_substance_id,

                                        collect()

                                    )

                                    : collect();



                                $hasAnnexVi =

                                    $componentRegulatory->contains(

                                        fn($item) => (bool) $item->annex_vi

                                    );



                                $hasSvhc =

                                    $componentRegulatory->contains(

                                        fn($item) => (bool) $item->svhc

                                    );



                                $hasAnnexXiv =

                                    $componentRegulatory->contains(

                                        fn($item) => (bool) $item->annex_xiv

                                    );



                                $hasAnnexXvii =

                                    $componentRegulatory->contains(

                                        fn($item) => (bool) $item->annex_xvii

                                    );



                                $hasKkdik =

                                    $componentRegulatory->contains(

                                        fn($item) =>

                                            $item->regulation_profile === 'TR_KKDIK_SEA'

                                    );

                            
                                $annexViRecords =
                                    $componentRegulatory->filter(
                                        fn($item) =>
                                            $item->source_type === 'ECHA_ANNEX_VI'
                                    );

                                $annexViNotes = $annexViRecords
                                    ->map(function ($item) {
                                        $notes = $item->regulatory_notes;

                                        if (is_string($notes)) {
                                            $decoded = json_decode($notes, true);

                                            $notes = is_array($decoded)
                                                ? $decoded
                                                : [];
                                        }

                                        return is_array($notes)
                                            ? ($notes['notes'] ?? null)
                                            : null;
                                    })
                                    ->filter()
                                    ->implode(' ');

                                $hasNoteL =
                                    preg_match(
                                        '/(?:^|[^A-Z])(?:NOTE\\s+)?L(?:[^A-Z]|$)/i',
                                        $annexViNotes
                                    ) === 1;

                                $evidence =
                                    is_array($component->regulatory_notes)
                                        ? $component->regulatory_notes
                                        : [];

                                $ip346Tested =
                                    (bool) ($evidence['ip346_tested'] ?? false);

                                $ip346BelowThree =
                                    (bool) (
                                        $evidence['ip346_dmso_below_3_confirmed']
                                        ?? false
                                    );

                                $ip346Value =
                                    $evidence['ip346_dmso_extract_percent']
                                    ?? null;

                                $noteLConditionMet =
                                    $ip346BelowThree ||
                                    (
                                        $ip346Tested &&
                                        $ip346Value !== null &&
                                        is_numeric($ip346Value) &&
                                        (float) $ip346Value < 3
                                    );

@endphp



                            <tr>



                                <td>



                                    <strong>

                                        {{ $component->chemical_name }}

                                    </strong>



                                    @if($component->supplier_note)



                                        <div class="small text-muted mt-1">

                                            {{ $component->supplier_note }}

                                        </div>



                                    @endif



                                    @if($component->source_page)



                                        <div class="small text-muted mt-1">

                                            SDS Bölüm 3 · Sayfa {{ $component->source_page }}

                                        </div>



                                    @endif



                                </td>





                                <td>



                                    <div class="small">

                                        <strong>CAS:</strong>

                                        {{ $component->cas_no ?? '-' }}

                                    </div>



                                    <div class="small">

                                        <strong>EC:</strong>

                                        {{ $component->ec_no ?? '-' }}

                                    </div>



                                    <div class="small">

                                        <strong>Index:</strong>

                                        {{ $component->index_no ?? '-' }}

                                    </div>



                                </td>





                                <td>



                                    @if(

                                        $component->concentration_min !== null &&

                                        $component->concentration_max !== null

                                    )



                                        {{ rtrim(rtrim(number_format($component->concentration_min, 6, '.', ''), '0'), '.') }}

                                        -

                                        {{ rtrim(rtrim(number_format($component->concentration_max, 6, '.', ''), '0'), '.') }}

                                        %



                                    @elseif($component->concentration_min !== null)



                                        ≥

                                        {{ rtrim(rtrim(number_format($component->concentration_min, 6, '.', ''), '0'), '.') }}

                                        %



                                    @elseif($component->concentration_max !== null)



                                        ≤

                                        {{ rtrim(rtrim(number_format($component->concentration_max, 6, '.', ''), '0'), '.') }}

                                        %



                                    @else



                                        -



                                    @endif



                                </td>





                                <td>



                                    @if(

                                        is_array($component->hazard_classifications) &&

                                        count($component->hazard_classifications)

                                    )



                                        @foreach($component->hazard_classifications as $classification)



                                            <span class="badge text-bg-secondary mb-1">

                                                {{ $classification }}

                                            </span>



                                        @endforeach



                                    @else



                                        -



                                    @endif



                                </td>





                                <td>



                                    @if(

                                        is_array($component->h_statements) &&

                                        count($component->h_statements)

                                    )



                                        @foreach($component->h_statements as $statement)



                                            <span class="badge text-bg-warning mb-1">

                                                {{ $statement }}

                                            </span>



                                        @endforeach



                                    @else



                                        -



                                    @endif



                                </td>





                                <td>



                                    @if($component->regulatory_substance_id)



                                        <div class="mb-2">



                                            <span class="badge text-bg-success">

                                                ✓ Resmî Kayıt Eşleşti

                                            </span>



                                        </div>



                                        @if($component->regulatorySubstance)



                                            <div class="small fw-bold mb-1">

                                                {{ $component->regulatorySubstance->preferred_name }}

                                            </div>



                                        @endif



                                        <div>



                                            @if($hasAnnexVi)

                                                <span class="badge text-bg-success mb-1">

                                                    Annex VI

                                                </span>

                                            @endif

                                            @if($hasNoteL)
                                                <span class="badge text-bg-dark mb-1">
                                                    Note L
                                                </span>

                                                @if($noteLConditionMet)
                                                    <span class="badge text-bg-success mb-1">
                                                        IP346 / DMSO &lt; %3 ✓
                                                    </span>
                                                @else
                                                    <span class="badge text-bg-warning mb-1">
                                                        IP346 Kanıt Gerekli
                                                    </span>
                                                @endif
                                            @endif



                                            @if($hasSvhc)

                                                <span class="badge text-bg-warning mb-1">

                                                    SVHC

                                                </span>

                                            @endif



                                            @if($hasAnnexXiv)

                                                <span class="badge text-bg-danger mb-1">

                                                    Annex XIV

                                                </span>

                                            @endif



                                            @if($hasAnnexXvii)

                                                <span class="badge text-bg-dark mb-1">

                                                    Annex XVII

                                                </span>

                                            @endif



                                            @if($hasKkdik)

                                                <span class="badge text-bg-primary mb-1">

                                                    TR KKDİK / SEA

                                                </span>

                                            @endif



                                        </div>



                                    @else



                                        <span class="badge text-bg-warning">

                                            Eşleşme Bekliyor

                                        </span>



                                    @endif



                                </td>





                                <td>



                                    @if($component->regulatory_substance_id)



                                        <button type="button"

                                                class="btn btn-outline-primary btn-sm mb-2"

                                                data-bs-toggle="collapse"

                                                data-bs-target="#kkdikForm{{ $component->id }}">



                                            + KKDİK / SEA



                                        </button>

                                        @if($hasNoteL)
                                            <button type="button"
                                                    class="btn btn-outline-dark btn-sm mb-2"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#annexEvidenceForm{{ $component->id }}">
                                                + Note L / IP346
                                            </button>
                                        @endif





                                        <form method="POST"

                                              action="{{ route(

                                                  'sds.raw-materials.components.regulatory.detach',

                                                  [

                                                      $rawMaterial->id,

                                                      $profile->id,

                                                      $component->id

                                                  ]

                                              ) }}">



                                            @csrf

                                            @method('DELETE')



                                            <button type="submit"

                                                    class="btn btn-outline-danger btn-sm">



                                                Eşleşmeyi Kaldır



                                            </button>



                                        </form>



                                    @else



                                        <span class="small text-muted">

                                            Bileşen eklenirken mevzuat kaydı seçilebilir.

                                        </span>



                                    @endif



                                </td>



                            </tr>
                            @if(
                                $component->regulatory_substance_id &&
                                $hasNoteL
                            )
                                <tr class="collapse"
                                    id="annexEvidenceForm{{ $component->id }}">
                                    <td colspan="7">
                                        <div class="p-3 bg-light border rounded">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <h6 class="fw-bold mb-1">
                                                        Annex VI Note L / IP 346 Kanıtı
                                                    </h6>
                                                    <div class="small text-muted">
                                                        Kanıt bu hammadde SDS bileşenine özeldir. Aynı CAS farklı tedarikçide farklı kanıta sahip olabilir.
                                                    </div>
                                                </div>
                                                @if($noteLConditionMet)
                                                    <span class="badge text-bg-success">Koşul Sağlandı</span>
                                                @else
                                                    <span class="badge text-bg-warning">Kanıt Gerekli</span>
                                                @endif
                                            </div>

                                            <form method="POST"
                                                  action="{{ route(
                                                      'sds.raw-materials.components.annex-evidence.store',
                                                      [
                                                          $rawMaterial->id,
                                                          $profile->id,
                                                          $component->id
                                                      ]
                                                  ) }}">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-2 mb-3">
                                                        <label class="form-label fw-bold">IP 346 Testi</label>
                                                        <input type="hidden" name="ip346_tested" value="0">
                                                        <div class="form-check mt-2">
                                                            <input type="checkbox" name="ip346_tested" value="1"
                                                                   class="form-check-input"
                                                                   id="ip346Tested{{ $component->id }}"
                                                                   @checked($ip346Tested)>
                                                            <label class="form-check-label" for="ip346Tested{{ $component->id }}">
                                                                Kanıt mevcut
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label fw-bold">DMSO &lt; %3 teyidi</label>
                                                        <input type="hidden" name="ip346_dmso_below_3_confirmed" value="0">
                                                        <div class="form-check mt-2">
                                                            <input type="checkbox" name="ip346_dmso_below_3_confirmed" value="1"
                                                                   class="form-check-input"
                                                                   id="ip346Below3{{ $component->id }}"
                                                                   @checked($ip346BelowThree)>
                                                            <label class="form-check-label" for="ip346Below3{{ $component->id }}">
                                                                Kaynak açıkça &lt; %3 diyor
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-2 mb-3">
                                                        <label class="form-label fw-bold">DMSO %</label>
                                                        <input type="number" name="ip346_dmso_extract_percent"
                                                               class="form-control" min="0" max="100" step="0.0001"
                                                               value="{{ $ip346Value }}">
                                                        <div class="form-text">Kesin değer varsa gir.</div>
                                                    </div>

                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label fw-bold">Kanıt Kaynağı</label>
                                                        <input type="text" name="evidence_source" class="form-control"
                                                               value="{{ $evidence['evidence_source'] ?? '' }}"
                                                               placeholder="Tedarikçi SDS / COA / Laboratuvar">
                                                    </div>

                                                    <div class="col-md-2 mb-3">
                                                        <label class="form-label fw-bold">Kanıt Tarihi</label>
                                                        <input type="date" name="evidence_date" class="form-control"
                                                               value="{{ $evidence['evidence_date'] ?? '' }}">
                                                    </div>

                                                    <div class="col-md-5 mb-3">
                                                        <label class="form-label fw-bold">Referans</label>
                                                        <input type="text" name="evidence_reference" class="form-control"
                                                               value="{{ $evidence['evidence_reference'] ?? '' }}"
                                                               placeholder="SDS revizyonu / rapor no / sayfa">
                                                    </div>

                                                    <div class="col-md-7 mb-3">
                                                        <label class="form-label fw-bold">Açıklama</label>
                                                        <input type="text" name="evidence_note" class="form-control"
                                                               value="{{ $evidence['evidence_note'] ?? '' }}"
                                                               placeholder="Örn: DMSO extract is less than 3% according to IP346.">
                                                    </div>
                                                </div>

                                                <button type="submit" class="btn btn-dark">
                                                    Note L / IP346 Kanıtını Kaydet
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endif







                            @if($component->regulatory_substance_id)



                                <tr class="collapse"

                                    id="kkdikForm{{ $component->id }}">



                                    <td colspan="7">



                                        <div class="p-3 bg-light">



                                            <h6 class="fw-bold">

                                                🇹🇷 KKDİK / SEA Verisi

                                            </h6>



                                            <form method="POST"

                                                  action="{{ route(

                                                      'sds.raw-materials.components.kkdik.store',

                                                      [

                                                          $rawMaterial->id,

                                                          $profile->id,

                                                          $component->id

                                                      ]

                                                  ) }}">



                                                @csrf



                                                <div class="row">



                                                    <div class="col-md-3 mb-3">



                                                        <label class="form-label fw-bold">

                                                            KKDİK Kayıt No

                                                        </label>



                                                        <input type="text"

                                                               name="kkdik_registration_no"

                                                               class="form-control">



                                                    </div>





                                                    <div class="col-md-4 mb-3">



                                                        <label class="form-label fw-bold">

                                                            SEA Sınıflandırması

                                                        </label>



                                                        <input type="text"

                                                               name="tr_hazard_classifications_text"

                                                               class="form-control"

                                                               placeholder="Örn: Carc. 1B, Asp. Tox. 1">



                                                    </div>





                                                    <div class="col-md-5 mb-3">



                                                        <label class="form-label fw-bold">

                                                            H Kodları

                                                        </label>



                                                        <input type="text"

                                                               name="tr_h_statements_text"

                                                               class="form-control"

                                                               placeholder="Örn: H350, H304">



                                                    </div>





                                                    <div class="col-md-4 mb-3">



                                                        <label class="form-label fw-bold">

                                                            Kaynak

                                                        </label>



                                                        <input type="text"

                                                               name="tr_source_name"

                                                               class="form-control"

                                                               value="KKDİK / SEA"

                                                               required>



                                                    </div>





                                                    <div class="col-md-3 mb-3">



                                                        <label class="form-label fw-bold">

                                                            Kaynak Tarihi

                                                        </label>



                                                        <input type="date"

                                                               name="tr_source_date"

                                                               class="form-control">



                                                    </div>





                                                    <div class="col-md-5 mb-3">



                                                        <label class="form-label fw-bold">

                                                            Not

                                                        </label>



                                                        <input type="text"

                                                               name="tr_regulatory_note"

                                                               class="form-control"

                                                               placeholder="Varsa açıklama">



                                                    </div>



                                                </div>





                                                <button type="submit"

                                                        class="btn btn-primary">



                                                    🇹🇷 KKDİK / SEA Verisini Kaydet



                                                </button>



                                            </form>



                                        </div>



                                    </td>



                                </tr>



                            @endif



                        @empty



                            <tr>



                                <td colspan="7"

                                    class="text-center py-5">



                                    <div class="text-muted mb-3">

                                        Henüz Bölüm 3 bileşeni girilmemiş.

                                    </div>



                                    <button type="button"

                                            class="btn btn-success"

                                            data-bs-toggle="collapse"

                                            data-bs-target="#componentForm">



                                        ➕ İlk Bileşeni Ekle



                                    </button>



                                </td>



                            </tr>



                        @endforelse



                    </tbody>



                </table>



            </div>



        </div>





        @if($profile->components->count() > 0)



            <div class="card-footer">



                <button type="button"

                        class="btn btn-success btn-sm"

                        data-bs-toggle="collapse"

                        data-bs-target="#componentForm">



                    ➕ Yeni Bileşen Ekle



                </button>



            </div>



        @endif



    </div>





    <div class="collapse {{ $errors->any() ? 'show' : '' }}"

         id="componentForm">



        <div class="card mb-3">



            <div class="card-header">



                <div class="d-flex justify-content-between align-items-center">



                    <strong>

                        ➕ Yeni Bölüm 3 Bileşeni

                    </strong>



                    <button type="button"

                            class="btn btn-outline-secondary btn-sm"

                            data-bs-toggle="collapse"

                            data-bs-target="#componentForm">



                        Kapat



                    </button>



                </div>



            </div>





            <div class="card-body">



                <div class="alert alert-info">



                    Tedarikçi SDS'nin Bölüm 3 kısmında bildirilen her kimyasalı

                    ayrı bir kayıt olarak ekleyebilirsiniz.



                    Kimyasal adı, CAS, EC veya Index bilgisi girildiğinde

                    mevzuat veritabanında arama yapılacaktır.



                </div>





                <form method="POST"

                      action="{{ route(

                          'sds.raw-materials.components.store',

                          [

                              $rawMaterial->id,

                              $profile->id

                          ]

                      ) }}">



                    @csrf



                    <input type="hidden"

                           name="regulatory_substance_id"

                           id="regulatorySubstanceId"

                           value="{{ old('regulatory_substance_id') }}">





                    <div class="row">



                        <div class="col-md-4 mb-3">



                            <label class="form-label fw-bold">

                                Kimyasal Adı *

                            </label>



                            <input type="text"

                                   id="chemicalName"

                                   name="chemical_name"

                                   class="form-control @error('chemical_name') is-invalid @enderror"

                                   value="{{ old('chemical_name') }}"

                                   required>



                            @error('chemical_name')



                                <div class="invalid-feedback">

                                    {{ $message }}

                                </div>



                            @enderror



                        </div>





                        <div class="col-md-2 mb-3">



                            <label class="form-label fw-bold">

                                CAS No

                            </label>



                            <input type="text"

                                   id="casNo"

                                   name="cas_no"

                                   class="form-control @error('cas_no') is-invalid @enderror"

                                   value="{{ old('cas_no') }}"

                                   placeholder="Örn: 64742-54-7">



                        </div>





                        <div class="col-md-2 mb-3">



                            <label class="form-label fw-bold">

                                EC No

                            </label>



                            <input type="text"

                                   id="ecNo"

                                   name="ec_no"

                                   class="form-control @error('ec_no') is-invalid @enderror"

                                   value="{{ old('ec_no') }}">



                        </div>





                        <div class="col-md-2 mb-3">



                            <label class="form-label fw-bold">

                                Index No

                            </label>



                            <input type="text"

                                   id="indexNo"

                                   name="index_no"

                                   class="form-control @error('index_no') is-invalid @enderror"

                                   value="{{ old('index_no') }}">



                        </div>





                        <div class="col-md-1 mb-3">



                            <label class="form-label fw-bold">

                                Min %

                            </label>



                            <input type="number"

                                   step="0.000001"

                                   min="0"

                                   max="100"

                                   name="concentration_min"

                                   class="form-control"

                                   value="{{ old('concentration_min') }}">



                        </div>





                        <div class="col-md-1 mb-3">



                            <label class="form-label fw-bold">

                                Max %

                            </label>



                            <input type="number"

                                   step="0.000001"

                                   min="0"

                                   max="100"

                                   name="concentration_max"

                                   class="form-control"

                                   value="{{ old('concentration_max') }}">



                        </div>

                                                <div class="col-12 mb-3">



                            <div class="card border-primary">



                                <div class="card-header py-2">



                                    <strong>

                                        🔎 Resmî Mevzuat Araması

                                    </strong>



                                </div>



                                <div class="card-body">



                                    <div class="small text-muted mb-2">



                                        Kimyasal adı, CAS, EC veya Index bilgisi

                                        yazıldığında mevzuat veritabanında otomatik

                                        arama yapılır.



                                        <strong>

                                            Sonuç bulunsa bile siz seçmeden eşleştirme yapılmaz.

                                        </strong>



                                    </div>



                                    <div id="regulatorySearchStatus"

                                         class="small text-muted">



                                        Arama için kimyasal bilgisi giriniz.



                                    </div>



                                    <div id="regulatorySearchResults"

                                         class="mt-2">

                                    </div>



                                    <div id="selectedRegulatoryResult"

                                         class="mt-2">

                                    </div>



                                </div>



                            </div>



                        </div>





                        <div class="col-md-6 mb-3">



                            <label class="form-label fw-bold">

                                Tehlike Sınıflandırmaları

                            </label>



                            <input type="text"

                                   name="hazard_classifications_text"

                                   class="form-control"

                                   value="{{ old('hazard_classifications_text') }}"

                                   placeholder="Örn: Skin Irrit. 2, Eye Dam. 1, Aquatic Chronic 2">



                            <small class="text-muted">

                                Tedarikçi SDS'de yazan sınıflandırmaları

                                virgül veya noktalı virgül ile ayırabilirsiniz.

                            </small>



                        </div>





                        <div class="col-md-6 mb-3">



                            <label class="form-label fw-bold">

                                H Kodları

                            </label>



                            <input type="text"

                                   name="h_statements_text"

                                   class="form-control"

                                   value="{{ old('h_statements_text') }}"

                                   placeholder="Örn: H315, H318, H411">



                            <small class="text-muted">

                                Tedarikçi SDS'de yazan H kodlarını

                                virgül veya noktalı virgül ile ayırabilirsiniz.

                            </small>



                        </div>





                        <div class="col-md-2 mb-3">



                            <label class="form-label fw-bold">

                                SDS Sayfası

                            </label>



                            <input type="number"

                                   name="source_page"

                                   min="1"

                                   class="form-control"

                                   value="{{ old('source_page') }}">



                        </div>





                        <div class="col-md-10 mb-3">



                            <label class="form-label fw-bold">

                                Tedarikçi Notu

                            </label>



                            <input type="text"

                                   name="supplier_note"

                                   class="form-control"

                                   value="{{ old('supplier_note') }}"

                                   placeholder="Bölüm 3'te yer alan ek açıklama varsa yazılabilir.">



                        </div>



                    </div>





                    <div class="d-flex justify-content-between align-items-center">



                        <button type="button"

                                class="btn btn-outline-secondary"

                                data-bs-toggle="collapse"

                                data-bs-target="#componentForm">



                            İptal



                        </button>





                        <button type="submit"

                                class="btn btn-success">



                            💾 Bileşeni Kaydet



                        </button>



                    </div>



                </form>



            </div>



        </div>



    </div>



</div>





<script>

document.addEventListener('DOMContentLoaded', function () {



    const chemicalName =

        document.getElementById('chemicalName');



    const casNo =

        document.getElementById('casNo');



    const ecNo =

        document.getElementById('ecNo');



    const indexNo =

        document.getElementById('indexNo');



    const selectedId =

        document.getElementById('regulatorySubstanceId');



    const statusBox =

        document.getElementById('regulatorySearchStatus');



    const resultsBox =

        document.getElementById('regulatorySearchResults');



    const selectedBox =

        document.getElementById('selectedRegulatoryResult');



    if (

        !chemicalName ||

        !casNo ||

        !ecNo ||

        !indexNo ||

        !selectedId ||

        !statusBox ||

        !resultsBox ||

        !selectedBox

    ) {

        return;

    }



    let timer = null;

    let lastResults = [];





    function escapeHtml(value)

    {

        const div =

            document.createElement('div');



        div.textContent =

            value ?? '';



        return div.innerHTML;

    }





    function badge(label, active, cssClass)

    {

        if (!active) {

            return '';

        }



        return `

            <span class="badge ${cssClass} me-1 mb-1">

                ${escapeHtml(label)}

            </span>

        `;

    }





    function clearSelected()

    {

        selectedId.value = '';

        selectedBox.innerHTML = '';

    }





    function renderSelected(item)

    {

        selectedId.value =

            item.id;

        // Keep the regulatory identity and the editable component fields in sync.
        chemicalName.value = item.preferred_name || '';
        casNo.value = item.cas_no || '';
        ecNo.value = item.ec_no || '';
        indexNo.value = item.index_no || '';

        // Use the classification from the selected regulatory record.
        // Keep supplier data editable; do not invent missing hazard data.
        const hazardClassesField = document.querySelector('[name="hazard_classifications_text"]');
        const hStatementsField = document.querySelector('[name="h_statements_text"]');
        const normalizeItems = (value) => Array.isArray(value)
            ? value.filter(Boolean).map(v => String(v).trim()).filter(Boolean)
            : (typeof value === 'string' ? value.split(/[,;]+/).map(v => v.trim()).filter(Boolean) : []);
        if (hazardClassesField) {
            hazardClassesField.value = normalizeItems(item.hazard_classifications).join(', ');
            hazardClassesField.dispatchEvent(new Event('change', { bubbles: true }));
        }
        if (hStatementsField) {
            hStatementsField.value = normalizeItems(item.hazard_statements).join(', ');
            hStatementsField.dispatchEvent(new Event('change', { bubbles: true }));
        }



        selectedBox.innerHTML = `

            <div class="alert alert-success mb-0">



                <div class="fw-bold">

                    ✓ Seçilen resmî madde

                </div>



                <div class="mt-1">

                    ${escapeHtml(item.preferred_name || '-')}

                </div>



                <div class="small mt-1">



                    <strong>CAS:</strong>

                    ${escapeHtml(item.cas_no || '-')}



                    &nbsp; | &nbsp;



                    <strong>EC:</strong>

                    ${escapeHtml(item.ec_no || '-')}



                    &nbsp; | &nbsp;



                    <strong>Index:</strong>

                    ${escapeHtml(item.index_no || '-')}



                </div>



                <div class="mt-2">



                    ${badge(

                        'Annex VI',

                        item.annex_vi,

                        'text-bg-success'

                    )}



                    ${badge(

                        'SVHC',

                        item.svhc,

                        'text-bg-warning'

                    )}



                    ${badge(

                        'Annex XIV',

                        item.annex_xiv,

                        'text-bg-danger'

                    )}



                    ${badge(

                        'Annex XVII',

                        item.annex_xvii,

                        'text-bg-dark'

                    )}



                    ${badge(

                        'TR KKDİK / SEA',

                        item.tr_kkdik_sea,

                        'text-bg-primary'

                    )}



                </div>



                <button type="button"

                        id="removeSelectedRegulatory"

                        class="btn btn-outline-danger btn-sm mt-2">



                    Seçimi Kaldır



                </button>



            </div>

        `;

    }





    function searchRegulatory()

    {

        clearTimeout(timer);



        timer = setTimeout(async function () {



            const name =

                chemicalName.value.trim();



            const cas =

                casNo.value.trim();



            const ec =

                ecNo.value.trim();



            const index =

                indexNo.value.trim();



            if (

                !cas &&

                !ec &&

                !index &&

                name.length < 2

            ) {

                resultsBox.innerHTML = '';



                statusBox.innerHTML =

                    'Arama için kimyasal bilgisi giriniz.';



                return;

            }





            statusBox.innerHTML =

                'Mevzuat veritabanı aranıyor...';



            resultsBox.innerHTML = '';





            const params =

                new URLSearchParams();



            params.set(

                'name',

                name

            );



            params.set(

                'cas',

                cas

            );



            params.set(

                'ec',

                ec

            );



            params.set(

                'index',

                index

            );





            try {



                const response = await fetch(

                    "{{ route('sds.regulatory.search') }}"

                    + '?'

                    + params.toString(),

                    {

                        headers: {

                            'Accept':

                                'application/json'

                        }

                    }

                );





                if (!response.ok) {

                    throw new Error(

                        'Arama işlemi başarısız.'

                    );

                }





                lastResults =

                    await response.json();





                if (!lastResults.length) {



                    statusBox.innerHTML = `

                        <span class="text-warning">

                            Mevzuat kaydı bulunamadı.

                        </span>

                    `;



                    resultsBox.innerHTML = '';



                    return;

                }





                statusBox.innerHTML = `

                    <span class="text-success">

                        ${lastResults.length}

                        aday kayıt bulundu.

                    </span>

                `;





                resultsBox.innerHTML =

                    lastResults

                    .map(function (item, position) {



                        return `

                            <div class="border rounded p-3 mb-2">



                                <div class="d-flex

                                            justify-content-between

                                            align-items-start

                                            gap-3">



                                    <div>



                                        <div class="fw-bold">

                                            ${escapeHtml(

                                                item.preferred_name || '-'

                                            )}

                                        </div>





                                        <div class="small text-muted mt-1">



                                            <strong>CAS:</strong>

                                            ${escapeHtml(

                                                item.cas_no || '-'

                                            )}



                                            &nbsp; | &nbsp;



                                            <strong>EC:</strong>

                                            ${escapeHtml(

                                                item.ec_no || '-'

                                            )}



                                            &nbsp; | &nbsp;



                                            <strong>Index:</strong>

                                            ${escapeHtml(

                                                item.index_no || '-'

                                            )}



                                        </div>





                                        <div class="mt-2">



                                            ${badge(

                                                'Annex VI',

                                                item.annex_vi,

                                                'text-bg-success'

                                            )}



                                            ${badge(

                                                'SVHC',

                                                item.svhc,

                                                'text-bg-warning'

                                            )}



                                            ${badge(

                                                'Annex XIV',

                                                item.annex_xiv,

                                                'text-bg-danger'

                                            )}



                                            ${badge(

                                                'Annex XVII',

                                                item.annex_xvii,

                                                'text-bg-dark'

                                            )}



                                            ${badge(

                                                'TR KKDİK / SEA',

                                                item.tr_kkdik_sea,

                                                'text-bg-primary'

                                            )}



                                        </div>



                                    </div>





                                    <div>



                                        <button type="button"

                                                class="btn btn-primary btn-sm regulatory-select"

                                                data-index="${position}">



                                            Bu Maddeyle Eşleştir



                                        </button>



                                    </div>



                                </div>



                            </div>

                        `;



                    })

                    .join('');



            } catch (error) {



                statusBox.innerHTML = `

                    <span class="text-danger">

                        Mevzuat araması yapılamadı.

                    </span>

                `;



                resultsBox.innerHTML = '';



            }



        }, 400);

    }





    [

        chemicalName,

        casNo,

        ecNo,

        indexNo

    ].forEach(function (input) {



        input.addEventListener(

            'input',

            function () {



                clearSelected();

                searchRegulatory();



            }

        );



    });





    resultsBox.addEventListener(

        'click',

        function (event) {



            const button =

                event.target.closest(

                    '.regulatory-select'

                );



            if (!button) {

                return;

            }





            const position =

                parseInt(

                    button.dataset.index,

                    10

                );





            const item =

                lastResults[position];





            if (!item) {

                return;

            }





            renderSelected(item);



            resultsBox.innerHTML = '';



            statusBox.innerHTML = `

                <span class="text-success">

                    Resmî madde seçildi.

                    Bileşeni kaydettiğinizde bağlantı kurulacaktır.

                </span>

            `;



        }

    );





    selectedBox.addEventListener(

        'click',

        function (event) {



            if (

                event.target.id ===

                'removeSelectedRegulatory'

            ) {

                clearSelected();

                searchRegulatory();

            }



        }

    );





    @if(old('regulatory_substance_id'))



        searchRegulatory();



    @endif



});

</script>




{{-- Only expand an existing, authorized Note L evidence form when opened from formula analysis. --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);
    const id = params.get('evidence_component');
    if (!id || !/^\d+$/.test(id)) return;
    const target = document.getElementById('annexEvidenceForm' + id);
    if (!target) return;
    if (window.bootstrap && window.bootstrap.Collapse) {
        window.bootstrap.Collapse.getOrCreateInstance(target, {toggle:false}).show();
    } else {
        target.classList.add('show');
    }
    window.setTimeout(function () {
        target.scrollIntoView({behavior:'smooth', block:'center'});
    }, 120);
});
</script>

@endsection