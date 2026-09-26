@extends('layouts.admin')
@section('title','Yeni Teklif Oluştur')

@section('content')
<div class="container">
    <h4 class="mb-3">➕ Yeni Teklif Oluştur</h4>

    <form action="{{ route('offers.store') }}" method="POST">
        @csrf

        <!-- Teklif No -->
        <div class="mb-3">
            <label class="form-label">Teklif No</label>
            <input type="text" name="offer_no" class="form-control" value="{{ $offerNo }}" readonly>
        </div>

        <!-- Firma Seçimi -->
        <div class="mb-3">
            <label class="form-label">Firma</label>
            <select name="customer_id" class="form-control" required>
                <option value="">Firma Seçiniz</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Başlık -->
        <div class="mb-3">
            <label class="form-label">Başlık</label>
            <input type="text" name="title" class="form-control">
        </div>

        <!-- Teklif Metni -->
        <div class="mb-3">
            <label class="form-label">Teklif Metni</label>
            <textarea name="text" class="form-control" rows="4">Sayın ilgili;

Talep etmiş olduğunuz analizlere ilişkin hazırlanan fiyat teklifimiz ilişikte bilgilerinize sunulmuştur. Laboratuvarımız çalışmalarını “TÜRKAK Akreditasyon Belgesi” çerçevesinde gerçekleştirmektedir.</textarea>
        </div>

        <!-- Teklif Tarihi -->
        <div class="mb-3">
            <label class="form-label">Teklif Tarihi</label>
            <input type="date" name="offer_date" class="form-control" required>
        </div>

        <!-- Numune Adedi -->
        <div class="mb-3">
            <label class="form-label">Numune Adedi</label>
            <input type="number" name="sample_count" class="form-control" min="1" required>
        </div>

        <!-- Parametreler -->
        <h5 class="mt-4">Parametreler</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Seç</th>
                    <th>Parametre</th>
                    <th>Metot</th>
                    <th>Fiyat (₺)</th>
                    <th>Kapsam</th>
                </tr>
            </thead>
            <tbody>
                @foreach($parameters as $param)
                <tr>
                    <td>
                        <input type="checkbox" name="parameters[{{ $param->id }}][selected]" value="1" class="param-check">
                        <input type="hidden" name="parameters[{{ $param->id }}][id]" value="{{ $param->id }}">
                    </td>
                    <td>{{ $param->parameter }}</td>
                    <td>
                        <input type="text" name="parameters[{{ $param->id }}][method]" class="form-control" value="{{ $param->method_tr }}">
                    </td>
                    <td>
                        <input type="number" step="0.01" name="parameters[{{ $param->id }}][price]" class="form-control price-input">
                    </td>
                    <td>
                        <input type="text" name="parameters[{{ $param->id }}][scope]" class="form-control" placeholder="TÜRKAK / Kapsam Dışı / Açıklama">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- İndirim Oranı -->
        <div class="mb-3">
            <label class="form-label">İndirim Oranı (%)</label>
            <input type="number" step="0.01" name="discount_rate" id="discountRate" class="form-control" value="0">
        </div>

        <!-- Toplam ve Net Tutar -->
        <div class="mb-3">
            <label class="form-label">Toplam Tutar (₺)</label>
            <input type="text" id="totalAmount" class="form-control" name="total_amount" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label">Net Tutar (₺)</label>
            <input type="text" id="netAmount" class="form-control" name="net_amount" readonly>
        </div>

        <!-- Kaydet -->
        <button type="submit" class="btn btn-primary">💾 Kaydet</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
function calculateTotals(){
    let total = 0;
    document.querySelectorAll('tbody tr').forEach(row => {
        let check = row.querySelector('.param-check');
        let priceInput = row.querySelector('.price-input');
        if(check && check.checked && priceInput && priceInput.value){
            total += parseFloat(priceInput.value);
        }
    });
    let discount = parseFloat(document.getElementById('discountRate').value) || 0;
    let net = total - (total * discount / 100);

    document.getElementById('totalAmount').value = total.toFixed(2);
    document.getElementById('netAmount').value = net.toFixed(2);
}

// Event listeners
document.querySelectorAll('.param-check, .price-input, #discountRate').forEach(el => {
    el.addEventListener('input', calculateTotals);
});
</script>
@endsection
