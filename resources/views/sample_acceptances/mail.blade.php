<p>Merhaba {{ $sample->customer->contact_person ?? 'Yetkili' }},</p>

<p>Numune raporunuz hazırlanmıştır. Özet bilgileri aşağıda bulabilirsiniz:</p>

<ul>
    <li><strong>Firma:</strong> {{ $sample->company_name }}</li>
    <li><strong>Ürün:</strong> {{ $sample->labProduct->product_name ?? '-' }}</li>
    <li><strong>Batch No:</strong> {{ $sample->batch_no }}</li>
    <li><strong>Rapor No:</strong> {{ $sample->report_no }}</li>
    <li><strong>Rapor Tarihi:</strong> {{ $sample->report_date?->format('d.m.Y') ?? '-' }}</li>
    <li><strong>Açıklama:</strong> {{ $sample->report_description ?? '-' }}</li>
</ul>

<h4>Parametreler, Metotlar ve Sonuçlar (Özet)</h4>
<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse; width:100%;">
    <thead style="background:#f0f0f0;">
        <tr>
            <th>Parametre</th>
            <th>Metot</th>
            <th>Sonuç</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sample->parameters as $param)
            <tr>
                <td>{{ $param->parameter }}</td>
                <td>{{ $param->method_tr ?? '-' }}</td>
                <td>{{ $param->pivot->result ?? 'Bekliyor' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p>Saygılar,<br><strong>ALKİM Petrokimya Laboratuvar ERP v0.2</strong></p>
