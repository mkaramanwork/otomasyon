<table class="foot-sign">
    <tr><td class="caption"><strong>Mühür</strong><br>(Seal)</td><td class="caption"><strong>Raporun Yayım Tarihi</strong><br>(Date of Issue)</td><td class="caption"><strong>Laboratuvar Personeli</strong><br>(Person in Charge)</td><td class="caption"><strong>Onaylayan / Laboratuvar Müdürü</strong><br>(Approval / Laboratory Manager)</td></tr>
    <tr>
      <td>@if($approved && $stampDataUri)<img class="seal-img" src="{{ $stampDataUri }}" alt="Mühür">@else<div class="sig-space"></div>@endif</td>
      <td>{{ $reportDateText }}</td>
      <td>@if($approved && $preparerSignatureDataUri)<img class="sig" src="{{ $preparerSignatureDataUri }}" alt="Personel imzası">@else<div class="sig-space"></div>@endif<br>{{ $preparer->name ?? 'Belirlenemedi' }}</td>
      <td>@if($approved && $approvalSignatureDataUri)<img class="sig" src="{{ $approvalSignatureDataUri }}" alt="Onaylayan imzası">@else<div class="sig-space"></div>@endif<br>{{ $manager->name ?? 'Tanımlı değil' }}@if($approved && $approvedAt)<br><small>Onay: {{ \Carbon\Carbon::parse($approvedAt)->format('d.m.Y H:i') }}</small>@endif</td>
    </tr>
  </table>
  