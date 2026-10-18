{{-- PDF ve tarayicida ayni kutu genisligi icin tablo yerlesimi. --}}
<div class="report-block report-header-fixed">
<table class="header-layout" role="presentation" cellspacing="0" cellpadding="0">
  <colgroup><col style="width:28%"><col style="width:44%"><col style="width:28%"></colgroup>
  <tr>
    <td class="header-logo-cell">
      @if($logoDataUri)
        <img class="company-logo-fixed" src="{{ $logoDataUri }}" alt="Kurumsal logo">
      @endif
    </td>
    <td class="header-title-cell" style="text-align:center; vertical-align:top;">
      <div class="report-title">DENEY RAPORU</div>
      <div class="report-subtitle">Test Report</div>
    </td>
    <td class="header-brand-cell">
      <div class="brand-frame">
        @if($markDataUri)
          <img class="brand-image" src="{{ $markDataUri }}" alt="Akreditasyon markası">
        @endif
        <table class="brand-serial-table" cellspacing="0" cellpadding="0" role="presentation">
          <tr><td>{{ $markDataUri ? ($settings->accreditation_number ?: '-') : '-' }}</td></tr>
          <tr><td>{{ $sample->report_no ?: '-' }}</td></tr>
          <tr><td>{{ $sample->report_date ? \Carbon\Carbon::parse($sample->report_date)->format('y-m') : '-' }}</td></tr>
        </table>
      </div>
    </td>
  </tr>
</table>
</div>
