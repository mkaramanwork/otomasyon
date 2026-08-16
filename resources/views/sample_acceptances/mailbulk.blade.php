@foreach($samples as $sample)
<p><strong>Ürün:</strong> {{ $sample->labProduct->product_name ?? '-' }}</p>
<p><strong>Rapor No:</strong> {{ $sample->report_no ?? '-' }}</p>
<p><strong>Kabul Tarihi:</strong> {{ $sample->taken_at?->format('d.m.Y H:i') ?? '-' }}</p>
<p><strong>Kabul Eden:</strong> {{ $sample->acceptedBy->name ?? '-' }}</p>

<h5>Analiz Sonuçları</h5>
<table border="1" cellpadding="5" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Parametre</th>
            <th>Metot</th>
            <th>Sonuç</th>
            <th>Birim</th>
        </tr>
    </thead>
    <tbody>
    @foreach($sample->parameters as $param)
        <tr>
            <td>{{ $param->parameter }}</td>
            <td>{{ $param->method_tr }}</td>
            <td>{{ $param->pivot->result ?? '-' }}</td>
            <td>{{ $param->unit ?? $param->pivot->unit ?? '-' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<hr>
@endforeach

<p style="font-size:12px; color:gray; margin-top:20px;">
    Bu mail <strong>Murat KARAMAN WEP</strong> tarafından otomatik gönderilmiştir. Lütfen cevaplamayınız.
</p>
