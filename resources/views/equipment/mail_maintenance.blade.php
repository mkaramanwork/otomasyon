<p>{{ $equipment->name }} için yeni bakım kaydı eklendi.</p>
<ul>
  <li>Tür: {{ $maintenance->type }}</li>
  <li>Tarih: {{ $maintenance->performed_at }}</li>
  <li>Sonuç: {{ $maintenance->result }}</li>
</ul>