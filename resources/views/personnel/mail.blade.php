{{-- Tek mail şablonu --}}
@switch($type)

    {{-- Yeni Personel --}}
    @case('new')
        <p>Merhaba,</p>
        <p>Yeni personel eklendi:</p>
        <ul>
            <li>Adı: {{ $personnel->name }}</li>
            <li>Ünvanı: {{ $personnel->title }}</li>
            <li>Görevi: {{ $personnel->duty }}</li>
            <li>İşe Başlama Tarihi: {{ $personnel->start_date }}</li>
            <li>Vekili: {{ $personnel->deputy }}</li>
        </ul>
    @break

    {{-- Oryantasyon Planı --}}
    @case('orientation')
        <p>{{ $personnel->name }} için yeni oryantasyon planı oluşturuldu.</p>
        <p>Bölüm: {{ $plan->department }}<br>
        Amaç: {{ $plan->purpose }}</p>
    @break

    {{-- Oryantasyon Faaliyeti --}}
    @case('activity')
        <p>Oryantasyon faaliyeti eklendi:</p>
        <ul>
            <li>Faaliyet: {{ $activity->activity }}</li>
            <li>Eğitimi Veren: {{ $activity->trainer }}</li>
            <li>Başlangıç: {{ $activity->start_date }}</li>
            <li>Bitiş: {{ $activity->end_date }}</li>
            <li>Sonuç: {{ $activity->result }}</li>
        </ul>
    @break

    {{-- Oryantasyon Tamamlama --}}
    @case('complete')
        <p>{{ $plan->personnel->name }} için oryantasyon tamamlandı.</p>
        <p>Değerlendirme Tarihi: {{ $plan->evaluation_date }}<br>
        Onay Tarihi: {{ $plan->approval_date }}</p>
    @break

    {{-- Yetkinlik Atama --}}
    @case('competency')
        <p>{{ $personnel->name }} için yeni yetkinlik ataması yapıldı.</p>
        <ul>
            @foreach($personnel->competencies as $c)
                <li>{{ $c->method }} (Seviye: {{ $c->pivot->level }}, Tarih: {{ $c->pivot->granted_at }})</li>
            @endforeach
        </ul>
    @break

@endswitch