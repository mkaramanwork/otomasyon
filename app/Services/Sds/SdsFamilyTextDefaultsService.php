<?php
namespace App\Services\Sds;

/** Auto-populated, editable bilingual baseline. Do not use these statements
 * as evidence for OEL/DNEL/PNEC or transport classification. */
class SdsFamilyTextDefaultsService
{
    public function forFamily(string $code, string $locale = 'en'): array
    {
        $code = strtoupper(trim($code));
        $locale = $locale === 'tr' ? 'tr' : 'en';
        $use = match (true) {
            str_contains($code, 'MOTOR') => ['Motor ve ekipmanlarda üretici tavsiyesine uygun yağlama.', 'Lubrication of engines and equipment in accordance with manufacturer guidance.'],
            str_contains($code, 'PROSES') => ['Endüstriyel proseslerde proses yağı olarak kullanım.', 'Use as process oil in industrial applications.'],
            str_contains($code, 'DISLI'), str_contains($code, 'GEAR') => ['Dişli ve transmisyon mekanizmalarında yağlama.', 'Lubrication of gear and transmission mechanisms.'],
            default => ['Endüstriyel yağlama uygulamalarında kullanım.', 'Use in industrial lubrication applications.'],
        };
        $tr = $locale === 'tr';
        $choose = fn ($a, $b) => $tr ? $a : $b;
        return [
            'handling_storage' => [
                'handling_precautions' => $choose('Gereksiz cilt ve göz temasından kaçının. Çalışma sırasında uygun hijyen önlemlerini uygulayın.', 'Avoid unnecessary skin and eye contact. Apply appropriate hygiene measures during handling.'),
                'storage_conditions' => $choose('Kapları kapalı tutun. Isı ve tutuşturucu kaynaklardan uzakta, uygun koşullarda depolayın.', 'Keep containers closed. Store appropriately away from heat and sources of ignition.'),
            ],
            'accidental_release' => [
                'spill_environmental_precautions' => $choose('Kanalizasyona, toprağa ve yüzey sularına kontrolsüz salımı önleyin.', 'Prevent uncontrolled release to drains, soil and surface water.'),
                'spill_cleanup' => $choose('Dökülen maddeyi uygun emici malzemeyle toplayın; atıkları yerel gerekliliklere göre bertaraf edin.', 'Collect spills with suitable absorbent material and dispose of waste according to local requirements.'),
            ],
            'exposure_controls' => [
                'environmental_controls' => $choose('Çevreye kontrolsüz salımı önleyin.', 'Prevent uncontrolled release to the environment.'),
                'eye_protection' => $choose('Sıçrama riski olduğunda uygun koruyucu gözlük kullanın.', 'Use suitable safety goggles when splashing is possible.'),
                'hand_protection' => $choose('Uzun süreli veya tekrarlanan temas ihtimalinde uygun kimyasala dayanıklı eldiven kullanın.', 'Wear suitable chemical-resistant gloves where prolonged or repeated contact is possible.'),
            ],
            'disposal' => [
                'disposal' => $choose('Atığı kanalizasyona veya çevreye bırakmayın. Kullanım ve kontaminasyon geçmişine göre atık kodunu belirleyin; yetkili atık yönetimine teslim edin.', 'Do not discharge waste to drains or the environment. Assign waste codes based on actual use and contamination history, and use an authorised waste contractor.'),
            ],
            'transport' => [
                // Never set status=not_regulated by family alone.
                'special_precautions' => $choose('Sızıntıyı önleyin ve kapları nakliye sırasında sabitleyin.', 'Prevent leakage and secure containers during transport.'),
            ],
        ];
    }
}
