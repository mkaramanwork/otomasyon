<h3>Satınalma Talebi: {{ $request->request_code }}</h3>
<p><strong>Durum:</strong> {{ $request->status }}</p>
<p><strong>Laboratuvar Müdürü Onayı:</strong> {{ $request->lab_manager_name }}</p>

<h4>Ürünler</h4>
<table border="1" cellpadding="5" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Ürün</th>
            <th>Adet</th>
            <th>Birim</th>
            <th>Tedarikçi</th>
            <th>Teknik Şartname</th>
        </tr>
    </thead>
    <tbody>
        @foreach($request->items as $item)
        <tr>
            <td>{{ $item->inventory->product_name }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ $item->unit }}</td>
            <td>{{ $item->supplier->name ?? '-' }}</td>
            <td>
                <strong>{{ $item->inventory->technicalSpecification->spec_title ?? '-' }}</strong><br>
                {!! $item->inventory->technicalSpecification->spec_content ?? '' !!}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<p><strong>Onaylayanlar:</strong></p>
<ul>
    @if($request->general_manager_name)
        <li>Genel Müdür: {{ $request->general_manager_name }}</li>
    @endif
    @if($request->procurement_name)
        <li>Satınalma: {{ $request->procurement_name }}</li>
    @endif
    @if($request->order_name)
        <li>Sipariş: {{ $request->order_name }}</li>
    @endif
    @if($request->control_name)
        <li>Kontrol: {{ $request->control_name }}</li>
    @endif
    @if($request->acceptance_name)
        <li>Kabul: {{ $request->acceptance_name }}</li>
    @endif
</ul>