@extends('layouts.admin')

@section('content')
<div class="container">
    <h4>Tedarikçi Değerlendirme: {{ $supplier->name }}</h4>

    <form action="{{ route('suppliers.evaluate.store',$supplier->id) }}" method="POST" id="evaluationForm">
        @csrf

        <div class="mb-3">
            <label>Sertifikasyon Kapsamı</label>
            <select name="certification" class="form-select score-input" data-score="certification" required>
                <option value="Var">Var (25 puan)</option>
                <option value="Yok">Yok (0 puan)</option>
                <option value="Aşamada">Sertifika aşamasında (5 puan)</option>
            </select>
            <input type="text" name="certification_detail" class="form-control mt-2" placeholder="Sertifika bilgisi (var ise)">
        </div>

        <div class="mb-3">
            <label>İzlenebilirlik Yöntemi</label>
            <select name="traceability" class="form-select score-input" data-score="traceability" required>
                <option value="Evet">Evet (25 puan)</option>
                <option value="Hayır">Hayır (0 puan)</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Talep/Şikayet Dönüş Hızı</label>
            <select name="complaint_response" class="form-select score-input" data-score="complaint_response" required>
                <option value="Üç gün altında">3 gün altında (20 puan)</option>
                <option value="Üç gün üzerinde">3 gün üzerinde (0 puan)</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Fiyat ve Ödeme Politikası Uygun mu?</label>
            <select name="pricing_policy" class="form-select score-input" data-score="pricing_policy" required>
                <option value="Evet">Evet (15 puan)</option>
                <option value="Hayır">Hayır (0 puan)</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Teknik Destek ve Bilgilendirme Yeterli mi?</label>
            <select name="technical_support" class="form-select score-input" data-score="technical_support" required>
                <option value="Evet">Evet (15 puan)</option>
                <option value="Hayır">Hayır (0 puan)</option>
            </select>
        </div>

        <!-- Canlı Puan Gösterimi -->
        <div class="alert alert-info">
            <strong>Toplam Puan:</strong> <span id="totalScore">0</span> / 100 <br>
            <strong>Durum:</strong> <span id="statusText">Henüz hesaplanmadı</span>
        </div>

        <button type="submit" class="btn btn-success">Değerlendirmeyi Kaydet</button>
        <a href="{{ route('suppliers.show',$supplier->id) }}" class="btn btn-secondary">Geri</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('evaluationForm');
    const scoreDisplay = document.getElementById('totalScore');
    const statusText = document.getElementById('statusText');

    function calculateScore() {
        let score = 0;

        const cert = form.querySelector('[name="certification"]').value;
        if(cert === 'Var') score += 25;
        else if(cert === 'Aşamada') score += 5;

        const trace = form.querySelector('[name="traceability"]').value;
        if(trace === 'Evet') score += 25;

        const complaint = form.querySelector('[name="complaint_response"]').value;
        if(complaint === 'Üç gün altında') score += 20;

        const pricing = form.querySelector('[name="pricing_policy"]').value;
        if(pricing === 'Evet') score += 15;

        const support = form.querySelector('[name="technical_support"]').value;
        if(support === 'Evet') score += 15;

        scoreDisplay.textContent = score;

        if(score >= 80) statusText.textContent = "Uzun dönem çalışılır (Listeye eklenir)";
        else if(score >= 60) statusText.textContent = "Üç yıl içinde iyileştirme istenir (Listeye eklenir)";
        else statusText.textContent = "Çalışılmaz (İyileştirme istenir)";
    }

    form.querySelectorAll('.score-input').forEach(el => {
        el.addEventListener('change', calculateScore);
    });

    calculateScore(); // sayfa yüklenince hesapla
});
</script>
@endsection