<!DOCTYPE html>
<html lang="{{ request()->query('lang') === 'tr' ? 'tr' : 'en' }}">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>
        SDS - {{ $formula->name }}
    </title>

    <style>
        /* Chrome/Edge 131+ print margin-box page counters. */
        @page {
            size: A4;
            margin: 14mm 13mm 18mm 13mm;
            @bottom-right {
                content: "{{ request()->query('lang') === 'tr' ? 'Sayfa' : 'Page' }} " counter(page) " / " counter(pages);
                font-family: Arial, Helvetica, sans-serif;
                font-size: 8pt;
                color: #333333;
                vertical-align: middle;
            }
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
            font-size: 9.7pt;
            line-height: 1.35;
            background: #fff;
        }

        .toolbar {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            padding: 10px;
            border-bottom: 1px solid #ccc;
            background: #f6f6f6;
        }

        .toolbar button,
        .toolbar a {
            border: 1px solid #333;
            background: #fff;
            color: #111;
            padding: 8px 12px;
            text-decoration: none;
            cursor: pointer;
            border-radius: 4px;
            font: inherit;
        }

        .sheet {
            max-width: 190mm;
            margin: 0 auto;
        }

        .header {
            border: 1px solid {{ $template->header_color ?: '#222222' }};
            margin-bottom: 10px;
        }

        .header-main {
            display: grid;
            grid-template-columns: 1fr 1.6fr 0.85fr;
            align-items: center;
            min-height: 72px;
        }

        .header-cell {
            padding: 10px;
            min-width: 0;
        }

        .header-cell + .header-cell {
            border-left: 1px solid #222;
        }

        .company {
            font-weight: 700;
            font-size: 10pt;
        }

        .doc-title {
            text-align: center;
            font-size: 15pt;
            font-weight: 700;
        }

        .doc-subtitle {
            text-align: center;
            font-size: 9pt;
            margin-top: 4px;
        }

        .meta {
            font-size: 9pt;
        }

        .product {
            border-top: 1px solid #222;
            padding: 8px 10px;
            font-size: 12pt;
            font-weight: 700;
        }

        .section {
            break-inside: auto;
            margin: 0 0 8px 0;
        }

        .section-title {
            background: #ffffff;
            border-bottom: 1.5px solid {{ $template->header_color ?: '#b73842' }};
            border: 1px solid #222;
            padding: 6px 8px;
            font-weight: 700;
            font-size: 10.5pt;
        }

        .section-body {
            border: 1px solid #222;
            border-top: 0;
            padding: 8px;
        }

        .sub {
            margin-bottom: 7px;
        }

        .sub:last-child {
            margin-bottom: 0;
        }

        .sub-title {
            font-weight: 700;
            margin-bottom: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0;
        }

        th,
        td {
            border: 1px solid #777;
            padding: 5px 6px;
            text-align: left;
            vertical-align: top;
            word-break: break-word;
        }

        th {
            background: #f0f0f0;
            font-size: 9pt;
        }

        td {
            font-size: 9pt;
        }

        .small {
            font-size: 8.5pt;
        }

        .muted {
            color: #555;
        }

        .badge {
            display: inline-block;
            border: 1px solid #555;
            padding: 2px 5px;
            border-radius: 3px;
            margin: 1px 2px 1px 0;
            font-size: 8pt;
        }

        .note {
            margin-top: 5px;
            padding: 5px 7px;
            border-left: 3px solid #555;
            background: #f8f8f8;
        }

        .kv {
            display: grid;
            grid-template-columns: 36% 64%;
            border-top: 1px solid #ddd;
        }

        .kv:first-child {
            border-top: 0;
        }

        .kv > div {
            padding: 3px 4px;
        }

        .kv .k {
            font-weight: 700;
        }

        .page-break {
            break-before: page;
        }

        .print-footer { display: none; }
        .kv, tr { break-inside: avoid; }
        thead { display: table-header-group; }
        .sub-title { break-after: avoid; }

        /* Compact reference-style SDS, especially large Section 3 tables. */
        .header { border-left: 0; border-right: 0; border-top: 0; }
        .header-main { min-height: 52px; grid-template-columns: 1fr 1.7fr 0.85fr; }
        .header-cell { padding: 5px 8px; }
        .header-cell + .header-cell { border-left: 0; }
        .doc-title { font-size: 13pt; }
        .product { font-size: 10.5pt; padding: 5px 8px; }
        .section { margin-bottom: 5px; }
        .section-title { border: 0; border-bottom: 1.4px solid {{ $template->header_color ?: '#b73842' }}; padding: 4px 2px; font-size: 9.7pt; }
        .section-body { border: 0; padding: 5px 2px; }
        .sub { margin-bottom: 5px; }
        .kv { grid-template-columns: 35% 65%; }
        .kv > div { padding: 2px 3px; }
        table th, table td { font-size: 8pt; padding: 3px 4px; }
        .exposure-table th, .exposure-table td { border-color: #c3c8ce; font-size: 7.7pt; }
        .exposure-table th { background: #e9e9e9; }
        .exposure-substance { font-weight: bold; background: #f2f2f2; padding: 3px 5px; margin-top: 7px; }
        .exposure-source { color: #555; font-size: 7.5pt; }

        @media print {
            .print-footer {
                display: block;
                position: fixed;
                bottom: -11mm;
                left: 0;
                right: 0;
                border-top: 1px solid #999;
                padding-top: 3px;
                font-size: 8pt;
                color: #555;
            }
            .toolbar {
                display: none !important;
            }

            body {
                font-size: 9.7pt;
            }

            .sheet {
                max-width: none;
                margin: 0;
            }

            .section-title {
                background: {{ $template->accent_color ?: '#e9ecef' }} !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            th {
                background: #f0f0f0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        /* Professional SDS print typography, modeled on the supplied reference document. */
        :root { --sds-ink: #22272c; --sds-rule: #cbd0d4; --sds-muted: #525b65; }
        body { color:var(--sds-ink); font-size:9.15pt; line-height:1.30; }
        .sheet { max-width:184mm; }
        .header { border:0; border-bottom:1px solid var(--sds-rule); margin-bottom:11px; }
        .header-main { grid-template-columns:24% 51% 25%; min-height:67px; }
        .header-cell { padding:5px 4px; }
        .header-cell + .header-cell { border-left:0; }
        .header-cell img { max-height:56px !important; max-width:130px !important; margin-bottom:0 !important; }
        .doc-title { font-size:13pt; letter-spacing:.35px; line-height:1.25; }
        .meta { color:var(--sds-muted); font-size:8pt; text-align:right; line-height:1.55; }
        .product { padding:8px 0 7px; font-size:11pt; text-align:center; border-top:1px solid var(--sds-rule); }
        .section { margin:0 0 12px; break-inside:auto; }
        .section-title { background:transparent !important; color:var(--sds-ink); border:0 !important; border-bottom:1.6px solid {{ $template->header_color ?: '#a32831' }} !important; padding:5px 0 4px; font-size:10pt; line-height:1.3; break-after:avoid; }
        .section-body { border:0 !important; padding:6px 0 1px; }
        .sub { margin:0 0 8px; }
        .sub-title { font-size:9pt; margin-bottom:4px; padding-top:1px; }
        .kv { display:grid; grid-template-columns:38% 62%; border:0; border-bottom:1px solid #e2e5e7; break-inside:avoid; }
        .kv > div { padding:4px 5px; }
        .kv .k { font-weight:400; color:#414b55; }
        table { border-collapse:collapse; margin:5px 0 8px; table-layout:fixed; }
        th, td { border:0 !important; border-bottom:1px solid #dce0e3 !important; padding:5px 5px !important; line-height:1.35; overflow-wrap:anywhere; }
        th { border-top:1px solid #b7bec4 !important; border-bottom:1px solid #b7bec4 !important; background:#f2f4f5; font-size:8pt; font-weight:700; }
        td { font-size:8.1pt; }
        tr { break-inside:avoid; }
        .note { background:#f8f8f8; border-left:2px solid {{ $template->header_color ?: '#a32831' }}; padding:5px 7px; margin:4px 0; }
        .exposure-substance { background:#f5f6f7; border-bottom:1px solid #ccc; margin-top:9px; padding:5px; }
        .exposure-table { table-layout:auto; }
        .exposure-table td, .exposure-table th { font-size:8pt !important; }
        .print-footer { font-size:8pt; color:#58616a; }
        @media print {
            .sheet { max-width:none; }
            .section-title { background:transparent !important; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
            th { background:#f2f4f5 !important; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
            .header, .sub-title { break-after:avoid; }
            .print-footer { bottom:-9mm; right:auto; width:68%; }
        }
        .ghs-images { display: flex; flex-wrap: wrap; gap: 8px; padding-top: 5px; align-items: center; }
        .ghs-item { display: inline-block; break-inside: avoid; }
        .ghs-pictogram { width: 72px; height: 72px; object-fit: contain; }
        .ghs-missing { display: inline-block; font-size: 8pt; padding: 5px 7px; border: 1px dashed #b74141; }
        @media print { .ghs-pictogram { width: 18mm; height: 18mm; } }
    </style>
</head>

<body>

@php
    $sections =
        $sdsDraft['sections']
        ?? [];

    // Attempt template logo first; always fall back to company logo if file is absent.
    $printLogoUrl = null;
    $logoCandidates = [
        $template->logo_path ?? null,
        $company->logo_path ?? null,
    ];
    foreach ($logoCandidates as $candidate) {
        if (!is_string($candidate) || trim($candidate) === '') {
            continue;
        }
        // Accept only public/sds/logos filenames; no user-controlled filesystem paths.
        $relative = ltrim(str_replace('\\', '/', trim($candidate)), '/');
        $relative = preg_replace('~^public/~i', '', $relative);
        if (!preg_match('~^sds/logos/[A-Za-z0-9._-]+\.(png|jpe?g|webp)$~i', $relative)) {
            continue;
        }
        $absolute = public_path($relative);
        if (!is_file($absolute) || !is_readable($absolute) || filesize($absolute) > 2097152) {
            continue;
        }
        $mime = function_exists('mime_content_type') ? mime_content_type($absolute) : null;
        if (!in_array($mime, ['image/png','image/jpeg','image/webp'], true)) {
            continue;
        }
        $data = file_get_contents($absolute);
        if ($data === false) {
            continue;
        }
        $printLogoUrl = 'data:' . $mime . ';base64,' . base64_encode($data);
        break;
    }
    // GHS sembolleri Wikimedia Commons üzerinden doğrudan HTTPS ile yüklenir.
    // Dosya indirme, sunucuya kayıt veya Laravel HTTP isteği yapılmaz.
    $ghsNames = [
        'GHS01' => 'GHS-pictogram-explos.svg',
        'GHS02' => 'GHS-pictogram-flamme.svg',
        'GHS03' => 'GHS-pictogram-rondflam.svg',
        'GHS04' => 'GHS-pictogram-bottle.svg',
        'GHS05' => 'GHS-pictogram-acid.svg',
        'GHS06' => 'GHS-pictogram-skull.svg',
        'GHS07' => 'GHS-pictogram-exclam.svg',
        'GHS08' => 'GHS-pictogram-silhouette.svg',
        'GHS09' => 'GHS-pictogram-pollu.svg',
    ];
    $ghsImageData = [];
    foreach ($ghsNames as $ghsCode => $fileName) {
        $ghsImageData[$ghsCode] = 'https://commons.wikimedia.org/wiki/Special:FilePath/' . $fileName;
    }
    $sdsLang = request()->query('lang') === 'tr' ? 'tr' : 'en';
    $labels = [
      'tr' => [
        'company'=>'Firma', 'template'=>'Şablon','prepared'=>'Hazırlayan','cert'=>'Belge / sertifika',
        'issuer'=>'Belgeyi veren','expiry'=>'Geçerlilik tarihi','version'=>'Revizyon',
        'issue'=>'Yayın tarihi','formula'=>'Reçete ID','product'=>'Ürün kodu',
        'sources'=>'Kaynaklar','control'=>'Belge kontrol bilgileri',
        'chemical'=>'Kimyasal adı','classes'=>'Sınıflandırma', 'notClassified'=>'Sınıflandırılmamış',
        'draft'=>'Bu belge otomatik hazırlanmış TASLAKTIR. Yetkili inceleme ve revizyon kaydı tamamlanmadan onaylı değildir.',
        'sections'=>[
          1=>'Madde/karışımın ve şirketin/dağıtıcının kimliği',2=>'Zararlılık tanımlanması',
          3=>'Bileşimi/içindekiler hakkında bilgi',4=>'İlk yardım önlemleri',
          5=>'Yangınla mücadele önlemleri',6=>'Kaza sonucu yayılmaya karşı önlemler',
          7=>'Elleçleme ve depolama',8=>'Maruz kalma kontrolleri/kişisel korunma',
          9=>'Fiziksel ve kimyasal özellikler',10=>'Kararlılık ve tepkime',
          11=>'Toksikolojik bilgiler',12=>'Ekolojik bilgiler',13=>'Bertaraf etme bilgileri',
          14=>'Taşımacılık bilgileri',15=>'Mevzuat bilgileri',16=>'Diğer bilgiler'
        ]
      ],
      'en' => [
        'company'=>'Company', 'template'=>'Template','prepared'=>'Prepared by','cert'=>'Certificate',
        'issuer'=>'Issued by','expiry'=>'Expiry date','version'=>'Revision',
        'issue'=>'Issue date','formula'=>'Formula ID','product'=>'Product code',
        'sources'=>'Sources','control'=>'Document control',
        'chemical'=>'Chemical name','classes'=>'Classification','notClassified'=>'Not classified',
        'draft'=>'AUTOMATIC DRAFT. Technical review and revision recording are required before approval.',
        'sections'=>[
          1=>'Identification of the substance/mixture and of the company/undertaking',
          2=>'Hazards identification',3=>'Composition/information on ingredients',4=>'First aid measures',
          5=>'Firefighting measures',6=>'Accidental release measures',7=>'Handling and storage',
          8=>'Exposure controls/personal protection',9=>'Physical and chemical properties',
          10=>'Stability and reactivity',11=>'Toxicological information',12=>'Ecological information',
          13=>'Disposal considerations',14=>'Transport information',15=>'Regulatory information',
          16=>'Other information'
        ]
      ],
    ];
    $L = $labels[$sdsLang];
    $subLabels = [
      'tr' => ['1.1'=>'Ürün tanımlayıcı','1.2'=>'İlgili tanımlanmış kullanımlar ve tavsiye edilmeyen kullanımlar','1.3'=>'Güvenlik bilgi formu tedarikçisinin bilgileri','1.4'=>'Acil telefon numarası','2.1'=>'Maddenin veya karışımın sınıflandırılması','2.2'=>'Etiket unsurları','2.3'=>'Diğer zararlar ve özel değerlendirmeler','3.2'=>'Karışımlar','product'=>'Ürün','code'=>'Ürün kodu','against'=>'Tavsiye edilmeyen kullanımlar','signal'=>'Uyarı kelimesi','pictograms'=>'Piktogramlar','hazards'=>'Zararlılık ifadeleri','none'=>'Yok','unclassified'=>'Mevcut karışım sınıflandırma değerlendirmesine göre sınıflandırılmamıştır.','notes'=>'Notlar'],
      'en' => ['1.1'=>'Product identifier','1.2'=>'Relevant identified uses and uses advised against','1.3'=>'Details of the supplier of the safety data sheet','1.4'=>'Emergency telephone number','2.1'=>'Classification of the substance or mixture','2.2'=>'Label elements','2.3'=>'Other hazards and special assessments','3.2'=>'Mixtures','product'=>'Product','code'=>'Product code','against'=>'Uses advised against','signal'=>'Signal word','pictograms'=>'Pictograms','hazards'=>'Hazard statements','none'=>'None','unclassified'=>'Not classified according to the current mixture classification assessment.','notes'=>'Notes']
    ];
    $SL = $subLabels[$sdsLang];

    $valueTranslationsTr = [
      'Lubricants and related applications.'=>'Yağlayıcılar ve ilgili uygulamalar.',
      'Uses other than the intended applications are not recommended.'=>'Amaçlanan kullanımlar dışındaki uygulamalar önerilmez.',
      'Remove the affected person from the source of exposure. If in doubt or if symptoms persist, obtain medical advice.'=>'Etkilenen kişiyi maruziyet kaynağından uzaklaştırın. Şüphe halinde veya belirtiler devam ederse tıbbi yardım alın.',
      'Move the person to fresh air if discomfort occurs. Seek medical advice if symptoms persist.'=>'Rahatsızlık görülürse kişiyi temiz havaya çıkarın. Belirtiler devam ederse tıbbi yardım alın.',
      'Remove contaminated clothing and wash skin with soap and water. Obtain medical advice if irritation persists.'=>'Kirlenmiş giysileri çıkarın ve cildi sabun ve suyla yıkayın. Tahriş sürerse tıbbi yardım alın.',
      'Rinse carefully with clean water for several minutes. Remove contact lenses if easy to do. Obtain medical advice if irritation persists.'=>'Birkaç dakika temiz suyla dikkatlice durulayın. Kolayca çıkarılabiliyorsa kontakt lensleri çıkarın. Tahriş sürerse tıbbi yardım alın.',
      'Rinse mouth. Do not give anything by mouth to an unconscious person. Obtain medical advice if unwell.'=>'Ağzı çalkalayın. Bilinci kapalı kişiye ağızdan hiçbir şey vermeyin. Rahatsızlık halinde tıbbi yardım alın.',
      'No specific acute symptoms are expected under normal conditions of use; prolonged or repeated contact may cause discomfort or irritation.'=>'Normal kullanımda belirli bir akut belirti beklenmez; uzun veya tekrarlı temas rahatsızlık veya tahrişe yol açabilir.',
      'Treat symptomatically and according to the route and severity of exposure.'=>'Maruziyet yolu ve şiddetine göre semptomatik tedavi uygulayın.',
      'Foam, dry chemical powder, carbon dioxide (CO2) or water mist.'=>'Köpük, kuru kimyevi toz, karbondioksit (CO2) veya su sisi.',
      'Do not use a strong direct water jet.'=>'Kuvvetli doğrudan su jeti kullanmayın.',
      'Combustion may produce irritating or harmful fumes and carbon oxides.'=>'Yanma tahriş edici veya zararlı dumanlar ve karbon oksitler oluşturabilir.',
      'Thermal decomposition or combustion may generate carbon oxides and other irritating fumes.'=>'Isıl bozunma veya yanma karbon oksitler ve diğer tahriş edici dumanlar oluşturabilir.',
      'Use suitable protective equipment and self-contained breathing apparatus where required. Cool exposed containers with water spray.'=>'Uygun koruyucu ekipman kullanın; gerektiğinde bağımsız solunum cihazı takın. Isıya maruz kalan kapları su spreyiyle soğutun.',
      'Avoid contact with skin and eyes. Avoid breathing vapour, mist or aerosol. Use suitable personal protective equipment and provide adequate ventilation.'=>'Cilt ve göz temasından kaçının. Buhar, sis veya aerosolü solumayın. Uygun kişisel koruyucu donanım kullanın ve yeterli havalandırma sağlayın.',
      'Prevent uncontrolled release to soil, drains, sewers, surface water and groundwater.'=>'Toprağa, giderlere, kanalizasyona, yüzey ve yeraltı sularına kontrolsüz salımı önleyin.',
      'Stop the leak if it can be done safely. Absorb with inert non-combustible material such as sand, earth or suitable absorbent and collect in a suitable labelled container.'=>'Güvenliyse sızıntıyı durdurun. Kum, toprak veya uygun emici gibi yanmaz inert malzeme ile emdirip uygun etiketli kapta toplayın.',
      'See Section 8 for personal protective equipment and Section 13 for disposal considerations.'=>'Kişisel korunma için Bölüm 8’e, bertaraf için Bölüm 13’e bakın.',
      'Avoid unnecessary contact with skin and eyes and avoid formation of mist or aerosol. Observe good industrial hygiene practices.'=>'Cilt ve gözlerle gereksiz temastan ve sis/aerosol oluşumundan kaçının. İyi endüstriyel hijyen uygulamalarına uyun.',
      'Do not eat, drink or smoke while handling the product. Wash hands after use.'=>'Ürünü kullanırken yemeyin, içmeyin veya sigara içmeyin. Kullanımdan sonra ellerinizi yıkayın.',
      'Keep containers closed. Store appropriately away from heat and sources of ignition.'=>'Kapları kapalı tutun. Isıdan ve tutuşturucu kaynaklardan uzakta uygun koşullarda depolayın.',
      'Keep containers tightly closed in a cool, dry and well-ventilated place. Protect from excessive heat and incompatible materials.'=>'Kapları serin, kuru ve iyi havalandırılan yerde sıkıca kapalı tutun. Aşırı ısıdan ve uyumsuz maddelerden koruyun.',
      'Strong oxidising agents.'=>'Kuvvetli oksitleyiciler.',
      'No product-specific occupational exposure limit is stored in the local SDS regulatory database. Applicable national or component-specific limits shall be observed where available.'=>'Yerel SDS veri tabanında ürüne özel mesleki maruziyet sınırı kayıtlı değil. Varsa geçerli ulusal veya bileşene özel sınırlar dikkate alınmalıdır.',
      'No product-specific DNEL value is stored in the local SDS regulatory database.'=>'Yerel SDS mevzuat veri tabanında ürüne özel DNEL değeri kayıtlı değil.',
      'No product-specific PNEC value is stored in the local SDS regulatory database.'=>'Yerel SDS mevzuat veri tabanında ürüne özel PNEC değeri kayıtlı değil.',
      'Provide adequate general ventilation. Use local exhaust ventilation where mist or aerosol may be generated.'=>'Yeterli genel havalandırma sağlayın. Sis veya aerosol oluşabilecek yerlerde lokal emiş kullanın.',
      'Respiratory protection is normally not required under adequate ventilation. Use suitable respiratory protective equipment where exposure cannot be adequately controlled.'=>'Yeterli havalandırmada normalde solunum koruması gerekmez. Maruziyet yeterince kontrol edilemiyorsa uygun solunum koruyucusu kullanın.',
      'Wear suitable chemical-resistant gloves where prolonged or repeated contact is possible.'=>'Uzun veya tekrarlı temas ihtimalinde uygun kimyasala dayanıklı eldiven kullanın.',
      'Wear suitable safety goggles when splashing is possible.'=>'Sıçrama olasılığında uygun koruyucu gözlük kullanın.',
      'Use suitable work clothing. Remove contaminated clothing and clean before reuse.'=>'Uygun iş giysisi kullanın. Kirlenmiş giysileri çıkarın ve yeniden kullanmadan önce temizleyin.',
      'Prevent uncontrolled release to the environment.'=>'Çevreye kontrolsüz salımı önleyin.',
      'Liquid'=>'Sıvı',
      'Amber/Clear'=>'Kehribar/Şeffaf',
      'Characteristic'=>'Karakteristik',
      'Not applicable for hydrocarbon/oil-based mixture unless otherwise determined.'=>'Aksi belirlenmedikçe hidrokarbon/yağ bazlı karışım için uygulanabilir değildir.',
      'Insoluble / practically insoluble in water'=>'Suda çözünmez / pratik olarak çözünmez',
      'No separate flammability classification assigned from the current mixture classification.'=>'Mevcut karışım sınıflandırmasından ayrı bir alevlenebilirlik sınıfı atanmadı.',
      'Not applicable - product is a liquid.'=>'Uygulanmaz - ürün sıvıdır.',
      'Other properties are generated from product-family defaults unless a product-specific override is entered.'=>'Diğer özellikler, ürüne özel değişiklik yapılmadıkça ürün ailesi varsayılanlarından oluşturulur.',
      'No specific reactivity hazard is expected under normal conditions of use.'=>'Normal kullanım koşullarında belirli bir tepkime zararı beklenmez.',
      'Stable under recommended storage and handling conditions.'=>'Önerilen depolama ve elleçleme koşullarında kararlıdır.',
      'Hazardous reactions are not expected under normal conditions of use and storage.'=>'Normal kullanım ve depolama koşullarında zararlı tepkimeler beklenmez.',
      'Avoid excessive heat, open flames, sparks and other ignition sources.'=>'Aşırı ısı, açık alev, kıvılcım ve diğer tutuşturucu kaynaklardan kaçının.',
      'Not classified by the current mixture calculation.'=>'Mevcut karışım hesaplamasına göre sınıflandırılmamıştır.',
      'No product-specific conclusion is stored in the SDS module.'=>'SDS modülünde ürüne özel değerlendirme kaydı bulunmuyor.',
      'No product-specific ecotoxicity test data is stored in the SDS module.'=>'SDS modülünde ürüne özel ekotoksisite test verisi kayıtlı değil.',
      'No product-specific data available.'=>'Ürüne özel veri mevcut değil.',
      'No product-specific assessment is stored in the SDS module.'=>'SDS modülünde ürüne özel değerlendirme kayıtlı değil.',
      'No additional information available.'=>'İlave bilgi mevcut değil.',
      'Do not discharge to drains, soil or water. Recover or recycle where technically and legally appropriate; otherwise use an authorised waste contractor. Waste classification and waste code must be assigned according to actual use and contamination history.'=>'Giderlere, toprağa veya suya boşaltmayın. Teknik ve yasal açıdan uygunsa geri kazanın veya geri dönüştürün; aksi halde yetkili atık işleme kuruluşuna teslim edin. Atık sınıflandırması ve kodu gerçek kullanım ve kirlenme geçmişine göre belirlenmelidir.',
      'To be assigned by the waste producer according to actual use and contamination history.'=>'Gerçek kullanım ve kirlenme geçmişine göre atık üreticisi tarafından belirlenmelidir.',
      'Drain containers thoroughly and manage contaminated packaging through an authorised waste route.'=>'Kapları iyice boşaltın ve kirlenmiş ambalajları yetkili atık yönetimi yoluyla bertaraf edin.',
      'Not applicable'=>'Uygulanmaz',
      'Not applicable / not determined.'=>'Uygulanmaz / belirlenmedi.',
      'Not regulated as dangerous goods under the default lubricant transport profile unless a product-specific assessment indicates otherwise.'=>'Ürüne özgü değerlendirme aksi yönde olmadıkça varsayılan yağlayıcı taşıma profiline göre tehlikeli madde taşımacılığı kapsamında değerlendirilmemiştir.',
      'Prevent leakage and secure containers during transport.'=>'Sızıntıyı önleyin ve taşıma sırasında kapları sabitleyin.',
      'No product-specific chemical safety assessment is stored in the SDS module unless explicitly entered.'=>'Ayrıca girilmedikçe SDS modülünde ürüne özel kimyasal güvenlik değerlendirmesi kayıtlı değil.',
       'Note L: IP 346 demonstrated DMSO extract below 3%; harmonised carcinogenic classification does not apply.'=>'Not L: IP 346 ile DMSO ekstraktının %3 altında olduğu kanıtlandı; harmonize kanserojen sınıflandırması uygulanmadı.',
       'No data available'=>'Veri mevcut değil',
       'Not available / Not applicable'=>'Veri mevcut değil / uygulanamaz',

    ];
    $translateValue = function ($value) use (&$translateValue, $sdsLang, $valueTranslationsTr) {
        if (is_array($value)) {
            $out=[];
            foreach ($value as $key => $child) $out[$key]=$translateValue($child);
            return $out;
        }
        if (!is_string($value)) return $value;
        $v=trim($value);
        if ($sdsLang === 'tr') {
            return $valueTranslationsTr[$v] ?? $value;
        }
        $reverse=array_flip($valueTranslationsTr);
        if (isset($reverse[$v])) return $reverse[$v];
        // Note L can be generated with an extra 'Note L:' prefix by the engine.
        if (preg_match('/^(?:Note L:\s*)?Note L:\s*IP 346 ile DMSO ekstraktının %3 altında olduğu kanıtlandı; harmonize kanserojen sınıflandırması uygulanmadı\.?$/u', $v)) {
            return 'Note L: IP 346 demonstrated DMSO extract below 3%; harmonised carcinogenic classification does not apply.';
        }
        if (preg_match('/^Note L:\s*IP 346 ile DMSO ekstraktının %3 altında olduğu kanıtlandı; harmonize kanserojen sınıflandırması uygulanmadı\.?$/u', $v)) {
            return 'Note L: IP 346 demonstrated DMSO extract below 3%; harmonised carcinogenic classification does not apply.';
        }
        return $value;
    };

    $fieldLabels = [
      'general'=>['Genel','General'],
      'inhalation'=>['Solunması halinde','Inhalation'],
      'skin contact'=>['Ciltle teması halinde','Skin contact'],
      'eye contact'=>['Gözle teması halinde','Eye contact'],
      'ingestion'=>['Yutulması halinde','Ingestion'],
      'important symptoms and effects'=>['Önemli belirtiler ve etkiler','Important symptoms and effects'],
      'medical attention and special treatment'=>['Tıbbi müdahale ve özel tedavi','Medical attention and special treatment'],
      'suitable extinguishing media'=>['Uygun söndürücü maddeler','Suitable extinguishing media'],
      'unsuitable extinguishing media'=>['Uygun olmayan söndürücü maddeler','Unsuitable extinguishing media'],
      'special hazards'=>['Özel zararlar','Special hazards'],
      'hazardous combustion products'=>['Zararlı yanma ürünleri','Hazardous combustion products'],
      'advice for firefighters'=>['İtfaiyeciler için tavsiyeler','Advice for firefighters'],
      'personal precautions'=>['Kişisel önlemler','Personal precautions'],
      'environmental precautions'=>['Çevresel önlemler','Environmental precautions'],
      'containment and cleanup'=>['Muhafaza ve temizleme yöntemleri','Containment and cleanup'],
      'reference to other sections'=>['Diğer bölümlere atıflar','Reference to other sections'],
      'safe handling'=>['Güvenli elleçleme','Safe handling'],
      'occupational hygiene'=>['İş hijyeni','Occupational hygiene'],
      'storage conditions'=>['Depolama koşulları','Storage conditions'],
      'incompatible materials'=>['Uyumsuz maddeler','Incompatible materials'],
      'specific end use'=>['Belirli son kullanım','Specific end use'],
      'oel'=>['Mesleki maruziyet sınırları','Occupational exposure limits'],
      'dnel'=>['DNEL','DNEL'],
      'pnec'=>['PNEC','PNEC'],
      'engineering controls'=>['Mühendislik kontrolleri','Engineering controls'],
      'respiratory protection'=>['Solunum koruması','Respiratory protection'],
      'hand protection'=>['El koruması','Hand protection'],
      'eye protection'=>['Göz koruması','Eye protection'],
      'skin body protection'=>['Cilt ve vücut koruması','Skin and body protection'],
      'environmental exposure controls'=>['Çevresel maruziyet kontrolleri','Environmental exposure controls'],
      'physical state'=>['Fiziksel hal','Physical state'],
      'colour'=>['Renk','Colour'],
      'odour'=>['Koku','Odour'],
      'ph'=>['pH','pH'],
      'flash point c'=>['Parlama noktası (°C)','Flash point (°C)'],
      'density'=>['Yoğunluk','Density'],
      'water solubility'=>['Suda çözünürlük','Water solubility'],
      'flammability'=>['Alevlenebilirlik','Flammability'],
      'kinematic viscosity 40 cst'=>['40°C kinematik viskozite (mm²/s)','Kinematic viscosity at 40°C (mm²/s)'],
      'kinematic viscosity 100 cst'=>['100°C kinematik viskozite (mm²/s)','Kinematic viscosity at 100°C (mm²/s)'],
      'pour point c'=>['Akma noktası (°C)','Pour point (°C)'],
      'particle characteristics'=>['Parçacık özellikleri','Particle characteristics'],
      'other information'=>['Diğer bilgiler','Other information'],
      'reactivity'=>['Tepkime','Reactivity'],
      'chemical stability'=>['Kimyasal kararlılık','Chemical stability'],
      'hazardous reactions'=>['Zararlı tepkimeler','Hazardous reactions'],
      'conditions to avoid'=>['Kaçınılması gereken durumlar','Conditions to avoid'],
      'hazardous decomposition products'=>['Zararlı bozunma ürünleri','Hazardous decomposition products'],
      'acute toxicity'=>['Akut toksisite','Acute toxicity'],
      'skin corrosion irritation'=>['Cilt aşınması/tahrişi','Skin corrosion/irritation'],
      'serious eye damage irritation'=>['Ciddi göz hasarı/tahrişi','Serious eye damage/irritation'],
      'respiratory skin sensitisation'=>['Solunum/cilt hassaslaştırıcılığı','Respiratory/skin sensitisation'],
      'germ cell mutagenicity'=>['Eşey hücre mutajenitesi','Germ cell mutagenicity'],
      'carcinogenicity'=>['Kanserojenite','Carcinogenicity'],
      'reproductive toxicity'=>['Üreme sistemi toksisitesi','Reproductive toxicity'],
      'stot single exposure'=>['BHOT tek maruz kalma','STOT single exposure'],
      'stot repeated exposure'=>['BHOT tekrarlı maruz kalma','STOT repeated exposure'],
      'aspiration hazard'=>['Aspirasyon zararı','Aspiration hazard'],
      'additional information'=>['İlave bilgiler','Additional information'],
      'endocrine disrupting properties'=>['Endokrin bozucu özellikler','Endocrine disrupting properties'],
      'aquatic classification'=>['Sucul ortam sınıflandırması','Aquatic classification'],
      'toxicity data'=>['Toksisite verileri','Toxicity data'],
      'persistence degradability'=>['Kalıcılık ve bozunabilirlik','Persistence and degradability'],
      'bioaccumulative potential'=>['Biyobirikim potansiyeli','Bioaccumulative potential'],
      'mobility in soil'=>['Toprakta hareketlilik','Mobility in soil'],
      'pbt vpvb assessment'=>['PBT/vPvB değerlendirmesi','PBT/vPvB assessment'],
      'other adverse effects'=>['Diğer olumsuz etkiler','Other adverse effects'],
      'waste treatment methods'=>['Atık işleme yöntemleri','Waste treatment methods'],
      'waste code'=>['Atık kodu','Waste code'],
      'contaminated packaging'=>['Kirlenmiş ambalaj','Contaminated packaging'],
      'un number'=>['UN numarası','UN number'],
      'proper shipping name'=>['Uygun UN taşımacılık adı','Proper shipping name'],
      'transport hazard class'=>['Taşımacılık zararlılık sınıfı','Transport hazard class'],
      'packing group'=>['Ambalajlama grubu','Packing group'],
      'environmental hazards'=>['Çevresel zararlar','Environmental hazards'],
      'special precautions'=>['Özel önlemler','Special precautions'],
      'maritime transport in bulk'=>['Deniz yoluyla dökme taşımacılık','Maritime transport in bulk'],
      'regulation profile'=>['Mevzuat profili','Regulation profile'],
      'reach registered substances'=>['REACH kayıtlı maddeler','REACH registered substances'],
      'svhc candidate list'=>['SVHC aday listesi','SVHC candidate list'],
      'annex xiv'=>['Ek XIV','Annex XIV'],
      'annex xvii'=>['Ek XVII','Annex XVII'],
      'additional regulatory information'=>['Ek mevzuat bilgileri','Additional regulatory information'],
      'chemical safety assessment'=>['Kimyasal güvenlik değerlendirmesi','Chemical safety assessment'],
      'abbreviations'=>['Kısaltmalar','Abbreviations'],
      'hazard statement texts'=>['Zararlılık ifadesi metinleri','Hazard statement texts'],
    ];
    $fieldLabel = function($k) use ($fieldLabels,$sdsLang) {
        $text = str_replace('_',' ',(string)$k);
        return $fieldLabels[strtolower($text)][$sdsLang === 'tr' ? 0 : 1] ?? ucfirst($text);
    };
    $prettyValue = function($v) use (&$prettyValue, $sdsLang, $translateValue) {
      if ($v === null || $v === '') return '—';
      if (is_bool($v)) return $v ? ($sdsLang === 'tr' ? 'Evet' : 'Yes') : ($sdsLang === 'tr' ? 'Hayır' : 'No');
      if (is_scalar($v)) return (string)$v;
      if (!is_array($v)) return '—';
      $list=[];
      foreach($v as $k=>$item) {
        $text=$prettyValue($translateValue($item));
        if ($text === '—') continue;
        $list[]=(is_string($k) && !is_numeric($k) ? str_replace('_',' ',$k).': ' : '').$text;
      }
      return implode('; ',$list) ?: '—';
    };

    $companyData = $company->only(['name','address','phone','fax','email','emergency_phone','logo_path']);
    $company = $companyData;

    $section1 =
        $sections[1]['content']
        ?? [];

    $productName =
        $section1['1.1']['product_name']
        ?? $formula->name;

    $productCode =
        $section1['1.1']['product_code']
        ?? null;

    $revision =
        $sections[16]['content']['revision']
        ?? [];

    $signalWord =
        $classification[
            'classification'
        ][
            'signal_word'
        ] ?? null;

    $pictograms =
        $classification[
            'classification'
        ][
            'pictograms'
        ] ?? [];

    $pictogramCodes = collect($pictograms ?? [])
        ->flatten()
        ->map(function ($item) {
            $text = strtoupper((string) $item);
            if (preg_match('/GHS[\\s_-]*0?([1-9])/', $text, $match)) {
                return sprintf('GHS%02d', (int) $match[1]);
            }
            return null;
        })
        ->filter()
        ->unique()
        ->values()
        ->all();

    $hazardStatements =
        $classification[
            'classification'
        ][
            'hazard_statements'
        ] ?? [];

    $renderValue = function ($value) use (&$renderValue) {
        if ($value === null || $value === '') {
            return 'Not available / Not applicable';
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        if (is_array($value)) {
            $parts = [];

            foreach ($value as $key => $item) {
                if (is_array($item)) {
                    $parts[] = json_encode(
                        $item,
                        JSON_UNESCAPED_UNICODE |
                        JSON_UNESCAPED_SLASHES
                    );
                } else {
                    $parts[] =
                        is_string($key)
                            ? $key . ': ' . $item
                            : (string) $item;
                }
            }

            return implode(' | ', $parts);
        }

        return (string) $value;
    };
@endphp

<div class="toolbar">
    <a href="{{ request()->fullUrlWithQuery(['lang' => 'tr']) }}">TR</a>
    <a href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}">EN</a>
    <a href="{{ route(
        'sds.formula-analysis.show',
        [
            'formula' => $formula->id,
            'regulation_profile' => $regulationProfile
        ]
    ) }}">
        ← Analize Dön
    </a>

    <button type="button"
            onclick="window.print()">
        🖨️ Yazdır / PDF Kaydet
    </button>
</div>

<div class="sheet">

    <div class="header">

        <div class="header-main">

            <div class="header-cell">
                @if($printLogoUrl)
                   <img src="{{ $printLogoUrl }}" alt="Logo" style="width:auto;height:auto;max-width:160px;max-height:85px;object-fit:contain;object-position:left center;display:block;margin-bottom:5px">
                @endif

            </div>

            <div class="header-cell">
                <div class="doc-title">
                    {{ $sdsLang === 'tr' ? 'GÜVENLİK BİLGİ FORMU' : 'SAFETY DATA SHEET' }}
                </div>
            </div>

            <div class="header-cell meta">
                <div>
                    <strong>{{ $L['version'] }}:</strong>
                    {{ $revision['version'] ?? '1' }}
                </div>

                <div>
                    <strong>{{ $L['issue'] }}:</strong>
                    {{ $revision['issue_date'] ?? now()->format('Y-m-d') }}
                </div>

                @if($productCode)
                    <div>
                        <strong>{{ $L['product'] }}:</strong>
                        {{ $productCode }}
                    </div>
                @endif
            </div>

        </div>

        <div class="product">
            {{ $productName }}
        </div>

    </div>

    {{-- 1 --}}
    @php $s = $sections[1]['content'] ?? []; @endphp
    <section class="section">
        <div class="section-title">
            {{ 1 }}. {{ $L['sections'][1] }}
        </div>

        <div class="section-body">

            <div class="sub">
                <div class="sub-title">1.1 {{ $SL['1.1'] }}</div>
                <div>
                    <strong>{{ $SL['product'] }}:</strong>
                    {{ $s['1.1']['product_name'] ?? $productName }}
                </div>

                @if(!empty($s['1.1']['product_code']))
                    <div>
                        <strong>{{ $SL['code'] }}:</strong>
                        {{ $s['1.1']['product_code'] }}
                    </div>
                @endif
            </div>

            <div class="sub">
                <div class="sub-title">1.2 {{ $SL['1.2'] }}</div>
                <div>
                    {{ $translateValue($s['1.2']['identified_use'] ?? '-') }}
                </div>
                <div>
                    <strong>{{ $SL['against'] }}:</strong>
                    {{ $translateValue($s['1.2']['uses_advised_against'] ?? '-') }}
                </div>
            </div>

            <div class="sub">
                <div class="sub-title">1.3 {{ $SL['1.3'] }}</div>
                <div>{{ $company['name'] ?? '-' }}</div>
                <div>{{ $company['address'] ?? '-' }}</div>
                <div>
                    Tel: {{ $company['phone'] ?? '-' }}
                    @if(!empty($company['fax']))
                        | Fax: {{ $company['fax'] }}
                    @endif
                </div>
                <div>
                    E-mail: {{ $company['email'] ?? '-' }}
                </div>
            </div>

            <div class="sub">
                <div class="sub-title">1.4 {{ $SL['1.4'] }}</div>
                <div>
                    {{ $company['emergency_phone'] ?? '-' }}
                </div>
            </div>

        </div>
    </section>

    {{-- 2 --}}
    @php $s = $sections[2]['content'] ?? []; @endphp
    <section class="section">
        <div class="section-title">
            {{ 2 }}. {{ $L['sections'][2] }}
        </div>

        <div class="section-body">
            <div class="sub">
                <div class="sub-title">2.1 {{ $SL['2.1'] }}</div>

                @forelse($s['2.1']['hazard_classes'] ?? [] as $hazard)
                    <span class="badge">
                        {{ $hazard['hazard_class'] ?? '' }}
                        {{ $hazard['category'] ?? '' }}
                        {{ $hazard['h_statement'] ?? '' }}
                    </span>
                @empty
                    <div>
                        {{ $SL['unclassified'] }}
                    </div>
                @endforelse
            </div>

            <div class="sub">
                <div class="sub-title">2.2 {{ $SL['2.2'] }}</div>

                <div>
                    <strong>{{ $SL['signal'] }}:</strong>
                    {{ $signalWord ?: $SL['none'] }}
                </div>

                <div>
                    <strong>{{ $SL['pictograms'] }}:</strong>
                    @if(count($pictogramCodes))
                      <div class="ghs-images">
                        @foreach($pictogramCodes as $ghsCode)
                          <div class="ghs-item">
                            @if(isset($ghsImageData[$ghsCode]))
                              <img src="{{ $ghsImageData[$ghsCode] }}" alt="{{ $ghsCode }}" class="ghs-pictogram" loading="eager" referrerpolicy="no-referrer">
                            @else
                              <span class="ghs-missing">{{ $ghsCode }} — {{ $sdsLang === 'tr' ? 'görsel dosyası eksik' : 'pictogram file missing' }}</span>
                            @endif
                          </div>
                        @endforeach
                      </div>
                    @else
                      {{ count($pictograms) ? implode(', ', $pictograms) : $SL['none'] }}
                    @endif
                </div>

                <div>
                    <strong>{{ $SL['hazards'] }}:</strong>
                    {{ count($hazardStatements) ? implode(', ', $hazardStatements) : $SL['none'] }}
                </div>
            </div>

            @if(!empty($s['2.3']['rule_decisions']))
                <div class="sub">
                    <div class="sub-title">2.3 {{ $SL['2.3'] }}</div>

                    @foreach($s['2.3']['rule_decisions'] as $decision)
                        <div class="note">
                            {{ $translateValue($decision['message'] ?? '-') }}
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- 3 --}}
    @php
        $s = $sections[3]['content'] ?? [];
        $components = $s['3.2']['components'] ?? [];
    @endphp
    <section class="section">
        <div class="section-title">
            {{ 3 }}. {{ $L['sections'][3] }}
        </div>

        <div class="section-body">

            <div class="sub">
                <div class="sub-title">3.2 {{ $SL['3.2'] }}</div>

                <table>
                    <thead>
                        <tr>
                            <th style="width:25%">{{ $L['chemical'] }}</th>
                            <th style="width:17%">CAS / EC / Index</th>
                            <th style="width:11%">%</th>
                            <th style="width:25%">{{ $L['classes'] }}</th>
                            <th style="width:22%">SCL / ATE / M-factor / {{ $SL['notes'] }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($components as $component)
                            @php
                                $classes = [];

                                foreach(
                                    $component['hazard_classifications'] ?? []
                                    as $class
                                ) {
                                    $classes[] = $class;
                                }

                                foreach(
                                    $component['h_statements'] ?? []
                                    as $h
                                ) {
                                    $classes[] = $h;
                                }

                                // Preserve ingredient hazard codes independently of final mixture classification.
                                $ingredientCodes = [];
                                $ingredientInputs = [
                                    $component['h_statements'] ?? [],
                                    $component['hazard_statements'] ?? [],
                                    $component['hazard_classifications'] ?? [],
                                ];
                                foreach ($ingredientInputs as $input) {
                                    foreach ((array) $input as $entry) {
                                        $flat = is_scalar($entry) ? (string) $entry : json_encode($entry, JSON_UNESCAPED_UNICODE);
                                        if (preg_match_all('/\b(?:EUH\s*\d{3}[A-Z]*|H\s*\d{3}[A-Z]*)\b/i', $flat ?: '', $matches)) {
                                            foreach ($matches[0] as $match) {
                                                $ingredientCodes[] = strtoupper(preg_replace('/\s+/', '', $match));
                                            }
                                        }
                                    }
                                }
                                $ingredientCodes = array_values(array_unique($ingredientCodes));
                                foreach ($ingredientCodes as $code) {
                                    $classes[] = $code;
                                }

                                $special = [];

                                foreach(
                                    $component['specific_concentration_limits'] ?? []
                                    as $limit
                                ) {
                                    $special[] = 'SCL: ' . $limit;
                                }

                                foreach(
                                    $component['ate_data'] ?? []
                                    as $ate
                                ) {
                                    $special[] =
                                        'ATE: '
                                        . (
                                            is_array($ate)
                                                ? json_encode(
                                                    $ate,
                                                    JSON_UNESCAPED_UNICODE |
                                                    JSON_UNESCAPED_SLASHES
                                                )
                                                : $ate
                                        );
                                }

                                if(
                                    !empty(
                                        $component['m_factor_acute']
                                    ) &&
                                    (float) $component['m_factor_acute'] !== 1.0
                                ) {
                                    $special[] =
                                        'M acute: '
                                        . $component['m_factor_acute'];
                                }

                                if(
                                    !empty(
                                        $component['m_factor_chronic']
                                    ) &&
                                    (float) $component['m_factor_chronic'] !== 1.0
                                ) {
                                    $special[] =
                                        'M chronic: '
                                        . $component['m_factor_chronic'];
                                }

                                foreach(
                                    $component['notes'] ?? []
                                    as $note
                                ) {
                                    $special[] = $translateValue($note);
                                }

                                $min =
                                    $component['effective_concentration_min']
                                    ?? null;

                                $max =
                                    $component['effective_concentration_max']
                                    ?? null;

                                $range = '-';

                                if($min !== null && $max !== null) {
                                    $range =
                                        abs((float)$min - (float)$max) < 0.000001
                                            ? rtrim(rtrim(number_format((float)$max, 3, '.', ''), '0'), '.') . '%'
                                            : rtrim(rtrim(number_format((float)$min, 3, '.', ''), '0'), '.')
                                              . ' - '
                                              . rtrim(rtrim(number_format((float)$max, 3, '.', ''), '0'), '.')
                                              . '%';
                                }
                            @endphp

                            <tr>
                                <td>
                                    <strong>
                                        {{ $component['chemical_name'] ?? '-' }}
                                    </strong>
                                </td>

                                <td class="small">
                                    CAS:
                                    {{ $component['cas_no'] ?? '-' }}
                                    <br>
                                    EC:
                                    {{ $component['ec_no'] ?? '-' }}
                                    <br>
                                    Index:
                                    {{ $component['index_no'] ?? '-' }}
                                </td>

                                <td>
                                    {{ $range }}
                                </td>

                                <td>
                                    {{ count($classes)
                                        ? implode('; ', array_unique($classes))
                                        : $L['notClassified']
                                    }}
                                </td>

                                <td>
                                    {{ count($special)
                                        ? implode('; ', $special)
                                        : '-'
                                    }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </section>

    {{-- Generic sections 4-16 --}}
    @foreach(range(4,16) as $no)
        @php
            $section =
                $sections[$no]
                ?? [];

            $content =
                $section['content']
                ?? [];
            // Hide only the long Sources lists in Sections 15 and 16.
            // Regulatory source records remain in the database and SDS calculations.
            if (in_array($no, [15, 16], true)) {
                unset($content['sources']);
            }
            if ($no === 16) {
                // Administrative draft metadata is not part of the visible Section 16.
                // Real selected preparer information is printed below.
                unset(
                    $content['revision'],
                    $content['prepared_by'],
                    $content['preparedby'],
                                        $content['version'],
                    $content['issue_date'],
                    $content['revision_date'],
                    $content['change_summary']
                );
            }
        @endphp

        <section class="section">
            <div class="section-title">
                {{ $no }}.
                {{ $L['sections'][$no] ?? ($section['title'] ?? '') }}
            </div>

            <div class="section-body">

                @foreach($content as $subNo => $subContent)

                    @if($no === 8 && $subNo === '8.1' && is_array($subContent))
                        <div class="sub">
                          <div class="sub-title">8.1 {{ $sdsLang === 'tr' ? 'Kontrol parametreleri' : 'Control parameters' }}</div>
                          @php
                             $limits = $subContent['verified_substance_limits'] ?? [];
                             $groups = collect($limits)->groupBy(fn($r) => ($r['cas_no'] ?? '') . '|' . ($r['substance_name'] ?? ''));
                          @endphp
                          @forelse($groups as $rows)
                            @php $first = $rows->first(); @endphp
                            <div class="exposure-substance">{{ $first['substance_name'] ?: $first['cas_no'] }} (CAS: {{ $first['cas_no'] }})</div>
                            <table class="exposure-table">
                              <thead><tr>
                                <th>{{ $sdsLang === 'tr' ? 'Tür' : 'Type' }}</th>
                                <th>{{ $sdsLang === 'tr' ? 'Değer' : 'Value' }}</th>
                                <th>{{ $sdsLang === 'tr' ? 'Kapsam / Etki' : 'Population / Effect' }}</th>
                                <th>{{ $sdsLang === 'tr' ? 'Kaynak' : 'Source' }}</th>
                              </tr></thead>
                              <tbody>
                              @foreach($rows as $r)
                                <tr>
                                  <td>{{ $r['parameter_type'] }} – {{ $r['route_or_medium'] }}</td>
                                  <td>{{ $r['value_text'] }} {{ $r['unit'] }}</td>
                                  <td>{{ $r['population'] }} {{ $r['effect'] }}</td>
                                  <td>{{ $r['source_name'] }}{{ $r['source_date'] ? ' / '.$r['source_date'] : '' }}</td>
                                </tr>
                              @endforeach
                              </tbody>
                            </table>
                          @empty
                            <div class="muted">{{ $sdsLang === 'tr' ? 'Eşleşen kimyasallar için doğrulanmış OEL/DNEL/PNEC kaydı bulunamadı; uygulanabilir sınırlar ayrıca kontrol edilmelidir.' : 'No verified OEL/DNEL/PNEC records are available for the matched substances. Applicable exposure limits must be checked separately.' }}</div>
                          @endforelse
                        </div>
                    @elseif($subNo === 'sources')
                        <div class="sub">
                            <div class="sub-title">
                                {{ $L['sources'] }}
                            </div>

                            @foreach($subContent as $source)
                                <div class="small">
                                    {{ $source['source_name'] ?? $source['source_type'] ?? '-' }}
                                    @if(!empty($source['source_version']))
                                        | {{ $source['source_version'] }}
                                    @endif
                                    @if(!empty($source['source_reference']))
                                        | {{ $source['source_reference'] }}
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @elseif(is_array($subContent))
                        <div class="sub">
                            <div class="sub-title">
                                {{ is_numeric(str_replace('.', '', (string)$subNo)) ? $subNo : $fieldLabel($subNo) }}
                            </div>

                            @foreach($subContent as $key => $value)

                                @if(is_array($value))
                                    <div class="kv">
                                        <div class="k">
                                            {{ $fieldLabel($key) }}
                                        </div>

                                        <div>
                                            @foreach($value as $itemKey => $item)
                                                <div>
                                                    {{ is_string($itemKey) ? $itemKey . ': ' : '' }}
                                                    {{ $prettyValue($translateValue($item)) }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="kv">
                                        <div class="k">
                                            {{ $fieldLabel($key) }}
                                        </div>

                                        <div>
                                            {{ $prettyValue($translateValue($value)) }}
                                        </div>
                                    </div>
                                @endif

                            @endforeach
                        </div>
                    @else
                        <div>
                            {{ $translateValue($subContent) }}
                        </div>
                    @endif

                @endforeach
                @if($no === 16)
                  @php
                    $allCodes = [];
                    foreach ($sections[3]['content']['3.2']['components'] ?? [] as $item) {
                        foreach (['h_statements','hazard_statements','hazard_classifications'] as $key) {
                            foreach ((array) ($item[$key] ?? []) as $value) {
                                $valueText = is_scalar($value) ? (string)$value : json_encode($value, JSON_UNESCAPED_UNICODE);
                                if (preg_match_all('/\b(?:EUH\s*\d{3}[A-Z]*|H\s*\d{3}[A-Z]*)\b/i', $valueText ?: '', $matches)) {
                                    foreach ($matches[0] as $m) $allCodes[] = strtoupper(preg_replace('/\s+/', '', $m));
                                }
                            }
                        }
                    }
                    $allCodes = array_values(array_unique($allCodes));
                    sort($allCodes);
                    $hDictionary = config('sds.h_statements', []);
                  @endphp
                  @if(count($allCodes))
                    <div class="sub">
                      <div class="sub-title">{{ $sdsLang === 'tr' ? 'Bileşenlere ait H / EUH kodları' : 'Ingredient H / EUH statements' }}</div>
                      <table><tbody>
                        @foreach($allCodes as $code)
                          @php
                            $description = $hDictionary[$code] ?? null;
                            // Do not show English-only descriptions in TR as though translated.
                            if ($sdsLang === 'tr' && is_string($description)) $description = null;
                          @endphp
                          <tr><td style="width:70px"><strong>{{ $code }}</strong></td><td>{{ $description ?: ($sdsLang === 'tr' ? 'Bileşen zararlılık kodu (açıklama doğrulanmalı)' : 'Ingredient hazard code (description not available)') }}</td></tr>
                        @endforeach
                      </tbody></table>
                    </div>
                  @endif

                  <div class="sub" style="border-top:1px solid #777;padding-top:8px;margin-top:10px">
                    <div class="sub-title">{{ $L['control'] }}</div>
                    @if($preparer)
                      <div><strong>{{ $L['prepared'] }}:</strong> {{ $preparer->name }} {{ $preparer->title ? ' / '.$preparer->title : '' }}</div>
                      <div><strong>{{ $L['cert'] }}:</strong> {{ $preparer->certificate_number ?: '—' }}</div>
                      <div><strong>{{ $L['issuer'] }}:</strong> {{ $preparer->issuer ?: '—' }}</div>
                      <div><strong>{{ $L['expiry'] }}:</strong> {{ $preparer->expires_at?->format('d.m.Y') ?? '—' }}</div>
                    @endif

                  </div>
                @endif

            </div>
        </section>
    @endforeach

</div>
<div class="print-footer">
    {{ $productName }} | {{ $sdsLang === 'tr' ? 'Güvenlik Bilgi Formu' : 'Safety Data Sheet' }}
    
</div>

</body>
</html>
