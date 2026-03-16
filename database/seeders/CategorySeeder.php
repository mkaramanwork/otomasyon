<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            // ISO 17025:2017 ana başlıkları
            ['name' => 'ISO 17025:2017 - Yönetim Sistemi', 'description' => 'Kalite yönetim sistemi, politika ve prosedürler.'],
            ['name' => 'ISO 17025:2017 - Teknik Gereklilikler', 'description' => 'Metot validasyonu, ölçüm belirsizliği, cihaz kalibrasyonu.'],
            ['name' => 'ISO 17025:2017 - Dokümantasyon', 'description' => 'Prosedürler, talimatlar, kayıtların yönetimi.'],
            ['name' => 'ISO 17025:2017 - Denetimler', 'description' => 'İç denetim, dış denetim ve akreditasyon süreçleri.'],

            // Temel laboratuvar uygulamaları
            ['name' => 'Genel Laboratuvar Uygulamaları', 'description' => 'Laboratuvar ortamında temel işleyiş ve cihaz kullanımı.'],
            ['name' => 'Numune Yönetimi', 'description' => 'Numune kabul, muhafaza, izlenebilirlik ve raporlama.'],
            ['name' => 'Kalite Kontrol', 'description' => 'İç ve dış kalite kontrol süreçleri.'],
            ['name' => 'Cihaz Kalibrasyonu ve Bakım', 'description' => 'Cihazların kalibrasyonu, doğrulama ve bakım süreçleri.'],

            // İSG ve çevre
            ['name' => 'İSG - İş Sağlığı ve Güvenliği', 'description' => 'Risk değerlendirme, koruyucu önlemler, acil durum planları.'],
            ['name' => 'Çevre ve Atık Yönetimi', 'description' => 'Atıkların yönetimi, çevre mevzuatına uyum.'],

            // İnsan ve süreç yönetimi
            ['name' => 'Yetkinlik ve Eğitim', 'description' => 'Personel yetkinliklerinin değerlendirilmesi ve eğitim programları.'],
            ['name' => 'Etik ve Gizlilik', 'description' => 'Etik kurallar, müşteri verilerinin gizliliği.'],
            ['name' => 'Müşteri İletişimi ve Raporlama', 'description' => 'Müşteri talepleri, rapor hazırlama ve iletişim.'],

            // Modern gereklilikler
            ['name' => 'Bilgi Teknolojileri ve Dijital Sistemler', 'description' => 'LIMS, dijital kayıt ve otomasyon süreçleri.'],
            ['name' => 'Risk ve Fırsat Yönetimi', 'description' => 'Risklerin belirlenmesi ve fırsatların değerlendirilmesi.'],
            ['name' => 'Laboratuvar Güvenliği', 'description' => 'Kimyasal güvenlik, biyogüvenlik ve fiziksel güvenlik önlemleri.'],
            ['name' => 'Standardizasyon ve Referans Malzemeler', 'description' => 'Referans malzemeler, standardizasyon ve izlenebilirlik.'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }
    }
}