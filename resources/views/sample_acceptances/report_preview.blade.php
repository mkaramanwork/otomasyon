<!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<title>Deney Raporu - {{ $sample->report_no }}</title>
<style>
@page { size:A4 portrait; margin:12mm 9mm 8mm 9mm; }
* { box-sizing:border-box; }
html, body { margin:0; padding:0; background:#fff; color:#171717; font-family:DejaVu Sans,Arial,sans-serif; font-size:8px; }
.toolbar { margin:6px auto; padding:9px; max-width:192mm; display:flex; justify-content:space-between; background:#fff; }
.btn {display:inline-block; background:#17466c;color:#fff;padding:7px 10px;text-decoration:none;}
.sheet {width:192mm;max-width:192mm; margin:0 auto; padding:0; background:#fff; border:0; box-shadow:none; height:auto; min-height:0; }
.report-page-top-space{height:1.8mm;}
.report-body {width:100%;padding:0;}
.report-block {width:100%;margin:0 0 1.4mm 0;page-break-inside:auto;}
.header {height:32mm;position:relative;text-align:center;}
.header h2 {margin:0;padding-top:2mm;font-size:12px;}
.header p {margin:0.6mm 0;font-size:7px;}
.company-logo {position:absolute;left:0;top:0;object-fit:contain;object-position:left top;}
.right-mark {position:absolute;right:0;top:0;text-align:center;}
.right-mark img {display:block;width:100%;height:auto;object-fit:contain;margin:0 auto .5mm;}
.serial {width:100%;border:1px solid #444; padding:.6mm;font-size:6.2px;line-height:1.2;word-break:break-word;}
.serial+.serial {border-top:0;}
.meta,.results,.foot-sign {border-collapse:collapse;table-layout:fixed;width:100%;}
.meta td {border:1px solid #777;padding:1.05mm 1.4mm;vertical-align:middle;font-size:7px;line-height:1.15;overflow-wrap:anywhere;}
.meta td:first-child {width:29%;font-weight:bold;}
.meta .en {display:block;font-size:5.9px;font-weight:normal;}
.remarks-block,.independent-fixed-text {border:1px solid #777; padding:1.3mm 1.6mm; margin:1mm 0; font-size:6.8px;line-height:1.23;white-space:pre-line;overflow-wrap:anywhere;}
.remarks-heading {font-weight:bold;margin-bottom:.7mm;}
.results-title {font-size:7.6px;font-weight:bold;margin:1.3mm 0 .9mm;}
.results th,.results td {border:1px solid #666;padding:.9mm .8mm;vertical-align:middle;font-size:6.6px;line-height:1.19;overflow-wrap:anywhere;}
.results th {background:#f5f5f5;text-align:center;font-size:6.4px;}
.results thead {display:table-header-group;}
.results tr {page-break-inside:avoid;}
.results td:nth-child(n+3) {text-align:center;}
.foot-sign {margin-top:2mm;text-align:center;page-break-inside:avoid;}
.foot-sign td {width:25%;padding:.7mm;font-size:6px;vertical-align:top;overflow-wrap:anywhere;}
.foot-sign .caption {height:7mm;}
.sig,.seal-img {max-width:100%;object-fit:contain;}
.sig-space {height:8mm;}
.footer {border-top:1px solid #777;margin-top:1.4mm;padding-top:.8mm;font-size:6px;page-break-inside:avoid;}
.footer p {margin:.5mm 0;}
.footer .page-num {float:right;}
.draft {position:fixed;top:47%;left:20%;transform:rotate(-30deg);color:rgba(130,0,0,.17);font-size:24px;pointer-events:none;}
@media print { .toolbar {display:none!important;} .sheet{width:100%;max-width:100%;margin:0;padding:0;min-height:0!important;height:auto!important;box-shadow:none!important;page-break-after:auto!important;} body{background:white;} }

/* V12: PDF motorunun yuzde kolonlari yeniden dagitmasini onle. */
.results.fixed-results { width:192mm!important; max-width:192mm!important; table-layout:fixed!important; border-collapse:collapse!important; border-spacing:0!important; }
.results.fixed-results th, .results.fixed-results td { box-sizing:border-box!important; overflow-wrap:break-word; word-wrap:break-word; padding:0.8mm 0.65mm!important; }
.results.fixed-results th {font-size:6.3px!important; line-height:1.2!important;}
.results.fixed-results td {font-size:6.6px!important; line-height:1.18!important;}
</style>
<style>
.company-logo{width:{{ min(50,max(10,(float)($settings->logo_width_mm ?? 38))) }}mm;max-height:{{ min(25,max(5,(float)($settings->logo_height_mm ?? 20))) }}mm;}
.right-mark{width:{{ min(30,max(12,(float)($settings->mark_width_mm ?? 23))) }}mm;}
.right-mark img{max-height:{{ min(24,max(10,(float)($settings->mark_height_mm ?? 21))) }}mm;}
.sig{width:{{ min(40,max(10,(float)($settings->signature_width_mm ?? 30))) }}mm;max-height:{{ min(15,max(5,(float)($settings->signature_height_mm ?? 13))) }}mm;}
.seal-img{width:{{ min(40,max(10,(float)($settings->stamp_width_mm ?? 32))) }}mm;max-height:{{ min(18,max(5,(float)($settings->stamp_height_mm ?? 17))) }}mm;}
.report-body{font-size:{{ min(9,max(7,(float)($reportTemplate['font_size'] ?? 8))) }}px;}
.meta td,.results td,.results th{padding:{{ min(1.6,max(.6,(float)($reportTemplate['cell_padding_mm'] ?? 1))) }}mm;}
.header{height:{{ min(40,max(27,(float)($reportTemplate['header_height_mm'] ?? 32))) }}mm;}
.report-block{margin-bottom:{{ min(3,max(.5,(float)($reportTemplate['section_gap_mm'] ?? 1.4))) }}mm;}
</style>
<style>
/* V10 - Ekran ve DOMPDF icin ayni mm tabanli baslik olculeri */
.header-layout { width:100%; border-collapse:collapse; table-layout:fixed; border:0; margin:0; padding:0; }
.header-layout > tbody > tr > td { border:0!important; padding:0!important; vertical-align:top; }
.header-layout > tbody > tr > td.header-title-cell { text-align:center; padding-top:2mm!important; }
.header-layout > tbody > tr > td.header-brand-cell { text-align:right; }
.company-logo-fixed {display:block; width:{{ min(50,max(10,(float)($settings->logo_width_mm ?? 38))) }}mm; height:auto; max-height:{{ min(25,max(5,(float)($settings->logo_height_mm ?? 20))) }}mm;}
.report-title{font-size:12px;font-weight:bold;line-height:1.3;}
.report-subtitle{font-size:7px;line-height:1.3;}
/* Marka ve 3 kutu AYNI sabit genislikte; DOMPDF width auto kullanmaz */
.brand-frame {width:{{ min(30,max(12,(float)($settings->mark_width_mm ?? 23))) }}mm!important; margin:0 0 0 auto!important; padding:0!important; border:0!important;}
.brand-image {display:block!important; width:100%!important; max-width:100%!important; height:auto!important; max-height:{{ min(24,max(10,(float)($settings->mark_height_mm ?? 21))) }}mm!important; margin:0 0 0.35mm 0!important; padding:0!important; border:0!important;}
.brand-serial-table {border-collapse:collapse!important; table-layout:fixed!important; width:100%!important; max-width:100%!important; margin:0!important; padding:0!important; border-spacing:0!important;}
.brand-serial-table td {width:100%!important; box-sizing:border-box!important; border:0.2mm solid #444!important; padding:0.5mm 0.2mm!important; font-size:6.2px!important; line-height:1.15!important; text-align:center!important; overflow-wrap:break-word;}
.brand-serial-table tr + tr td {border-top:0!important;}
/* Ust bilgiyi taslakta sabit tut; sonraki tablo markanin altina tasmasin */
.report-header-fixed {height:{{ min(42,max(33,(float)($reportTemplate['header_height_mm'] ?? 35))) }}mm!important; min-height:{{ min(42,max(33,(float)($reportTemplate['header_height_mm'] ?? 35))) }}mm!important; overflow:visible; page-break-inside:avoid; margin-top:0!important;}
.header-layout,.header-layout tr,.header-layout td,.brand-serial-table {page-break-inside:avoid!important;}
.sheet,.report-body {border:0!important;outline:0!important;box-shadow:none!important;background:#fff!important;}
@media print {
 html,body {width:auto!important;margin:0!important;padding:0!important;}
 .sheet {width:192mm!important;max-width:192mm!important;margin:0 auto!important;}
 .toolbar {display:none!important;}
}
</style>
</head>
<body>
@php
  // Müşteri adı ve adresi artık doğrudan mevcut lab-report/settings kayıtlarından gelir.
  $latestAnalysis = $sample->parameters->filter(fn ($p) => !empty($p->pivot->result_date))
      ->max(fn ($p) => \Carbon\Carbon::parse($p->pivot->result_date)->timestamp);
  $testDate = $latestAnalysis ? \Carbon\Carbon::createFromTimestamp($latestAnalysis)->format('d.m.Y') : '-';
  $reportDateText = $sample->report_date ? \Carbon\Carbon::parse($sample->report_date)->format('d.m.Y') : '-';
@endphp
@if(!$pdfMode)
<div class="toolbar">
  <div><strong>FRM.044 – Deney Raporu Önizleme</strong><br><small>{{ $approved ? 'Onaylı rapor' : 'Taslak – henüz onaylanmadı' }}</small></div>
  <div><a class="btn" href="{{ route('lab-report.settings') }}">Rapor Ayarları</a> @can("manage-lab-report-settings")<a class="btn" href="{{ route('lab-report.template.edit') }}">Ortak Şablon Editörü</a>@endcan <a class="btn" href="{{ route('samples.report.preview', ['sample' => $sample->id, 'download' => 'pdf']) }}">PDF İndir</a></div>
</div>
@endif
<div class="sheet">
<div class="report-page-top-space"></div>
<div class="report-body">
  @foreach(($reportTemplate["order"] ?? ["header","sample","remarks","fixed_text","results","signatures","footer"]) as $block)
    @include("sample_acceptances._report_" . $block)
  @endforeach






@if(!$approved)<div class="draft">ONAYSIZ TASLAK</div>@endif
</div>
</div>
@if(!$pdfMode && $canApprove && !$approved)
<div class="toolbar">
  <form method="POST" action="{{ route('samples.report.approve', $sample->id) }}">
    @csrf
    <label><input type="checkbox" name="confirm" value="1" required> Raporun tüm bilgilerini, kapsamını ve yayım yetkisini doğruladım.</label>
    <button class="btn" type="submit">Raporu Yetkili Olarak Onayla</button>
  </form>
</div>
@endif
</body>
</html>
