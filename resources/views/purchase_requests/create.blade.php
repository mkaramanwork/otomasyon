@extends('layouts.admin')

@section('content')
<div class="container">
    <h4>Satınalma Talebi Oluştur</h4>

    <form action="{{ route('purchase-requests.store') }}" method="POST">
        @csrf

        <div id="items-container">
            <div class="item-row mb-3 border p-2">
                <label>Ürün</label>
                <select name="items[0][inventory_id]" class="form-control inventory-select" required>
                    @foreach($inventories as $inv)
                        <option value="{{ $inv->id }}"
                                data-spec-title="{{ $inv->technicalSpecification->spec_title ?? '' }}"
                                data-spec-content="{{ $inv->technicalSpecification->spec_content ?? '' }}">
                            {{ $inv->product_name }}
                        </option>
                    @endforeach
                </select>

                <div class="mt-2 spec-area">
                    <strong>Teknik Şartname Başlığı:</strong> <span class="spec-title"></span><br>
                    <strong>İçerik:</strong> <span class="spec-content"></span>
                </div>

                <label>Adet</label>
                <input type="number" name="items[0][quantity]" class="form-control" required>

                <label>Birim</label>
                <select name="items[0][unit]" class="form-control" required>
                    <option value="kg">kg</option>
                    <option value="lt">lt</option>
                    <option value="adet">adet</option>
                </select>

                <label>Açıklama</label>
                <textarea name="items[0][description]" class="form-control"></textarea>

                <label>Tedarikçi</label>
                <select name="items[0][supplier_id]" class="form-control">
                    <option value="">Seçiniz</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <button type="button" id="add-item" class="btn btn-secondary">+ Ürün Ekle</button>
        <button type="submit" class="btn btn-success">Talep Oluştur</button>
    </form>
</div>

<script>
let itemIndex = 1;
document.getElementById('add-item').addEventListener('click', function(){
    let container = document.getElementById('items-container');
    let newRow = container.firstElementChild.cloneNode(true);

    newRow.querySelectorAll('select, input, textarea').forEach(el => {
        let name = el.getAttribute('name');
        el.setAttribute('name', name.replace(/\d+/, itemIndex));
        el.value = '';
    });

    container.appendChild(newRow);
    itemIndex++;
});

document.addEventListener('change', function(e){
    if(e.target.classList.contains('inventory-select')){
        let option = e.target.selectedOptions[0];
        let specTitle = option.getAttribute('data-spec-title');
        let specContent = option.getAttribute('data-spec-content');
        let specArea = e.target.closest('.item-row').querySelector('.spec-area');
        specArea.querySelector('.spec-title').textContent = specTitle;
        specArea.querySelector('.spec-content').innerHTML = specContent; // içerik HTML olarak basılıyor
    }
});
</script>
@endsection