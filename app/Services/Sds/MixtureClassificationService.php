<?php

namespace App\Services\Sds;

use App\Models\Formula;
use App\Models\RegulatoryClassification;
use Illuminate\Support\Collection;

class MixtureClassificationService
{
    public function collect(
        Formula $formula,
        string $regulationProfile = 'TR_KKDIK_SEA'
    ): array {
        $formula->loadMissing([
            'details.rawMaterial.currentSdsProfile.components.regulatorySubstance',
        ]);

        $issues = [];
        $details = $formula->details->values();

        $amountContext = $this->buildAmountContext($details);
        $percentageMap = $this->buildPercentageMap(
            $details,
            $amountContext,
            $issues
        );

        $substanceIds = $details
            ->flatMap(function ($detail) {
                $profile = $detail->rawMaterial?->currentSdsProfile;

                if (!$profile) {
                    return [];
                }

                return $profile->components
                    ->pluck('regulatory_substance_id')
                    ->filter()
                    ->all();
            })
            ->unique()
            ->values();

        $regulatoryBySubstance = collect();

        if ($substanceIds->isNotEmpty()) {
            $regulatoryBySubstance = RegulatoryClassification::query()
                ->whereIn('regulatory_substance_id', $substanceIds)
                ->where('is_current', true)
                ->get()
                ->groupBy('regulatory_substance_id');
        }

        $rawMaterials = [];
        $mixtureComponents = [];

        foreach ($details as $detail) {
            $rawMaterial = $detail->rawMaterial;
            $formulaPercentage = $percentageMap[$detail->id]['percentage'] ?? null;
            $percentageSource = $percentageMap[$detail->id]['source'] ?? null;

            if (!$rawMaterial) {
                $issues[] = $this->issue(
                    'error',
                    'RAW_MATERIAL_NOT_FOUND',
                    'Formül satırındaki hammadde kaydı bulunamadı.',
                    [
                        'formula_detail_id' => $detail->id,
                        'raw_material_id' => $detail->raw_material_id,
                    ]
                );

                continue;
            }

            $profile = $rawMaterial->currentSdsProfile;

            $rawMaterialRow = [
                'formula_detail_id' => $detail->id,
                'raw_material_id' => $rawMaterial->id,
                'raw_material_name' => $rawMaterial->name,
                'formula_amount' => $this->toFloatOrNull($detail->amount),
                'formula_unit' => $detail->unit,
                'formula_percentage' => $formulaPercentage,
                'formula_percentage_source' => $percentageSource,
                'sds_profile_id' => $profile?->id,
                'sds_supplier_name' => $profile?->supplier_name,
                'sds_supplier_product_name' => $profile?->supplier_product_name,
                'sds_date' => $this->dateValue($profile?->supplier_sds_date),
                'sds_revision' => $profile?->supplier_sds_revision,
                'sds_version' => $profile?->supplier_sds_version,
                'sds_regulation_profile' => $profile?->regulation_profile,
                'sds_verified' => $profile?->is_verified,
                'component_count' => $profile?->components?->count() ?? 0,
            ];

            $rawMaterials[] = $rawMaterialRow;

            if ($formulaPercentage === null) {
                $issues[] = $this->issue(
                    'error',
                    'FORMULA_PERCENTAGE_MISSING',
                    'Hammadde için formül yüzdesi hesaplanamadı.',
                    [
                        'formula_detail_id' => $detail->id,
                        'raw_material_id' => $rawMaterial->id,
                        'raw_material_name' => $rawMaterial->name,
                    ]
                );
            }

            if (!$profile) {
                $issues[] = $this->issue(
                    'error',
                    'CURRENT_SDS_MISSING',
                    'Hammaddenin güncel SDS kaydı bulunmuyor.',
                    [
                        'raw_material_id' => $rawMaterial->id,
                        'raw_material_name' => $rawMaterial->name,
                    ]
                );

                continue;
            }

            if ($profile->components->isEmpty()) {
                $issues[] = $this->issue(
                    'error',
                    'SDS_COMPONENTS_MISSING',
                    'Hammaddenin güncel SDS kaydında Bölüm 3 bileşeni bulunmuyor.',
                    [
                        'raw_material_id' => $rawMaterial->id,
                        'raw_material_name' => $rawMaterial->name,
                        'sds_profile_id' => $profile->id,
                    ]
                );

                continue;
            }

            foreach ($profile->components as $component) {
                $componentIssues = [];

                $componentUnit = trim((string) ($component->concentration_unit ?? '%'));
                $componentMin = $this->toFloatOrNull($component->concentration_min);
                $componentMax = $this->toFloatOrNull($component->concentration_max);

                [$componentMin, $componentMax] = $this->normalizeRange(
                    $componentMin,
                    $componentMax
                );

                $effectiveMin = null;
                $effectiveMax = null;

                if ($componentUnit !== '%') {
                    $componentIssues[] = $this->issue(
                        'error',
                        'UNSUPPORTED_COMPONENT_UNIT',
                        'Bileşen konsantrasyon birimi yüzde değil; otomatik efektif konsantrasyon hesaplanmadı.',
                        [
                            'component_id' => $component->id,
                            'unit' => $componentUnit,
                        ]
                    );
                } elseif ($componentMin === null || $componentMax === null) {
                    $componentIssues[] = $this->issue(
                        'error',
                        'COMPONENT_CONCENTRATION_MISSING',
                        'SDS bileşen konsantrasyon aralığı eksik.',
                        [
                            'component_id' => $component->id,
                        ]
                    );
                } elseif ($formulaPercentage !== null) {
                    $effectiveMin = $this->roundPercent(
                        ($formulaPercentage * $componentMin) / 100
                    );
                    $effectiveMax = $this->roundPercent(
                        ($formulaPercentage * $componentMax) / 100
                    );
                }

                $substance = $component->regulatorySubstance;
                $records = $component->regulatory_substance_id
                    ? $regulatoryBySubstance->get(
                        $component->regulatory_substance_id,
                        collect()
                    )
                    : collect();

                $requestedRecords = $records
                    ->where('regulation_profile', $regulationProfile)
                    ->values();

                $euRecords = $records
                    ->where('regulation_profile', 'EU_REACH_CLP')
                    ->values();

                if (!$component->regulatory_substance_id) {
                    $componentIssues[] = $this->issue(
                        'warning',
                        'REGULATORY_SUBSTANCE_NOT_LINKED',
                        'SDS bileşeni mevzuat maddesiyle eşleştirilmemiş.',
                        [
                            'component_id' => $component->id,
                            'chemical_name' => $component->chemical_name,
                        ]
                    );
                } elseif ($records->isEmpty()) {
                    $componentIssues[] = $this->issue(
                        'warning',
                        'CURRENT_REGULATORY_CLASSIFICATION_MISSING',
                        'Eşleşen mevzuat maddesi için güncel sınıflandırma kaydı bulunamadı.',
                        [
                            'component_id' => $component->id,
                            'regulatory_substance_id' => $component->regulatory_substance_id,
                        ]
                    );
                }

                $row = [
                    'formula_detail_id' => $detail->id,
                    'raw_material_id' => $rawMaterial->id,
                    'raw_material_name' => $rawMaterial->name,
                    'formula_percentage' => $formulaPercentage,
                    'formula_percentage_source' => $percentageSource,
                    'sds_profile_id' => $profile->id,
                    'sds_component_id' => $component->id,
                    'chemical_name' => $component->chemical_name,
                    'cas_no' => $component->cas_no,
                    'ec_no' => $component->ec_no,
                    'index_no' => $component->index_no,
                    'regulatory_substance_id' => $component->regulatory_substance_id,
                    'official_preferred_name' => $substance?->preferred_name,
                    'official_cas_no' => $substance?->cas_no,
                    'official_ec_no' => $substance?->ec_no,
                    'official_index_no' => $substance?->index_no,
                    'component_concentration_min' => $componentMin,
                    'component_concentration_max' => $componentMax,
                    'component_concentration_unit' => $componentUnit,
                    'effective_concentration_min' => $effectiveMin,
                    'effective_concentration_max' => $effectiveMax,
                    'supplier' => [
                        'hazard_classifications' => $this->normalizeArray(
                            $component->hazard_classifications
                        ),
                        'h_statements' => $this->normalizeArray(
                            $component->h_statements
                        ),
                        'specific_concentration_limits' => $this->normalizeArray(
                            $component->specific_concentration_limits
                        ),
                        'ate_data' => $this->normalizeArray(
                            $component->ate_data
                        ),
                        'm_factor_acute' => $this->toFloatOrNull(
                            $component->m_factor_acute
                        ),
                        'm_factor_chronic' => $this->toFloatOrNull(
                            $component->m_factor_chronic
                        ),
                        'regulatory_notes' => $this->normalizeJsonValue(
                            $component->regulatory_notes
                        ),
                        'supplier_note' => $component->supplier_note,
                        'is_disclosed' => (bool) $component->is_disclosed,
                        'is_verified' => (bool) $component->is_verified,
                    ],
                    'regulatory' => [
                        'requested_profile' => $regulationProfile,
                        'requested_profile_records' => $this->serializeRecords(
                            $requestedRecords
                        ),
                        'eu_reach_clp_records' => $this->serializeRecords(
                            $euRecords
                        ),
                        'all_current_records' => $this->serializeRecords(
                            $records
                        ),
                    ],
                    'issues' => $componentIssues,
                ];

                $mixtureComponents[] = $row;

                foreach ($componentIssues as $componentIssue) {
                    $issues[] = $componentIssue;
                }
            }
        }

        $formulaPercentageTotal = round(
            collect($percentageMap)
                ->pluck('percentage')
                ->filter(fn ($value) => $value !== null)
                ->sum(),
            6
        );

        if (
            count($percentageMap) > 0 &&
            ($formulaPercentageTotal < 99.5 || $formulaPercentageTotal > 100.5)
        ) {
            $issues[] = $this->issue(
                'warning',
                'FORMULA_PERCENTAGE_TOTAL_NOT_100',
                'Formül yüzdeleri toplamı yaklaşık %100 değil.',
                [
                    'formula_percentage_total' => $formulaPercentageTotal,
                ]
            );
        }

        $errorCount = collect($issues)
            ->where('severity', 'error')
            ->count();

        return [
            'formula' => [
                'id' => $formula->id,
                'name' => $formula->name,
                'type' => $formula->type,
                'product_id' => $formula->product_id,
                'regulation_profile' => $regulationProfile,
                'detail_count' => $details->count(),
                'percentage_total' => $formulaPercentageTotal,
            ],
            'summary' => [
                'raw_material_count' => count($rawMaterials),
                'raw_materials_with_current_sds' => collect($rawMaterials)
                    ->whereNotNull('sds_profile_id')
                    ->count(),
                'sds_component_count' => count($mixtureComponents),
                'components_with_regulatory_link' => collect($mixtureComponents)
                    ->whereNotNull('regulatory_substance_id')
                    ->count(),
                'components_with_effective_concentration' => collect($mixtureComponents)
                    ->filter(
                        fn ($row) =>
                            $row['effective_concentration_min'] !== null &&
                            $row['effective_concentration_max'] !== null
                    )
                    ->count(),
                'error_count' => $errorCount,
                'warning_count' => collect($issues)
                    ->where('severity', 'warning')
                    ->count(),
                'ready_for_classification' => $errorCount === 0,
            ],
            'raw_materials' => $rawMaterials,
            'mixture_components' => $mixtureComponents,
            'issues' => array_values($issues),
        ];
    }

    private function buildAmountContext(Collection $details): array
    {
        $usable = $details->filter(function ($detail) {
            return $this->toFloatOrNull($detail->amount) !== null &&
                trim((string) $detail->unit) !== '';
        });

        if ($usable->count() !== $details->count() || $usable->isEmpty()) {
            return [
                'can_derive' => false,
                'unit' => null,
                'total_amount' => null,
            ];
        }

        $units = $usable
            ->pluck('unit')
            ->map(fn ($unit) => mb_strtolower(trim((string) $unit)))
            ->unique()
            ->values();

        if ($units->count() !== 1) {
            return [
                'can_derive' => false,
                'unit' => null,
                'total_amount' => null,
            ];
        }

        $totalAmount = $usable->sum(
            fn ($detail) => (float) $detail->amount
        );

        if ($totalAmount <= 0) {
            return [
                'can_derive' => false,
                'unit' => $units->first(),
                'total_amount' => $totalAmount,
            ];
        }

        return [
            'can_derive' => true,
            'unit' => $units->first(),
            'total_amount' => $totalAmount,
        ];
    }

    private function buildPercentageMap(
        Collection $details,
        array $amountContext,
        array &$issues
    ): array {
        $map = [];

        foreach ($details as $detail) {
            $storedPercentage = $this->toFloatOrNull($detail->percentage);

            if ($storedPercentage !== null) {
                $map[$detail->id] = [
                    'percentage' => $this->roundPercent($storedPercentage),
                    'source' => 'formula_detail.percentage',
                ];

                continue;
            }

            if ($amountContext['can_derive']) {
                $amount = $this->toFloatOrNull($detail->amount);

                if ($amount !== null) {
                    $map[$detail->id] = [
                        'percentage' => $this->roundPercent(
                            ($amount / $amountContext['total_amount']) * 100
                        ),
                        'source' => 'derived_from_amount',
                    ];

                    continue;
                }
            }

            $map[$detail->id] = [
                'percentage' => null,
                'source' => null,
            ];

            $issues[] = $this->issue(
                'error',
                'FORMULA_PERCENTAGE_CANNOT_BE_DERIVED',
                'FormulaDetail percentage alanı boş ve amount üzerinden güvenli yüzde hesabı yapılamıyor.',
                [
                    'formula_detail_id' => $detail->id,
                    'amount' => $detail->amount,
                    'unit' => $detail->unit,
                ]
            );
        }

        return $map;
    }

    private function serializeRecords(Collection $records): array
    {
        return $records
            ->map(function ($record) {
                return [
                    'id' => $record->id,
                    'regulation_profile' => $record->regulation_profile,
                    'jurisdiction' => $record->jurisdiction,
                    'source_type' => $record->source_type,
                    'source_name' => $record->source_name,
                    'source_version' => $record->source_version,
                    'source_date' => $this->dateValue($record->source_date),
                    'source_reference' => $record->source_reference,
                    'source_url' => $record->source_url,
                    'legal_status' => $record->legal_status,
                    'hazard_classifications' => $this->normalizeArray(
                        $record->hazard_classifications
                    ),
                    'signal_word' => $record->signal_word,
                    'pictograms' => $this->normalizeArray(
                        $record->pictograms
                    ),
                    'hazard_statements' => $this->normalizeArray(
                        $record->hazard_statements
                    ),
                    'supplemental_hazard_statements' => $this->normalizeArray(
                        $record->supplemental_hazard_statements
                    ),
                    'specific_concentration_limits' => $this->normalizeJsonValue(
                        $record->specific_concentration_limits
                    ),
                    'ate_data' => $this->normalizeJsonValue(
                        $record->ate_data
                    ),
                    'm_factor_acute' => $this->toFloatOrNull(
                        $record->m_factor_acute
                    ),
                    'm_factor_chronic' => $this->toFloatOrNull(
                        $record->m_factor_chronic
                    ),
                    'reach_registered' => $record->reach_registered,
                    'svhc' => $record->svhc,
                    'annex_vi' => $record->annex_vi,
                    'annex_xiv' => $record->annex_xiv,
                    'annex_xvii' => $record->annex_xvii,
                    'effective_from' => $this->dateValue($record->effective_from),
                    'effective_until' => $this->dateValue($record->effective_until),
                    'regulatory_notes' => $this->normalizeJsonValue(
                        $record->regulatory_notes
                    ),
                    'is_verified' => $record->is_verified,
                ];
            })
            ->values()
            ->all();
    }

    private function normalizeRange(
        ?float $min,
        ?float $max
    ): array {
        if ($min === null && $max !== null) {
            $min = $max;
        }

        if ($max === null && $min !== null) {
            $max = $min;
        }

        if ($min !== null && $max !== null && $min > $max) {
            [$min, $max] = [$max, $min];
        }

        return [$min, $max];
    }

    private function normalizeJsonValue(mixed $value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        if ($value instanceof Collection) {
            return $value->toArray();
        }

        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            if (
                json_last_error() === JSON_ERROR_NONE &&
                is_array($decoded)
            ) {
                return $decoded;
            }
        }

        return [];
    }

    private function normalizeArray(mixed $value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        if ($value instanceof Collection) {
            return $value->values()->all();
        }

        if (is_array($value)) {
            return array_values($value);
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return array_values($decoded);
            }

            return array_values(
                array_filter(
                    array_map(
                        'trim',
                        preg_split('/[|;,]+/', $value) ?: []
                    ),
                    fn ($item) => $item !== ''
                )
            );
        }

        return [$value];
    }

    private function issue(
        string $severity,
        string $code,
        string $message,
        array $context = []
    ): array {
        return [
            'severity' => $severity,
            'code' => $code,
            'message' => $message,
            'context' => $context,
        ];
    }

    private function toFloatOrNull(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $value = str_replace(',', '.', trim($value));
        }

        return is_numeric($value)
            ? (float) $value
            : null;
    }

    private function roundPercent(float $value): float
    {
        return round($value, 6);
    }

    private function dateValue(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_object($value) && method_exists($value, 'format')) {
            return $value->format('Y-m-d');
        }

        return (string) $value;
    }
}
