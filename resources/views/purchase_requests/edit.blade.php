@extends('layouts.admin')

@section('content')
<div class="container">
    <h4>Satınalma Talebi Düzenle</h4>

    <form action="{{ route('purchase-requests.update',$request->id) }}" method="POST">
        @csrf

        <div id="items-container">
            @foreach($request->items as $index => $item)
            <div class="item-row mb-3 border p-2">
                <label>Ürün</label>
                <select name="items[{{ $index }}][inventory_id]" class="form-control inventory-select" required>
                    @foreach($inventories as $inv)
                        <option value="{{ $inv->id }}"
                                data-spec-title="{{ $inv->technicalSpecification->spec_title ?? '' }}"
                                data-spec-content="{!! $inv->technicalSpecification->spec_content ?? '' !!}"
                                @if($item->inventory_id==$inv->id) selected @endif>
                            {{ $inv->product_name }}
                        </option>
                    @endforeach
                </select>

                <div class="mt-2 spec-area">
                    <strong>Teknik Şartname Başlığı:</strong>
                    <span class="spec-title">{{ $item->inventory->technicalSpecification->spec_title ?? '' }}</span><br>
                    <strong>İçerik:</strong>
                    <span class="spec-content">{!! $item->inventory->technicalSpecification->spec_content ?? '' !!}</span>
                </div>

                <label>Adet</label>
                <input type="number" name="items[{{ $index }}][quantity]" class="form-control" value="{{ $item->quantity }}" required>

                <label>Birim</label>
                <input type="text" name="items[{{ $index }}][unit]" class="form-control" value="{{ $item->unit }}" required>

                <label>Açıklama</label>
                <textarea name="items[{{ $index }}][description]" class="form-control">{{ $item->description }}</textarea>

                <label>Tedarikçi</label>
                <select name="items[{{ $index }}][supplier_id]" class="form-control">
                    <option value="">Seçiniz</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" @if($item->supplier_id==$sup->id) selected @endif>
                            {{ $sup->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-success">Güncelle</button>
    </form>
</div>

<script>
document.addEventListener('change', function(e){
    if(e.target.classList.contains('inventory-select')){
        let option = e.target.selectedOptions[0];
        let specTitle = option.getAttribute('data-spec-title');
        let specContent = option.getAttribute('data-spec-content');
        let specArea = e.target.closest('.item-row').querySelector('.spec-area');
        specArea.querySelector('.spec-title').textContent = specTitle;
        specArea.querySelector('.spec-content').innerHTML = specContent;
    }
});
</script>
@endsection