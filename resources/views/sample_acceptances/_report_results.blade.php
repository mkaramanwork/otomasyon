{{-- V15: DomPDF sabit 7 sutun; HIC colspan, rowspan veya ic ice table yok. --}}
@php
    // Boyutlar tek satirda yuzde olarak tanimlidir; toplam tam 100.
    $widths = [55, 17, 8, 3, 3, 3, 11];
    $limitsFor = function ($parameter) use ($sample) {
        return $sample->labProduct?->parameters?->firstWhere('parameter_id', $parameter->id);
    };
@endphp
<div class="results-title">Tablo 1. Analiz Sonuçları / Table 1. Analysis Results</div>
<table class="results frm-results-v15" cellpadding="0" cellspacing="0" style="width:100%!important; max-width:100%!important; table-layout:fixed!important; border-collapse:collapse!important; border-spacing:0!important; margin:0!important;">
    <colgroup>
        @foreach($widths as $w)
            <col style="width:{{ $w }}%!important;">
        @endforeach
    </colgroup>
    <thead>
        {{-- Tek seviye baslik: DomPDF kolon genisliklerini birlestirmesin. --}}
        <tr>
            <th style="width:55%!important;">ANALİZLER<br>(TESTS)</th>
            <th style="width:17%!important;">ANALİZ METODU<br>(ANALYSIS METHOD)</th>
            <th style="width:8%!important;">BİRİM<br>(UNIT)</th>
            <th style="width:3%!important; padding:.5mm .1mm!important; font-size:5.3px!important;">Max</th>
            <th style="width:3%!important; padding:.5mm .1mm!important; font-size:5.3px!important;">Min</th>
            <th style="width:3%!important; padding:.5mm .1mm!important; font-size:5.3px!important;">Net</th>
            <th style="width:11%!important;">SONUÇ<br>(RESULT)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sample->parameters as $param)
            @php
                $lim = $limitsFor($param);
                $accredited = trim((string) ($param->scope ?? '')) === 'TÜRKAK';
                // Mevcut sistemin veri eslestirmesini aynen koru.
                $analysisTr = trim((string) ($param->method_tr ?? ''));
                $analysisEn = trim((string) ($param->method_en ?? ''));
                $methodTr   = trim((string) ($param->parameter ?? ''));
                $methodEn   = trim((string) ($param->parameter_en ?? $param->name_en ?? ''));
            @endphp
            <tr style="page-break-inside:avoid!important;">
                <td style="width:55%!important; text-align:center!important; vertical-align:middle!important; overflow-wrap:break-word!important; word-wrap:break-word!important;">
                    {{ $analysisTr !== '' ? $analysisTr : '-' }}@if($accredited) * @endif
                    @if($analysisEn !== '' && $analysisEn !== $analysisTr)
                        <div class="en">{{ $analysisEn }}</div>
                    @endif
                </td>
                <td style="width:17%!important; text-align:center!important; vertical-align:middle!important; overflow-wrap:break-word!important; word-wrap:break-word!important;">
                    {{ $methodTr !== '' ? $methodTr : '-' }}
                    @if($methodEn !== '' && $methodEn !== $methodTr)
                        <div class="en">{{ $methodEn }}</div>
                    @endif
                </td>
                <td style="width:8%!important; text-align:center!important;">{{ $param->unit ?: '-' }}</td>
                <td style="width:3%!important; text-align:center!important; padding:.7mm .1mm!important; font-size:5.8px!important;">{{ $lim->max_value ?? '-' }}</td>
                <td style="width:3%!important; text-align:center!important; padding:.7mm .1mm!important; font-size:5.8px!important;">{{ $lim->min_value ?? '-' }}</td>
                <td style="width:3%!important; text-align:center!important; padding:.7mm .1mm!important; font-size:5.8px!important;">{{ $lim->net_value ?? '-' }}</td>
                <td style="width:11%!important; text-align:center!important;"><strong>{{ $param->pivot->result ?? '-' }}</strong></td>
            </tr>
        @endforeach
    </tbody>
</table>
