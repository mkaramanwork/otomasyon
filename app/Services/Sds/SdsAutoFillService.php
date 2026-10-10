<?php

namespace App\Services\Sds;

use App\Models\SdsFormulaProfile;

class SdsAutoFillService
{
    public function defaults(
        ?SdsFormulaProfile $profile = null
    ): array {
        $family = $profile?->sds_product_family_id
            ? \App\Models\SdsProductFamily::find($profile->sds_product_family_id)
            : null;
        $physical =
            $profile?->physical_properties
            ?? [];

        $isLiquid =
            strtolower(
                (string) (
                    $physical['physical_state']
                    ?? ''
                )
            ) === 'liquid';

        $defaults = [
            'exposure_controls' => [
                'oel' =>
                    'No product-specific occupational exposure limit is stored in the local SDS regulatory database. Applicable national or component-specific limits shall be observed where available.',

                'dnel' =>
                    'No product-specific DNEL value is stored in the local SDS regulatory database.',

                'pnec' =>
                    'No product-specific PNEC value is stored in the local SDS regulatory database.',
            ],

            'physical_defaults' => [
                'odour' =>
                    'Characteristic',

                'ph' =>
                    $isLiquid
                        ? 'Not applicable for hydrocarbon/oil-based mixture unless otherwise determined.'
                        : 'No data available',

                'water_solubility' =>
                    $isLiquid
                        ? 'Insoluble / practically insoluble in water'
                        : 'No data available',

                'flammability' =>
                    'No separate flammability classification assigned from the current mixture classification.',

                'particle_characteristics' =>
                    $isLiquid
                        ? 'Not applicable - product is a liquid.'
                        : 'No data available',
            ],

            'transport' => [
                'status' =>
                    'not_regulated',

                'environmental_hazards' =>
                    'Not regulated as dangerous goods under the default lubricant transport profile unless a product-specific assessment indicates otherwise.',

                'special_precautions' =>
                    'Prevent leakage and secure containers during transport.',

                'bulk_transport' =>
                    'Not applicable / not determined.',
            ],
        ];
        $custom = is_array($family?->defaults) ? $family->defaults : [];
        // Database bilingual JSON: {"tr": {"first_aid": {...}}, "en": {...}}.
        // Legacy flat defaults remain supported; no user JSON editing required.
        $locale = app()->getLocale() === 'tr' ? 'tr' : 'en';
        if (isset($custom[$locale]) && is_array($custom[$locale])) {
            $custom = $custom[$locale];
        } elseif (isset($custom['en']) && is_array($custom['en'])) {
            $custom = $custom['en'];
        }
        $fallback = (new SdsFamilyTextDefaultsService())->forFamily((string) ($family?->code ?? ''), $locale);
        foreach ($fallback as $group => $values) {
            if (is_array($values)) {
                $defaults[$group] = $this->merge($defaults[$group] ?? [], $values);
            }
        }

        foreach (['fire_fighting','accidental_release','handling_storage','exposure_controls','stability_reactivity','disposal','physical_defaults','first_aid','transport'] as $group) {
            if (isset($custom[$group]) && is_array($custom[$group])) {
                $defaults[$group] = $this->merge($defaults[$group] ?? [], $custom[$group]);
            }
        }
        return $defaults;
    }

    public function merge(
        array $defaults,
        array $override
    ): array {
        foreach ($override as $key => $value) {
            if (is_array($value)) {
                $defaults[$key] =
                    $this->merge(
                        is_array(
                            $defaults[$key]
                            ?? null
                        )
                            ? $defaults[$key]
                            : [],
                        $value
                    );

                continue;
            }

            if (
                $value !== null &&
                $value !== ''
            ) {
                $defaults[$key] =
                    $value;
            }
        }

        return $defaults;
    }
}
