<p>Merhaba,</p>
<p><strong>{{ $equipment->name }}</strong> cihazının kalibrasyon süresi kritik seviyeye geldi.</p>
<ul>
    <li>Tag: {{ $equipment->tag }}</li>
    <li>Son Kalibrasyon Geçerlilik Tarihi: {{ $equipment->calibration_valid_until }}</li>
    <li>Kalan Gün: {{ $equipment->days_left }}</li>
</ul>
<p>Lütfen gerekli aksiyonları alınız.</p>