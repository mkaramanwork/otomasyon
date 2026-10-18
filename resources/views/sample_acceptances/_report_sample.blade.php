<table class="meta">
    <tr><td>Müşterinin Adı <span class="en">Customer Name</span></td><td>{{ trim((string)($settings->laboratory_name ?? '')) !== '' ? $settings->laboratory_name : '-' }}</td></tr>
    <tr><td>Müşterinin Adresi <span class="en">Customer Address</span></td><td>{{ trim((string)($settings->address ?? '')) !== '' ? $settings->address : '-' }}</td></tr>
    <tr><td>Numunenin Adı ve Tarifi <span class="en">Name and Description of the Sample</span></td><td>{{ $sample->labProduct->product_name ?? '-' }}</td></tr>
    <tr><td>Numunenin Parti Numarası <span class="en">Batch Number of the Sample</span></td><td>{{ $sample->batch_no ?: '-' }}</td></tr>
    <tr><td>Numunenin Alınma Tarihi <span class="en">Date of Sampling</span></td><td>{{ $sample->taken_at ? \Carbon\Carbon::parse($sample->taken_at)->format('d.m.Y H:i') : '-' }}</td></tr>
    <tr><td>Numunenin Kabul Tarihi <span class="en">Date of Receipt of the Item</span></td><td>{{ $sample->created_at ? \Carbon\Carbon::parse($sample->created_at)->format('d.m.Y H:i') : '-' }}</td></tr>
    <tr><td>Numune Miktarı <span class="en">Sample Quantity</span></td><td>{{ $sample->amount ?? '-' }}</td></tr>
    <tr><td>Deneyin Yapıldığı Tarih <span class="en">Date of Test</span></td><td>{{ $testDate }}</td></tr>
    <tr><td>Rapor No <span class="en">Report No</span></td><td>{{ $sample->report_no ?: '-' }}</td></tr>

  </table>

  