<p>Merhaba,</p>
<p><strong>{{ $equipment->name }}</strong> cihazı güncellendi.</p>
<ul>
    <li>Kod: {{ $equipment->tag }}</li>
    <li>Model: {{ $equipment->model }}</li>
    <li>Seri No: {{ $equipment->serial_no }}</li>
    <li>Satın Alma Tarihi: {{ $equipment->purchase_date }}</li>
    <li>Kalibrasyon Tarihi: {{ $equipment->calibration_due_at }}</li>
    <li>Durum: {{ $equipment->status }}</li>
</ul>
<p>Lütfen güncel bilgileri dikkate alınız.</p>