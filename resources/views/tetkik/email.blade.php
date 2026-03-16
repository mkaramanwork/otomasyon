<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>İç Tetkik Bildirimi</title>
</head>
<body>
    <h3>İç Tetkik Bildirimi</h3>

    <p><strong>Tetkik No:</strong> {{ $plan->tetkik_no }}</p>
    <p><strong>Tarih:</strong> {{ $plan->tetkik_tarih }}</p>
    <p><strong>Denetçi:</strong> {{ $plan->denetci }}</p>
    <p><strong>Bölüm:</strong> {{ $plan->bolum }}</p>
    <p><strong>Durum:</strong> {{ $plan->durum ?? 'Planlandı' }}</p>

    <hr>

    @if($tip === 'tetkik_planlandi')
        <p>Bu bir <strong>planlama bildirimi</strong> mailidir. İç tetkik planlanmıştır.</p>
    @elseif($tip === 'tetkik_baslatildi')
        <p>Bu bir <strong>başlatma bildirimi</strong> mailidir. İç tetkik başlatılmıştır.</p>
    @elseif($tip === 'tetkik_tamamlandi')
        <p>Bu bir <strong>tamamlanma bildirimi</strong> mailidir. İç tetkik tamamlanmıştır.</p>
    @else
        <p>Tetkik ile ilgili bilgilendirme yapılmaktadır.</p>
    @endif

    <p>Bu mail MKaraman Wep sistemi tarafından otomatik gönderilmiştir.</p>
</body>
</html>