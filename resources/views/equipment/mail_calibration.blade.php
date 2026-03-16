<p>{{ $equipment->name }} için yeni kalibrasyon kaydı eklendi.</p>
<ul>
  <li>Sertifika No: {{ $calibration->certificate_no }}</li>
  <li>Sağlayıcı: {{ $calibration->provider }}</li>
  <li>Tarih: {{ $calibration->performed_at }}</li>
  <li>Geçerlilik: {{ $calibration->valid_until }}</li>
</ul>