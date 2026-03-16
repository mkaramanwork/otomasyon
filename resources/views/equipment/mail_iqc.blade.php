<p>{{ $equipment->name }} için yeni IQC kaydı eklendi.</p>
<ul>
  <li>Tarih: {{ $iqc->date }}</li>
  <li>Materyal: {{ $iqc->material }}</li>
  <li>Değer: {{ $iqc->value }}</li>
  <li>Durum: {{ $iqc->rule_status }}</li>
</ul>