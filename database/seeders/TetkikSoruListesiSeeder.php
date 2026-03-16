<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TetkikSoruListesi;

class TetkikSoruListesiSeeder extends Seeder
{
    public function run()
    {
        // --- Clause 4: General Requirements ---
        TetkikSoruListesi::create(['standart_maddesi'=>'4.1','soru'=>'Laboratuvar faaliyetlerinde çıkar çatışması engelleniyor mu?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'4.1','soru'=>'Tarafsızlık riskleri düzenli olarak değerlendiriliyor mu?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'4.2','soru'=>'Müşteri bilgileri gizlilik politikası ile korunuyor mu?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'4.2','soru'=>'Çalışanlar gizlilik taahhüdü imzalamış mı?']);

        // --- Clause 5: Structural Requirements ---
        TetkikSoruListesi::create(['standart_maddesi'=>'5.1','soru'=>'Organizasyon şeması güncel mi?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'5.1','soru'=>'Yönetim sorumlulukları açıkça tanımlanmış mı?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'5.2','soru'=>'Yetki devri yazılı olarak belgelenmiş mi?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'5.2','soru'=>'Kritik kararlar için sorumluluklar net mi?']);

        // --- Clause 6: Resource Requirements ---
        TetkikSoruListesi::create(['standart_maddesi'=>'6.2','soru'=>'Personel yetkinlikleri belgelenmiş mi?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'6.2','soru'=>'Eğitim kayıtları düzenli tutuluyor mu?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'6.2','soru'=>'Yetkinlik değerlendirmeleri periyodik yapılıyor mu?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'6.4','soru'=>'Çalışma ortamı test sonuçlarını etkilemeyecek şekilde kontrol ediliyor mu?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'6.4','soru'=>'Çevresel koşullar (ısı, nem, titreşim) izleniyor mu?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'6.5','soru'=>'Ekipmanlar kalibre edilmiş mi?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'6.5','soru'=>'Kalibrasyon sertifikaları izlenebilir mi?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'6.5','soru'=>'Bakım planları uygulanıyor mu?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'6.6','soru'=>'Tedarikçi değerlendirmeleri yapılıyor mu?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'6.6','soru'=>'Kritik hizmetler için sözleşmeler mevcut mu?']);

        // --- Clause 7: Process Requirements ---
        TetkikSoruListesi::create(['standart_maddesi'=>'7.1','soru'=>'Sözleşme gözden geçirme kayıtları mevcut mu?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'7.1','soru'=>'Müşteri talepleri doğrulanıyor mu?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'7.2','soru'=>'Kullanılan metotlar uygun ve güncel mi?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'7.2','soru'=>'Validasyon raporları mevcut mu?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'7.2','soru'=>'Ölçüm belirsizliği hesaplanıyor mu?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'7.4','soru'=>'Numune alma prosedürleri belgelenmiş mi?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'7.4','soru'=>'Numune bütünlüğü korunuyor mu?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'7.5','soru'=>'Testler prosedürlere uygun yapılıyor mu?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'7.5','soru'=>'Kalibrasyon sonuçları kayıt altına alınıyor mu?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'7.7','soru'=>'Sonuçlar doğrulama prosedürüne tabi tutuluyor mu?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'7.7','soru'=>'İç kalite kontrolleri uygulanıyor mu?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'7.9','soru'=>'Şikayetler kayıt altına alınıyor mu?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'7.9','soru'=>'Şikayetlere yönelik düzeltici faaliyetler yapılıyor mu?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'7.10','soru'=>'Uygunsuzluklar kayıt altına alınıyor mu?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'7.10','soru'=>'Düzeltici faaliyetler etkin şekilde takip ediliyor mu?']);

        // --- Clause 8: Management System Requirements ---
        TetkikSoruListesi::create(['standart_maddesi'=>'8.2','soru'=>'Dokümanlar güncel ve erişilebilir mi?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'8.2','soru'=>'Revizyonlar izlenebilir mi?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'8.4','soru'=>'Kayıtlar güvenli şekilde saklanıyor mu?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'8.4','soru'=>'Kayıtların erişim yetkileri tanımlanmış mı?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'8.5','soru'=>'Risk değerlendirme matrisi mevcut mu?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'8.5','soru'=>'Fırsatlar yönetim gözden geçirmesinde ele alınıyor mu?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'8.6','soru'=>'Düzeltici faaliyetler etkinliği doğrulanıyor mu?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'8.8','soru'=>'İç tetkikler planlı şekilde yapılıyor mu?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'8.8','soru'=>'Tetkik raporları kayıtlı mı?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'8.9','soru'=>'Yönetim gözden geçirme toplantıları düzenli yapılıyor mu?']);
        TetkikSoruListesi::create(['standart_maddesi'=>'8.9','soru'=>'Çıktılar (kararlar, aksiyonlar) kayıt altına alınıyor mu?']);
    }
}