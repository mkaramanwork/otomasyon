@extends('layouts.admin')

@section('title', 'SDS Hızlı Veri Girişi')

@section('content')

@php
    $pp = $profile->physical_properties ?? [];

    $fa = $profile->first_aid ?? [];
    $ff = $profile->fire_fighting ?? [];
    $ar = $profile->accidental_release ?? [];
    $hs = $profile->handling_storage ?? [];
    $ec = $profile->exposure_controls ?? [];
    $sr = $profile->stability_reactivity ?? [];
    $tx = $profile->toxicology ?? [];
    $eco = $profile->ecology ?? [];
    $dp = $profile->disposal ?? [];
    $tr = $profile->transport ?? [];
    $rg = $profile->regulatory ?? [];
    $rv = $profile->revision ?? [];

    $hasOverride =
        count($fa) ||
        count($ff) ||
        count($ar) ||
        count($hs) ||
        count($ec) ||
        count($sr) ||
        count($tx) ||
        count($eco) ||
        count($dp) ||
        count($tr) ||
        count($rg) ||
        count($rv);
@endphp

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h3 class="mb-1">
                ⚡ SDS Hızlı Veri Girişi
            </h3>

            <div class="text-muted">
                {{ $formula->name }}
                / Formula ID: {{ $formula->id }}
            </div>
        </div>

        <a href="{{ route(
                'sds.formula-analysis.show',
                [
                    'formula' => $formula->id,
                    'regulation_profile' => $regulationProfile
                ]
            ) }}"
           class="btn btn-outline-secondary">
            ← Analize Dön
        </a>
    </div>

    <div class="alert alert-info">
        <strong>Otomatik SDS:</strong>
        Yalnızca 7 fiziksel ürün değerini gir.
        Tehlike sınıfları, H kodları, piktogramlar, signal word ve diğer SDS bölümleri
        formül + hammadde SDS + mevzuat veritabanı + sistem kurallarından otomatik hazırlanır.
        Özel bir ürün durumu varsa <strong>Özel Durum / Override</strong> alanını aç.
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Kontrol et:</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route(
              'sds.formula-analysis.profile.save',
              $formula->id
          ) }}">

        @csrf
        @method('PUT')

        <input type="hidden"
               name="regulation_profile"
               value="{{ $regulationProfile }}">

        <div class="card mb-3"><div class="card-header bg-dark text-white"><strong>SDS Firma / Ürün Ailesi / Şablon / Hazırlayan</strong></div>
          <div class="card-body"><div class="row g-3">
            @foreach([
              ['sds_company_id','Şirket',$companies],
              ['sds_product_family_id','Ürün Ailesi',$families],
              ['sds_template_id','Şablon',$templates],
              ['sds_preparer_id','GBF Hazırlayan (isteğe bağlı)',$preparers],
            ] as $opt)
              <div class="col-md-3"><label class="form-label fw-bold">{{ $opt[1] }}</label>
                <select class="form-select" name="{{ $opt[0] }}" id="{{ $opt[0] }}" {{ $opt[0] !== 'sds_preparer_id' ? 'required' : '' }}>
                   <option value="">Seçiniz</option>
                   @foreach($opt[2] as $entry)
                      <option value="{{ $entry->id }}" data-company="{{ $entry->company_id ?? '' }}" @selected((string)old($opt[0],$profile->{$opt[0]})===(string)$entry->id)>{{ $entry->name }}</option>
                   @endforeach
                </select>
              </div>
            @endforeach
          </div><div class="mt-3 small text-muted">Şirket ve aile/şablon kayıtları: <a href="{{ route('sds.management.index','companies') }}">SDS Yönetimi</a>. Şablon firmaya göre filtrelenir.</div></div></div>
        <script>(function(){const c=document.getElementById('sds_company_id'),t=document.getElementById('sds_template_id');function sync(){if(!c||!t)return;for(const opt of t.options){if(!opt.value)continue;const visible=opt.dataset.company===c.value;opt.hidden=!visible;opt.disabled=!visible;}if(t.selectedOptions[0]&&t.selectedOptions[0].disabled)t.value='';}c?.addEventListener('change',sync);sync();})();</script>
        <div class="card mb-3">
            <div class="card-header bg-dark text-white">
                <strong>Zorunlu 7 Fiziksel Değer</strong>
            </div>

            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Fiziksel Hal</label>
                        <select name="physical_properties[physical_state]"
                                class="form-select"
                                required>
                            @foreach([
                                'Liquid' => 'Sıvı / Liquid',
                                'Solid' => 'Katı / Solid',
                                'Semi-solid' => 'Yarı Katı / Semi-solid',
                            ] as $value => $label)
                                <option value="{{ $value }}"
                                    @selected(old(
                                        'physical_properties.physical_state',
                                        $pp['physical_state'] ?? 'Liquid'
                                    ) === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Renk / Görünüş</label>
                        <input type="text"
                               name="physical_properties[colour]"
                               class="form-control"
                               required
                               value="{{ old('physical_properties.colour', $pp['colour'] ?? '') }}"
                               placeholder="Örn: Amber / Clear">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Yoğunluk</label>
                        <input type="text"
                               name="physical_properties[density]"
                               class="form-control"
                               required
                               value="{{ old('physical_properties.density', $pp['density'] ?? '') }}"
                               placeholder="Örn: 0.875 g/cm³ @15°C">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Parlama Noktası °C</label>
                        <input type="number"
                               step="0.01"
                               name="physical_properties[flash_point_c]"
                               class="form-control"
                               required
                               value="{{ old('physical_properties.flash_point_c', $pp['flash_point_c'] ?? '') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Viskozite @40°C cSt</label>
                        <input type="number"
                               step="0.0001"
                               min="0"
                               name="physical_properties[kinematic_viscosity_40_cst]"
                               class="form-control"
                               required
                               value="{{ old('physical_properties.kinematic_viscosity_40_cst', $pp['kinematic_viscosity_40_cst'] ?? '') }}">
                        <div class="form-text">H304/Aspiration hesabında kullanılır.</div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Viskozite @100°C cSt</label>
                        <input type="number"
                               step="0.0001"
                               min="0"
                               name="physical_properties[kinematic_viscosity_100_cst]"
                               class="form-control"
                               required
                               value="{{ old('physical_properties.kinematic_viscosity_100_cst', $pp['kinematic_viscosity_100_cst'] ?? '') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Akma Noktası °C</label>
                        <input type="number"
                               step="0.01"
                               name="physical_properties[pour_point_c]"
                               class="form-control"
                               required
                               value="{{ old('physical_properties.pour_point_c', $pp['pour_point_c'] ?? '') }}">
                    </div>

                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <div class="form-check form-switch mb-0">
                    <input type="hidden"
                           name="override_enabled"
                           value="0">

                    <input class="form-check-input"
                           type="checkbox"
                           value="1"
                           id="overrideEnabled"
                           name="override_enabled"
                           @checked(old('override_enabled', $hasOverride))>

                    <label class="form-check-label fw-bold"
                           for="overrideEnabled">
                        Özel Durum / Override Kullan
                    </label>
                </div>
            </div>

            <div class="card-body"
                 id="overrideArea">

                <div class="alert alert-warning">
                    Normal ürünlerde bu alanı açma.
                    Sadece gerçek tedarikçi SDS, laboratuvar sonucu, ADR değerlendirmesi
                    veya ürün özel bilgisi otomatik sonucu değiştirmeyi gerektiriyorsa kullan.
                </div>

                <div class="accordion" id="overrideAccordion">
                    @php
                        $groups = [
                            'first_aid' => [
                                'title' => 'Bölüm 4 - İlk Yardım',
                                'data' => $fa,
                                'fields' => [
                                    'general' => 'Genel',
                                    'inhalation' => 'Soluma',
                                    'skin_contact' => 'Cilt Teması',
                                    'eye_contact' => 'Göz Teması',
                                    'ingestion' => 'Yutma',
                                    'symptoms' => 'Belirtiler',
                                    'medical_attention' => 'Tıbbi Müdahale',
                                ],
                            ],
                            'fire_fighting' => [
                                'title' => 'Bölüm 5 - Yangın',
                                'data' => $ff,
                                'fields' => [
                                    'suitable_media' => 'Uygun Söndürücü',
                                    'unsuitable_media' => 'Uygun Olmayan Söndürücü',
                                    'special_hazards' => 'Özel Tehlikeler',
                                    'firefighter_advice' => 'İtfaiyeci Tavsiyesi',
                                ],
                            ],
                            'exposure_controls' => [
                                'title' => 'Bölüm 8 - Maruziyet / PPE',
                                'data' => $ec,
                                'fields' => [
                                    'oel' => 'OEL',
                                    'dnel' => 'DNEL',
                                    'pnec' => 'PNEC',
                                    'engineering_controls' => 'Mühendislik Kontrolleri',
                                    'respiratory_protection' => 'Solunum Koruması',
                                    'hand_protection' => 'El Koruması',
                                    'eye_protection' => 'Göz Koruması',
                                ],
                            ],
                            'transport' => [
                                'title' => 'Bölüm 14 - Taşımacılık',
                                'data' => $tr,
                                'fields' => [
                                    'status' => 'Durum: not_regulated / regulated',
                                    'un_number' => 'UN Numarası',
                                    'proper_shipping_name' => 'Taşıma Adı',
                                    'hazard_class' => 'Taşıma Sınıfı',
                                    'packing_group' => 'Ambalaj Grubu',
                                    'environmental_hazards' => 'Çevresel Tehlikeler',
                                ],
                            ],
                            'regulatory' => [
                                'title' => 'Bölüm 15 - Mevzuat',
                                'data' => $rg,
                                'fields' => [
                                    'additional_information' => 'Ek Mevzuat Bilgisi',
                                    'chemical_safety_assessment' => 'Kimyasal Güvenlik Değerlendirmesi',
                                ],
                            ],
                            'revision' => [
                                'title' => 'Bölüm 16 - Revizyon',
                                'data' => $rv,
                                'fields' => [
                                    'version' => 'Versiyon',
                                    'issue_date' => 'Yayın Tarihi',
                                    'revision_date' => 'Revizyon Tarihi',
                                    'change_summary' => 'Değişiklik Açıklaması',
                                ],
                            ],
                        ];
                    @endphp

                    @foreach($groups as $groupName => $group)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $groupName }}">
                                <button class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $groupName }}">
                                    {{ $group['title'] }}
                                </button>
                            </h2>

                            <div id="collapse{{ $groupName }}"
                                 class="accordion-collapse collapse"
                                 data-bs-parent="#overrideAccordion">
                                <div class="accordion-body">
                                    <div class="row g-3">
                                        @foreach($group['fields'] as $key => $label)
                                            <div class="col-md-6">
                                                <label class="form-label">{{ $label }}</label>
                                                <textarea name="override[{{ $groupName }}][{{ $key }}]"
                                                          class="form-control"
                                                          rows="2">{{ old(
                                                    'override.' . $groupName . '.' . $key,
                                                    $group['data'][$key] ?? ''
                                                ) }}</textarea>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="d-grid mb-4">
            <button type="submit"
                    class="btn btn-success btn-lg">
                💾 Kaydet → Otomatik SDS Analizi
            </button>
        </div>
    </form>
</div>

<script>
(function () {
    const checkbox =
        document.getElementById('overrideEnabled');

    const area =
        document.getElementById('overrideArea');

    function refreshOverrideArea() {
        if (!checkbox || !area) {
            return;
        }

        area.style.display =
            checkbox.checked
                ? ''
                : 'none';
    }

    checkbox?.addEventListener(
        'change',
        refreshOverrideArea
    );

    refreshOverrideArea();
})();
</script>
@endsection
