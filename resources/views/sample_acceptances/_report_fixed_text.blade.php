{{-- Raporun Açıklamalar bölümünden tamamen bağımsız ortak metin bloğu --}}
@if(trim((string)($reportTemplate['fixed_text'] ?? '')) !== '')
<section class="independent-fixed-text" style="border:1px solid #777;padding:1.1mm 1.5mm;margin:1mm 0;white-space:pre-line;overflow-wrap:anywhere;page-break-inside:avoid;font-size:inherit;line-height:1.3;">{{ $reportTemplate['fixed_text'] }}</section>
@endif
