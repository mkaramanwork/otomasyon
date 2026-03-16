<p>Merhaba {{ $sample->customer->contact_person ?? 'Yetkili' }},</p>

<p>Numune raporunuz hazırlanmıştır. Özet bilgileri aşağıda bulabilirsiniz:</p>

<ul>
    <li><strong>Firma:</strong> {{ $sample->company_name }}</li>
    <li><strong>Ürün:</strong> {{ $sample->product->name }}</li>
    <li><strong>Batch No:</strong> {{ $sample->batch_no }}</li>
    <li><strong>Rapor No:</strong> {{ $sample->report_no }}</li>
    <li><strong>Rapor Tarihi:</strong> {{ $sample->report_date?->format('d.m.Y') }}</li>
    <li><strong>Açıklama:</strong> {{ $sample->report_description }}</li>
</ul>

<h4>Parametreler ve Sonuçlar (Özet)</h4>
<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse;">
    <thead>
        <tr>
            <th>Parametre</th>
            <th>Sonuç</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sample->parameters as $param)
            <tr>
                <td>{{ $param->parameter }}</td>
                <td>{{ $param->pivot->result ?? 'Bekliyor' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p>Saygılar,<br>Murat KARAMAN ERP v0.1</p>