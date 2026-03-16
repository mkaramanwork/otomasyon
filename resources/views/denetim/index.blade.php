@extends('layouts.admin')

@section('title','Denetim Takip Dokümanları')

@section('content')
<div class="container mt-4">
    <h3>📑 Denetim Takip Dokümanları</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="accordion" id="denetimAccordion">

        @php
            $categories = [
                'yasal_statü' => '⚖️ Yasal Statü',
                'mesleki_sorumluluk_sigortası' => '🛡️ Mesleki Sorumluluk Sigortası',
                'imza_sirküleri' => '✍️ İmza Sirküleri',
                'hizmet_kapsamı' => '📌 Hizmet Kapsamı',
                'organizasyon_şeması' => '👥 Organizasyon Şeması ve Görev Tanımları',
                'laboratuvar_bilgi_beyan_formu' => '🧾 Laboratuvar Bilgi Beyan Formu',
                'ana_dokuman_listesi' => '📚 Ana Doküman Listesi',
                'kalite_el_kitabı' => '📖 Kalite El Kitabı',
                'prosedürler' => '📑 Prosedürler',
                'kalite_talimatları' => '📋 Kalite Talimatları',
                'cihaz_talimatları' => '🔧 Cihaz Talimatları',
                'kalibrasyon_planı' => '📐 Kalibrasyon Planı',
                'kalibrasyon_sertifikaları' => '📜 Kalibrasyon Sertifikaları',
                'referans_malzeme_listesi' => '🧪 Referans Malzeme Listesi ve Sertifikalar',
                'onaylı_tedarikçi_listesi' => '📦 Onaylı Tedarikçi Listesi ve Değerlendirmeler',
                'doğrulama_kayıtları' => '✅ Doğrulama ve Ölçüm Belirsizlikleri Kayıtları',
                'yeterlilik_deneyi_kayıtları' => '🧬 Yeterlilik Deneyi Kayıtları',
                'deney_raporu_örneği' => '📄 Deney Raporu Örneği',
                'risk_fırsat_değerlendirme' => '⚠️ Risk ve Fırsatları Değerlendirme',
                'iç_tetkik_kayıtları' => '🔍 İç Tetkik Kayıtları',
                'ygg' => '📊 YGG',
                'personel_listesi' => '👤 Personel Listesi',
                'deney_listesi' => '🧪 Deney Listesi',
                'iç_sözleşme' => '📑 İç Sözleşme',
                'uygunsuzluk_listesi' => '⚠️ Uygunsuzluk Listesi',
            ];
        @endphp

        @foreach($categories as $key => $label)
        <div class="accordion-item mb-3"> <!-- boşluk için mb-3 -->
            <h2 class="accordion-header" id="heading{{ $key }}">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $key }}">
                    {{ $label }}
                </button>
            </h2>
            <div id="collapse{{ $key }}" class="accordion-collapse collapse" data-bs-parent="#denetimAccordion">
                <div class="accordion-body">
                    <!-- Upload Form -->
                    <form action="{{ route('denetim.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="category" value="{{ $key }}">
                        <div class="mb-3">
                            <label>Doküman Adı</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>PDF Dosyası</label>
                            <input type="file" name="file" class="form-control" accept="application/pdf" required>
                        </div>
                        <div class="mb-3">
                            <label>Tanım</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Tarih</label>
                            <input type="date" name="date" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Yükle</button>
                    </form>

                    <!-- Listeleme -->
                    <table class="table mt-3">
                        <thead>
                            <tr>
                                <th>Adı</th>
                                <th>Tanım</th>
                                <th>Tarih</th>
                                <th>Ekleyen</th>
                                <th>Dosya</th>
                                <th>Sil</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents->where('category',$key) as $doc)
                                <tr>
                                    <td>{{ $doc->title }}</td>
                                    <td>{{ $doc->description }}</td>
                                    <td>{{ $doc->date }}</td>
                                    <td>{{ $doc->user->name }}</td>
                                    <td><a href="{{ asset($doc->file_path) }}" target="_blank">PDF</a></td>
                                    <td>
                                        <form action="{{ route('denetim.destroy',$doc->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach

    </div>
</div>
@endsection