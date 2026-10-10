<?php

namespace App\Services\Sds;

use App\Models\Formula;
use App\Models\Product;
use App\Models\SdsFormulaProfile;
use Illuminate\Support\Arr;

class Sds16SectionBuilderService
{
    public function __construct(
        private SdsAutoFillService $autoFill
    ) {
    }

    public function build(
        Formula $formula,
        array $analysis,
        array $classification,
        string $regulationProfile,
        ?SdsFormulaProfile $profile = null
    ): array {
        $engineStatus =
            $classification['status']
            ?? [];

        $engineResult =
            $classification['classification']
            ?? [];

        $components =
            $analysis['mixture_components']
            ?? [];

        $sections = [
            1 => $this->section1(
                $formula,
                $regulationProfile,
                $profile
            ),

            2 => $this->section2(
                $engineStatus,
                $engineResult,
                $classification
            ),

            3 => $this->section3(
                $components,
                $classification
            ),

            4 => $this->section4(
                $analysis,
                $classification,
                $profile
            ),

            5 => $this->section5(
                $classification,
                $profile
            ),

            6 => $this->section6(
                $classification,
                $profile
            ),

            7 => $this->section7(
                $classification,
                $profile
            ),

            8 => $this->section8(
                $classification,
                $profile
            ),

            9 => $this->section9(
                $profile
            ),

            10 => $this->section10(
                $profile
            ),

            11 => $this->section11(
                $analysis,
                $classification,
                $profile
            ),

            12 => $this->section12(
                $classification,
                $profile
            ),

            13 => $this->section13(
                $profile
            ),

            14 => $this->section14(
                $profile
            ),

            15 => $this->section15(
                $analysis,
                $regulationProfile,
                $profile
            ),

            16 => $this->section16(
                $analysis,
                $classification,
                $profile
            ),
        ];

        $ready = collect($sections)
            ->where('status', 'ready')
            ->count();

        $blocked = collect($sections)
            ->where('status', 'blocked')
            ->count();

        $needsData = collect($sections)
            ->where('status', 'needs_data')
            ->count();

        return [
            'formula_id' =>
                $formula->id,

            'formula_name' =>
                $formula->name,

            'regulation_profile' =>
                $regulationProfile,

            'profile_exists' =>
                $profile !== null,

            'summary' => [
                'total_sections' =>
                    16,

                'ready' =>
                    $ready,

                'blocked' =>
                    $blocked,

                'needs_data' =>
                    $needsData,

                'publish_ready' =>
                    $blocked === 0 &&
                    $needsData === 0,
            ],

            'sections' =>
                $sections,
        ];
    }

    private function section1(
        Formula $formula,
        string $regulationProfile,
        ?SdsFormulaProfile $profile
    ): array {
        $product = null;

        if (!empty($formula->product_id)) {
            $product = Product::find(
                $formula->product_id
            );
        }

        $selectedCompany = $profile?->sds_company_id
            ? \App\Models\SdsCompany::find($profile->sds_company_id)
            : null;
        $company = $selectedCompany
            ? $selectedCompany->only(['name','address','phone','fax','email','emergency_phone','logo_path'])
            : [];

        $missing = [];

        foreach (
            [
                'name' =>
                    'Şirket adı',
                'address' =>
                    'Şirket adresi',
                'phone' =>
                    'Şirket telefonu',
                'email' =>
                    'Şirket e-posta bilgisi',
                'emergency_phone' =>
                    'Acil telefon bilgisi',
            ]
            as $key => $label
        ) {
            if (empty($company[$key])) {
                $missing[] =
                    $label . ' eksik.';
            }
        }

        return [
            'number' => 1,

            'title' =>
                'Madde/karışımın ve şirket/dağıtıcının kimliği',

            'status' =>
                empty($missing)
                    ? 'ready'
                    : 'needs_data',

            'content' => [
                '1.1' => [
                    'product_name' =>
                        $product?->name
                        ?? $formula->name,

                    'product_code' =>
                        $product?->code,

                    'product_type' =>
                        $profile?->product_type
                        ?? config(
                            'sds.defaults.product_type',
                            'lubricant'
                        ),
                ],

                '1.2' => [
                    'identified_use' =>
                        $profile?->identified_use
                        ?? config(
                            'sds.defaults.identified_use'
                        ),

                    'uses_advised_against' =>
                        $profile?->uses_advised_against
                        ?? config(
                            'sds.defaults.uses_advised_against'
                        ),
                ],

                '1.3' => [
                    'supplier' =>
                        $company,
                ],

                '1.4' => [
                    'emergency_phone' =>
                        $company[
                            'emergency_phone'
                        ] ?? null,
                ],

                'regulation_profile' =>
                    $regulationProfile,
            ],

            'missing' =>
                $missing,
        ];
    }

    private function section2(
        array $engineStatus,
        array $engineResult,
        array $classification
    ): array {
        $blocked =
            !empty(
                $engineStatus[
                    'engine_error_count'
                ]
            ) ||
            !empty(
                $engineStatus[
                    'collector_error_count'
                ]
            );

        return [
            'number' => 2,

            'title' =>
                'Zararlılık tanımlanması',

            'status' =>
                $blocked
                    ? 'blocked'
                    : 'ready',

            'content' => [
                '2.1' => [
                    'hazard_classes' =>
                        $engineResult[
                            'hazard_classes'
                        ] ?? [],
                ],

                '2.2' => [
                    'signal_word' =>
                        $engineResult[
                            'signal_word'
                        ] ?? null,

                    'pictograms' =>
                        $engineResult[
                            'pictograms'
                        ] ?? [],

                    'hazard_statements' =>
                        $engineResult[
                            'hazard_statements'
                        ] ?? [],
                ],

                '2.3' => [
                    'rule_decisions' =>
                        $classification[
                            'rule_decisions'
                        ] ?? [],

                    'engine_issues' =>
                        $classification[
                            'issues'
                        ] ?? [],
                ],
            ],

            'missing' =>
                $blocked
                    ? [
                        'Sınıflandırma motorundaki blokajlar çözülmeli.',
                    ]
                    : [],
        ];
    }

    private function section3(
        array $components,
        array $classification
    ): array {
        $engineComponents = collect(
            $classification[
                'components_used'
            ] ?? []
        )->keyBy(
            fn ($row) =>
                (string) (
                    $row[
                        'sds_component_id'
                    ] ?? ''
                )
        );

        $ruleDecisions = collect(
            $classification[
                'rule_decisions'
            ] ?? []
        );

        $rows = [];

        foreach ($components as $component) {
            $componentId =
                $component[
                    'sds_component_id'
                ] ?? null;

            $engine =
                $engineComponents->get(
                    (string) $componentId
                ) ?? [];

            $chemicalName =
                $component[
                    'official_preferred_name'
                ]
                ?? $component[
                    'chemical_name'
                ]
                ?? $engine[
                    'chemical_name'
                ]
                ?? null;

            $casNo =
                $component[
                    'official_cas_no'
                ]
                ?? $component[
                    'cas_no'
                ]
                ?? $engine[
                    'cas_no'
                ]
                ?? null;

            $hazardClasses =
                $engine[
                    'hazard_classifications'
                ] ?? [];

            $hStatements =
                $engine[
                    'hazard_statements'
                ] ?? [];

            $specificLimits =
                $engine[
                    'specific_concentration_limits'
                ] ?? [];

            $ateData =
                $engine[
                    'ate_data'
                ] ?? [];

            $mAcute =
                $engine[
                    'm_factor_acute'
                ] ?? null;

            $mChronic =
                $engine[
                    'm_factor_chronic'
                ] ?? null;

            $notes = $ruleDecisions
                ->filter(function ($decision) use (
                    $casNo,
                    $component
                ) {
                    $sameCas =
                        $casNo &&
                        ($decision['cas_no'] ?? null)
                        === $casNo;

                    $sameRawMaterial =
                        !empty(
                            $component[
                                'raw_material_name'
                            ]
                        ) &&
                        (
                            $decision[
                                'raw_material_name'
                            ] ?? null
                        )
                        ===
                        $component[
                            'raw_material_name'
                        ];

                    return $sameCas ||
                        $sameRawMaterial;
                })
                ->map(
                    fn ($decision) =>
                        trim(
                            (
                                !empty(
                                    $decision['note']
                                )
                                    ? 'Note '
                                    . $decision['note']
                                    . ': '
                                    : ''
                            )
                            .
                            (
                                $decision['message']
                                ?? ''
                            )
                        )
                )
                ->filter()
                ->unique()
                ->values()
                ->all();

            $rows[] = [
                'chemical_name' =>
                    $chemicalName,

                'cas_no' =>
                    $casNo,

                'ec_no' =>
                    $component[
                        'official_ec_no'
                    ]
                    ?? $component[
                        'ec_no'
                    ]
                    ?? null,

                'index_no' =>
                    $component[
                        'official_index_no'
                    ]
                    ?? $component[
                        'index_no'
                    ]
                    ?? null,

                'effective_concentration_min' =>
                    $component[
                        'effective_concentration_min'
                    ] ?? null,

                'effective_concentration_max' =>
                    $component[
                        'effective_concentration_max'
                    ] ?? null,

                'hazard_classifications' =>
                    $hazardClasses,

                'h_statements' =>
                    $hStatements,

                'specific_concentration_limits' =>
                    $specificLimits,

                'ate_data' =>
                    $ateData,

                'm_factor_acute' =>
                    $mAcute,

                'm_factor_chronic' =>
                    $mChronic,

                'notes' =>
                    $notes,

                'classification_source' =>
                    $engine[
                        'classification_source'
                    ] ?? null,

                'regulatory_substance_id' =>
                    $component[
                        'regulatory_substance_id'
                    ] ?? null,
            ];
        }

        return [
            'number' => 3,

            'title' =>
                'Bileşimi / içindekiler hakkında bilgi',

            'status' =>
                count($rows)
                    ? 'ready'
                    : 'blocked',

            'content' => [
                '3.1' => [
                    'substances' =>
                        'Not applicable - product is a mixture.',
                ],

                '3.2' => [
                    'components' =>
                        $rows,
                ],
            ],

            'missing' =>
                count($rows)
                    ? []
                    : [
                        'Formülden SDS bileşenleri üretilemedi.',
                    ],
        ];
    }

    private function section4(
        array $analysis,
        array $classification,
        ?SdsFormulaProfile $profile
    ): array {
        $engineResult =
            $classification[
                'classification'
            ] ?? [];

        $hazards =
            $engineResult[
                'hazard_statements'
            ] ?? [];

        $collectorErrorCount =
            (int) (
                $analysis[
                    'summary'
                ][
                    'error_count'
                ] ?? 0
            );

        $custom =
            $this->autoFill->merge($this->autoFill->defaults($profile)['first_aid'] ?? [], $profile?->first_aid ?? []);

        $has = fn (string $code) =>
            in_array(
                $code,
                $hazards,
                true
            );

        $inhalation =
            $custom['inhalation']
            ?? (
                $has('H334') ||
                $has('H335') ||
                $has('H336')
                    ? 'Move the exposed person to fresh air and keep at rest in a position comfortable for breathing. Seek medical attention if respiratory symptoms, dizziness or discomfort occur.'
                    : 'Move the person to fresh air if discomfort occurs. Seek medical advice if symptoms persist.'
            );

        $skin =
            $custom['skin_contact']
            ?? (
                $has('H314')
                    ? 'Immediately remove contaminated clothing. Rinse skin with plenty of water for at least 15 minutes and obtain medical attention.'
                    : (
                        $has('H317')
                            ? 'Remove contaminated clothing and wash skin thoroughly with soap and water. Obtain medical advice if rash or allergic reaction occurs.'
                            : 'Remove contaminated clothing and wash skin with soap and water. Obtain medical advice if irritation persists.'
                    )
            );

        $eye =
            $custom['eye_contact']
            ?? (
                $has('H314') ||
                $has('H318')
                    ? 'Immediately rinse eyes carefully with plenty of clean water for at least 15 minutes. Remove contact lenses if easy to do. Continue rinsing and obtain urgent medical attention.'
                    : 'Rinse carefully with clean water for several minutes. Remove contact lenses if easy to do. Obtain medical advice if irritation persists.'
            );

        $ingestion =
            $custom['ingestion']
            ?? (
                $has('H304')
                    ? 'Rinse mouth. Do NOT induce vomiting. Obtain immediate medical attention because of the risk of aspiration into the lungs.'
                    : 'Rinse mouth. Do not give anything by mouth to an unconscious person. Obtain medical advice if unwell.'
            );

        $symptoms =
            $custom['symptoms']
            ?? $this->symptomText(
                $hazards
            );

        $medical =
            $custom['medical_attention']
            ?? (
                $has('H304')
                    ? 'Treat symptomatically. If aspiration is suspected, observe for delayed respiratory effects. Do not induce vomiting.'
                    : 'Treat symptomatically and according to the route and severity of exposure.'
            );

        return [
            'number' => 4,

            'title' =>
                'İlk yardım önlemleri',

            'status' =>
                $collectorErrorCount > 0
                    ? 'blocked'
                    : 'ready',

            'content' => [
                '4.1' => [
                    'general' =>
                        $custom['general']
                        ?? 'Remove the affected person from the source of exposure. If in doubt or if symptoms persist, obtain medical advice.',

                    'inhalation' =>
                        $inhalation,

                    'skin_contact' =>
                        $skin,

                    'eye_contact' =>
                        $eye,

                    'ingestion' =>
                        $ingestion,
                ],

                '4.2' => [
                    'important_symptoms_and_effects' =>
                        $symptoms,
                ],

                '4.3' => [
                    'medical_attention_and_special_treatment' =>
                        $medical,
                ],
            ],

            'missing' =>
                $collectorErrorCount > 0
                    ? [
                        'Formül/SDS veri toplama hataları çözülmeli.',
                    ]
                    : [],
        ];
    }

    private function section5(
        array $classification,
        ?SdsFormulaProfile $profile
    ): array {
        $custom =
            $this->autoFill->merge($this->autoFill->defaults($profile)['fire_fighting'] ?? [], $profile?->fire_fighting ?? []);

        $physical =
            $profile?->physical_properties
            ?? [];

        return [
            'number' => 5,

            'title' =>
                'Yangınla mücadele önlemleri',

            'status' =>
                'ready',

            'content' => [
                '5.1' => [
                    'suitable_extinguishing_media' =>
                        $custom['suitable_media']
                        ?? config(
                            'sds.defaults.fire_suitable_media'
                        ),

                    'unsuitable_extinguishing_media' =>
                        $custom['unsuitable_media']
                        ?? config(
                            'sds.defaults.fire_unsuitable_media'
                        ),
                ],

                '5.2' => [
                    'special_hazards' =>
                        $custom['special_hazards']
                        ?? config(
                            'sds.defaults.fire_special_hazards'
                        ),

                    'hazardous_combustion_products' =>
                        $custom[
                            'hazardous_combustion_products'
                        ]
                        ?? config(
                            'sds.defaults.hazardous_decomposition'
                        ),

                    'flash_point_c' =>
                        $physical[
                            'flash_point_c'
                        ] ?? null,
                ],

                '5.3' => [
                    'advice_for_firefighters' =>
                        $custom[
                            'firefighter_advice'
                        ]
                        ?? config(
                            'sds.defaults.firefighter_advice'
                        ),
                ],
            ],

            'missing' =>
                [],
        ];
    }

    private function section6(
        array $classification,
        ?SdsFormulaProfile $profile
    ): array {
        $custom =
            $this->autoFill->merge($this->autoFill->defaults($profile)['accidental_release'] ?? [], $profile?->accidental_release ?? []);

        $hazards =
            $classification[
                'classification'
            ][
                'hazard_statements'
            ] ?? [];

        $environmentText =
            $custom[
                'environmental_precautions'
            ]
            ?? config(
                'sds.defaults.spill_environmental_precautions'
            );

        if (
            collect($hazards)->contains(
                fn ($h) =>
                    in_array(
                        $h,
                        [
                            'H400',
                            'H410',
                            'H411',
                            'H412',
                            'H413',
                        ],
                        true
                    )
            )
        ) {
            $environmentText .=
                ' The mixture has an aquatic environmental hazard classification; prevent any release to the aquatic environment.';
        }

        return [
            'number' => 6,

            'title' =>
                'Kaza sonucu yayılmaya karşı önlemler',

            'status' =>
                'ready',

            'content' => [
                '6.1' => [
                    'personal_precautions' =>
                        $custom[
                            'personal_precautions'
                        ]
                        ?? config(
                            'sds.defaults.spill_personal_precautions'
                        ),
                ],

                '6.2' => [
                    'environmental_precautions' =>
                        $environmentText,
                ],

                '6.3' => [
                    'containment_and_cleanup' =>
                        $custom[
                            'containment_cleanup'
                        ]
                        ?? config(
                            'sds.defaults.spill_cleanup'
                        ),
                ],

                '6.4' => [
                    'reference_to_other_sections' =>
                        'See Section 8 for personal protective equipment and Section 13 for disposal considerations.',
                ],
            ],

            'missing' =>
                [],
        ];
    }

    private function section7(
        array $classification,
        ?SdsFormulaProfile $profile
    ): array {
        $custom =
            $this->autoFill->merge($this->autoFill->defaults($profile)['handling_storage'] ?? [], $profile?->handling_storage ?? []);

        $physical =
            $profile?->physical_properties
            ?? [];

        return [
            'number' => 7,

            'title' =>
                'Elleçleme ve depolama',

            'status' =>
                'ready',

            'content' => [
                '7.1' => [
                    'safe_handling' =>
                        $custom[
                            'safe_handling'
                        ]
                        ?? config(
                            'sds.defaults.handling_precautions'
                        ),

                    'occupational_hygiene' =>
                        $custom[
                            'occupational_hygiene'
                        ]
                        ?? 'Do not eat, drink or smoke while handling the product. Wash hands after use.',
                ],

                '7.2' => [
                    'storage_conditions' =>
                        $custom[
                            'storage_conditions'
                        ]
                        ?? config(
                            'sds.defaults.storage_conditions'
                        ),

                    'incompatible_materials' =>
                        $custom[
                            'incompatible_materials'
                        ]
                        ?? config(
                            'sds.defaults.incompatible_materials'
                        ),

                    'flash_point_c' =>
                        $physical[
                            'flash_point_c'
                        ] ?? null,
                ],

                '7.3' => [
                    'specific_end_use' =>
                        $profile?->identified_use
                        ?? config(
                            'sds.defaults.identified_use'
                        ),
                ],
            ],

            'missing' =>
                [],
        ];
    }

    private function section8(
        array $classification,
        ?SdsFormulaProfile $profile
    ): array {
        $auto =
            $this->autoFill->defaults(
                $profile
            );

        $custom =
            $this->autoFill->merge(
                $auto['exposure_controls'],
                $profile?->exposure_controls
                    ?? []
            );

        $hazards =
            $classification[
                'classification'
            ][
                'hazard_statements'
            ] ?? [];

        $eye =
            $custom['eye_protection']
            ?? (
                in_array(
                    'H318',
                    $hazards,
                    true
                ) ||
                in_array(
                    'H314',
                    $hazards,
                    true
                )
                    ? 'Use tightly fitting safety goggles and face protection where splashing is possible.'
                    : config(
                        'sds.defaults.eye_protection'
                    )
            );

        $respiratory =
            $custom[
                'respiratory_protection'
            ]
            ?? (
                collect($hazards)->intersect(
                    [
                        'H330',
                        'H331',
                        'H332',
                        'H334',
                        'H335',
                        'H336',
                    ]
                )->isNotEmpty()
                    ? 'Provide effective ventilation. Where adequate control cannot be achieved, use respiratory protective equipment suitable for the exposure.'
                    : config(
                        'sds.defaults.respiratory_protection'
                    )
            );

        return [
            'number' => 8,

            'title' =>
                'Maruz kalma kontrolleri / kişisel korunma',

            'status' =>
                'ready',

            'content' => [
                '8.1' => [
                    'oel' =>
                        $custom['oel'],

                    'dnel' =>
                        $custom['dnel'],

                    'pnec' =>
                        $custom['pnec'],
                ],

                '8.2' => [
                    'engineering_controls' =>
                        $custom[
                            'engineering_controls'
                        ]
                        ?? config(
                            'sds.defaults.engineering_controls'
                        ),

                    'respiratory_protection' =>
                        $respiratory,

                    'hand_protection' =>
                        $custom[
                            'hand_protection'
                        ]
                        ?? config(
                            'sds.defaults.hand_protection'
                        ),

                    'eye_protection' =>
                        $eye,

                    'skin_body_protection' =>
                        $custom[
                            'skin_body_protection'
                        ]
                        ?? config(
                            'sds.defaults.skin_body_protection'
                        ),

                    'environmental_exposure_controls' =>
                        $custom[
                            'environmental_controls'
                        ]
                        ?? config(
                            'sds.defaults.environmental_controls'
                        ),
                ],
            ],

            'missing' =>
                [],
        ];
    }

    private function section9(
        ?SdsFormulaProfile $profile
    ): array {
        $physical =
            $profile?->physical_properties
            ?? [];

        $auto =
            $this->autoFill->defaults(
                $profile
            );

        $physical =
            $this->autoFill->merge(
                $auto['physical_defaults'],
                $physical
            );

        $required = [
            'physical_state' =>
                'Fiziksel hal',

            'colour' =>
                'Renk / görünüş',

            'density' =>
                'Yoğunluk',

            'flash_point_c' =>
                'Parlama noktası',

            'kinematic_viscosity_40_cst' =>
                '40 °C kinematik viskozite',

            'kinematic_viscosity_100_cst' =>
                '100 °C kinematik viskozite',

            'pour_point_c' =>
                'Akma noktası',
        ];

        $missing = [];

        foreach ($required as $key => $label) {
            if (
                !array_key_exists(
                    $key,
                    $physical
                ) ||
                $physical[$key] === ''
            ) {
                $missing[] =
                    $label . ' eksik.';
            }
        }

        return [
            'number' => 9,

            'title' =>
                'Fiziksel ve kimyasal özellikler',

            'status' =>
                empty($missing)
                    ? 'ready'
                    : 'needs_data',

            'content' => [
                '9.1' => [
                    'physical_state' =>
                        $physical[
                            'physical_state'
                        ] ?? null,

                    'colour' =>
                        $physical[
                            'colour'
                        ] ?? null,

                    'odour' =>
                        $physical[
                            'odour'
                        ] ?? null,

                    'ph' =>
                        $physical[
                            'ph'
                        ] ?? null,

                    'flash_point_c' =>
                        $physical[
                            'flash_point_c'
                        ] ?? null,

                    'density' =>
                        $physical[
                            'density'
                        ] ?? null,

                    'water_solubility' =>
                        $physical[
                            'water_solubility'
                        ] ?? null,

                    'flammability' =>
                        $physical[
                            'flammability'
                        ] ?? null,

                    'kinematic_viscosity_40_cst' =>
                        $physical[
                            'kinematic_viscosity_40_cst'
                        ] ?? null,

                    'kinematic_viscosity_100_cst' =>
                        $physical[
                            'kinematic_viscosity_100_cst'
                        ] ?? null,

                    'pour_point_c' =>
                        $physical[
                            'pour_point_c'
                        ] ?? null,

                    'particle_characteristics' =>
                        $physical[
                            'particle_characteristics'
                        ] ?? null,
                ],

                '9.2' => [
                    'other_information' =>
                        'Other properties are generated from product-family defaults unless a product-specific override is entered.',
                ],
            ],

            'missing' =>
                $missing,
        ];
    }

    private function section10(
        ?SdsFormulaProfile $profile
    ): array {
        $custom =
            $this->autoFill->merge($this->autoFill->defaults($profile)['stability_reactivity'] ?? [], $profile?->stability_reactivity ?? []);

        return [
            'number' => 10,

            'title' =>
                'Kararlılık ve tepkime',

            'status' =>
                'ready',

            'content' => [
                '10.1' => [
                    'reactivity' =>
                        $custom['reactivity']
                        ?? config(
                            'sds.defaults.reactivity'
                        ),
                ],

                '10.2' => [
                    'chemical_stability' =>
                        $custom[
                            'chemical_stability'
                        ]
                        ?? config(
                            'sds.defaults.chemical_stability'
                        ),
                ],

                '10.3' => [
                    'hazardous_reactions' =>
                        $custom[
                            'hazardous_reactions'
                        ]
                        ?? config(
                            'sds.defaults.hazardous_reactions'
                        ),
                ],

                '10.4' => [
                    'conditions_to_avoid' =>
                        $custom[
                            'conditions_to_avoid'
                        ]
                        ?? config(
                            'sds.defaults.conditions_to_avoid'
                        ),
                ],

                '10.5' => [
                    'incompatible_materials' =>
                        $custom[
                            'incompatible_materials'
                        ]
                        ?? config(
                            'sds.defaults.incompatible_materials'
                        ),
                ],

                '10.6' => [
                    'hazardous_decomposition_products' =>
                        $custom[
                            'hazardous_decomposition_products'
                        ]
                        ?? config(
                            'sds.defaults.hazardous_decomposition'
                        ),
                ],
            ],

            'missing' =>
                [],
        ];
    }

    private function section11(
        array $analysis,
        array $classification,
        ?SdsFormulaProfile $profile
    ): array {
        $hazards =
            $classification[
                'classification'
            ][
                'hazard_classes'
            ] ?? [];

        $custom =
            $this->autoFill->merge($this->autoFill->defaults($profile)['toxicology'] ?? [], $profile?->toxicology ?? []);

        $summary =
            $this->toxicologySummary(
                $hazards
            );

        return [
            'number' => 11,

            'title' =>
                'Toksikolojik bilgiler',

            'status' =>
                (
                    (int) (
                        $analysis[
                            'summary'
                        ][
                            'error_count'
                        ] ?? 0
                    )
                ) > 0
                    ? 'blocked'
                    : 'ready',

            'content' => [
                '11.1' => [
                    'acute_toxicity' =>
                        $summary[
                            'acute_toxicity'
                        ],

                    'skin_corrosion_irritation' =>
                        $summary[
                            'skin'
                        ],

                    'serious_eye_damage_irritation' =>
                        $summary[
                            'eye'
                        ],

                    'respiratory_skin_sensitisation' =>
                        $summary[
                            'sensitisation'
                        ],

                    'germ_cell_mutagenicity' =>
                        $summary[
                            'mutagenicity'
                        ],

                    'carcinogenicity' =>
                        $summary[
                            'carcinogenicity'
                        ],

                    'reproductive_toxicity' =>
                        $summary[
                            'reproductive'
                        ],

                    'stot_single_exposure' =>
                        $summary[
                            'stot_se'
                        ],

                    'stot_repeated_exposure' =>
                        $summary[
                            'stot_re'
                        ],

                    'aspiration_hazard' =>
                        $summary[
                            'aspiration'
                        ],

                    'additional_information' =>
                        $custom[
                            'additional_information'
                        ] ?? null,
                ],

                '11.2' => [
                    'endocrine_disrupting_properties' =>
                        $custom[
                            'endocrine_disrupting'
                        ] ?? 'No product-specific conclusion is stored in the SDS module.',
                ],
            ],

            'missing' =>
                [],
        ];
    }

    private function section12(
        array $classification,
        ?SdsFormulaProfile $profile
    ): array {
        $custom =
            $this->autoFill->merge($this->autoFill->defaults($profile)['ecology'] ?? [], $profile?->ecology ?? []);

        $hazards =
            $classification[
                'classification'
            ][
                'hazard_classes'
            ] ?? [];

        $aquatic = collect(
            $hazards
        )->filter(
            fn ($row) =>
                str_starts_with(
                    (string) (
                        $row[
                            'hazard_class'
                        ] ?? ''
                    ),
                    'Aquatic'
                )
        )->values()->all();

        return [
            'number' => 12,

            'title' =>
                'Ekolojik bilgiler',

            'status' =>
                'ready',

            'content' => [
                '12.1' => [
                    'aquatic_classification' =>
                        $aquatic,

                    'toxicity_data' =>
                        $custom[
                            'toxicity_data'
                        ] ?? 'No product-specific ecotoxicity test data is stored in the SDS module.',
                ],

                '12.2' => [
                    'persistence_degradability' =>
                        $custom[
                            'persistence_degradability'
                        ] ?? 'No product-specific data available.',
                ],

                '12.3' => [
                    'bioaccumulative_potential' =>
                        $custom[
                            'bioaccumulative_potential'
                        ] ?? 'No product-specific data available.',
                ],

                '12.4' => [
                    'mobility_in_soil' =>
                        $custom[
                            'mobility_in_soil'
                        ] ?? 'No product-specific data available.',
                ],

                '12.5' => [
                    'pbt_vpvb_assessment' =>
                        $custom[
                            'pbt_vpvb_assessment'
                        ] ?? 'No product-specific assessment is stored in the SDS module.',
                ],

                '12.6' => [
                    'endocrine_disrupting_properties' =>
                        $custom[
                            'endocrine_disrupting'
                        ] ?? 'No product-specific conclusion is stored in the SDS module.',
                ],

                '12.7' => [
                    'other_adverse_effects' =>
                        $custom[
                            'other_adverse_effects'
                        ] ?? 'No additional information available.',
                ],
            ],

            'missing' =>
                [],
        ];
    }

    private function section13(
        ?SdsFormulaProfile $profile
    ): array {
        $custom =
            $this->autoFill->merge($this->autoFill->defaults($profile)['disposal'] ?? [], $profile?->disposal ?? []);

        return [
            'number' => 13,

            'title' =>
                'Bertaraf etme bilgileri',

            'status' =>
                'ready',

            'content' => [
                '13.1' => [
                    'waste_treatment_methods' =>
                        $custom[
                            'waste_treatment_methods'
                        ]
                        ?? config(
                            'sds.defaults.disposal'
                        ),

                    'waste_code' =>
                        $custom[
                            'waste_code'
                        ] ?? 'To be assigned by the waste producer according to actual use and contamination history.',

                    'contaminated_packaging' =>
                        $custom[
                            'contaminated_packaging'
                        ] ?? 'Drain containers thoroughly and manage contaminated packaging through an authorised waste route.',
                ],
            ],

            'missing' =>
                [],
        ];
    }

    private function section14(
        ?SdsFormulaProfile $profile
    ): array {
        $auto =
            $this->autoFill->defaults(
                $profile
            );

        $transport =
            $this->autoFill->merge(
                $auto['transport'],
                $profile?->transport
                    ?? []
            );

        $status =
            $transport['status']
            ?? 'not_regulated';

        if ($status === 'unknown') {
            return [
                'number' => 14,

                'title' =>
                    'Taşımacılık bilgileri',

                'status' =>
                    'needs_data',

                'content' =>
                    [],

                'missing' => [
                    'Taşımacılık durumu seçilmeli: tehlikeli değil veya tehlikeli madde.',
                ],
            ];
        }

        if ($status === 'not_regulated') {
            return [
                'number' => 14,

                'title' =>
                    'Taşımacılık bilgileri',

                'status' =>
                    'ready',

                'content' => [
                    '14.1' => [
                        'un_number' =>
                            'Not applicable',
                    ],

                    '14.2' => [
                        'proper_shipping_name' =>
                            'Not applicable',
                    ],

                    '14.3' => [
                        'transport_hazard_class' =>
                            'Not applicable',
                    ],

                    '14.4' => [
                        'packing_group' =>
                            'Not applicable',
                    ],

                    '14.5' => [
                        'environmental_hazards' =>
                            $transport[
                                'environmental_hazards'
                            ] ?? 'Not classified as dangerous goods for transport based on the entered transport assessment.',
                    ],

                    '14.6' => [
                        'special_precautions' =>
                            $transport[
                                'special_precautions'
                            ] ?? 'Prevent leakage and secure containers during transport.',
                    ],

                    '14.7' => [
                        'maritime_transport_in_bulk' =>
                            $transport[
                                'bulk_transport'
                            ] ?? 'Not applicable / not determined.',
                    ],
                ],

                'missing' =>
                    [],
            ];
        }

        $required = [
            'un_number' =>
                'UN numarası',
            'proper_shipping_name' =>
                'Uygun taşıma adı',
            'hazard_class' =>
                'Taşıma tehlike sınıfı',
        ];

        $missing = [];

        foreach ($required as $key => $label) {
            if (empty($transport[$key])) {
                $missing[] =
                    $label . ' eksik.';
            }
        }

        return [
            'number' => 14,

            'title' =>
                'Taşımacılık bilgileri',

            'status' =>
                empty($missing)
                    ? 'ready'
                    : 'needs_data',

            'content' => [
                '14.1' => [
                    'un_number' =>
                        $transport[
                            'un_number'
                        ] ?? null,
                ],

                '14.2' => [
                    'proper_shipping_name' =>
                        $transport[
                            'proper_shipping_name'
                        ] ?? null,
                ],

                '14.3' => [
                    'transport_hazard_class' =>
                        $transport[
                            'hazard_class'
                        ] ?? null,
                ],

                '14.4' => [
                    'packing_group' =>
                        $transport[
                            'packing_group'
                        ] ?? null,
                ],

                '14.5' => [
                    'environmental_hazards' =>
                        $transport[
                            'environmental_hazards'
                        ] ?? null,
                ],

                '14.6' => [
                    'special_precautions' =>
                        $transport[
                            'special_precautions'
                        ] ?? null,
                ],

                '14.7' => [
                    'maritime_transport_in_bulk' =>
                        $transport[
                            'bulk_transport'
                        ] ?? null,
                ],
            ],

            'missing' =>
                $missing,
        ];
    }

    private function section15(
        array $analysis,
        string $regulationProfile,
        ?SdsFormulaProfile $profile
    ): array {
        $records =
            $this->allRegulatoryRecords(
                $analysis
            );

        $statuses = [
            'reach_registered' =>
                [],

            'svhc' =>
                [],

            'annex_xiv' =>
                [],

            'annex_xvii' =>
                [],
        ];

        foreach (
            $analysis[
                'mixture_components'
            ] ?? []
            as $component
        ) {
            $name =
                $component[
                    'official_preferred_name'
                ]
                ?? $component[
                    'chemical_name'
                ]
                ?? '-';

            $cas =
                $component[
                    'official_cas_no'
                ]
                ?? $component[
                    'cas_no'
                ]
                ?? null;

            foreach (
                $component[
                    'regulatory'
                ][
                    'all_current_records'
                ] ?? []
                as $record
            ) {
                foreach (
                    array_keys(
                        $statuses
                    )
                    as $flag
                ) {
                    if (
                        !empty(
                            $record[$flag]
                        )
                    ) {
                        $statuses[$flag][] = [
                            'name' =>
                                $name,
                            'cas_no' =>
                                $cas,
                            'source' =>
                                $record[
                                    'source_name'
                                ] ?? null,
                            'reference' =>
                                $record[
                                    'source_reference'
                                ] ?? null,
                        ];
                    }
                }
            }
        }

        foreach ($statuses as $key => $rows) {
            $statuses[$key] =
                collect($rows)
                    ->unique(
                        fn ($row) =>
                            (
                                $row['cas_no']
                                ?? $row['name']
                            )
                            . '|'
                            . (
                                $row['source']
                                ?? ''
                            )
                    )
                    ->values()
                    ->all();
        }

        return [
            'number' => 15,

            'title' =>
                'Mevzuat bilgileri',

            'status' =>
                'ready',

            'content' => [
                '15.1' => [
                    'regulation_profile' =>
                        $regulationProfile,

                    'reach_registered_substances' =>
                        $statuses[
                            'reach_registered'
                        ],

                    'svhc_candidate_list' =>
                        $statuses[
                            'svhc'
                        ],

                    'annex_xiv' =>
                        $statuses[
                            'annex_xiv'
                        ],

                    'annex_xvii' =>
                        $statuses[
                            'annex_xvii'
                        ],

                    'additional_regulatory_information' =>
                        $profile?->regulatory[
                            'additional_information'
                        ] ?? null,
                ],

                '15.2' => [
                    'chemical_safety_assessment' =>
                        $profile?->regulatory[
                            'chemical_safety_assessment'
                        ]
                        ?? config(
                            'sds.defaults.chemical_safety_assessment'
                        ),
                ],

                'sources' =>
                    $records,
            ],

            'missing' =>
                [],
        ];
    }

    private function section16(
        array $analysis,
        array $classification,
        ?SdsFormulaProfile $profile
    ): array {
        $revision =
            $profile?->revision
            ?? [];

        $hazardStatements =
            $classification[
                'classification'
            ][
                'hazard_statements'
            ] ?? [];

        $hTexts =
            config(
                'sds.h_statements',
                []
            );

        $hazardTextRows = [];

        foreach (
            $hazardStatements
            as $code
        ) {
            $hazardTextRows[] = [
                'code' =>
                    $code,

                'text' =>
                    $hTexts[$code]
                    ?? null,
            ];
        }

        return [
            'number' => 16,

            'title' =>
                'Diğer bilgiler',

            'status' =>
                'ready',

            'content' => [
                'revision' => [
                    'version' =>
                        $revision[
                            'version'
                        ] ?? '1',

                    'issue_date' =>
                        $revision[
                            'issue_date'
                        ] ?? now()->format(
                            'Y-m-d'
                        ),

                    'revision_date' =>
                        $revision[
                            'revision_date'
                        ] ?? null,

                    'change_summary' =>
                        $revision[
                            'change_summary'
                        ] ?? 'Initial SDS draft generated by the internal SDS module.',
                ],

                'prepared_by' =>
                    config(
                        'sds.prepared_by',
                        []
                    ),

                'hazard_statement_texts' =>
                    $hazardTextRows,

                'abbreviations' =>
                    config(
                        'sds.abbreviations',
                        []
                    ),

                'sources' =>
                    $this->allRegulatoryRecords(
                        $analysis
                    ),

                'disclaimer' =>
                    'The SDS draft is generated from the formula, supplier SDS component data, regulatory database records and product-specific supplemental information. Final legal and technical review is required before issue.',
            ],

            'missing' =>
                [],
        ];
    }

    private function symptomText(
        array $hazards
    ): string {
        $parts = [];

        $map = [
            'H304' =>
                'Aspiration into the lungs may cause serious lung effects.',

            'H314' =>
                'Severe skin burns and serious eye damage may occur.',

            'H317' =>
                'An allergic skin reaction may occur in sensitised persons.',

            'H318' =>
                'Serious eye damage may occur.',

            'H334' =>
                'Allergy or asthma symptoms or breathing difficulties may occur after inhalation.',

            'H335' =>
                'Respiratory irritation may occur.',

            'H336' =>
                'Drowsiness or dizziness may occur.',
        ];

        foreach ($map as $code => $text) {
            if (
                in_array(
                    $code,
                    $hazards,
                    true
                )
            ) {
                $parts[] =
                    $text;
            }
        }

        return empty($parts)
            ? 'No specific acute symptoms are expected under normal conditions of use; prolonged or repeated contact may cause discomfort or irritation.'
            : implode(
                ' ',
                $parts
            );
    }

    private function toxicologySummary(
        array $hazards
    ): array {
        $byClass = collect(
            $hazards
        )->groupBy(
            fn ($row) =>
                $row[
                    'hazard_class'
                ] ?? 'Other'
        );

        $result = [
            'acute_toxicity' =>
                'Not classified by the current mixture calculation.',

            'skin' =>
                'Not classified by the current mixture calculation.',

            'eye' =>
                'Not classified by the current mixture calculation.',

            'sensitisation' =>
                'Not classified by the current mixture calculation.',

            'mutagenicity' =>
                'Not classified by the current mixture calculation.',

            'carcinogenicity' =>
                'Not classified by the current mixture calculation.',

            'reproductive' =>
                'Not classified by the current mixture calculation.',

            'stot_se' =>
                'Not classified by the current mixture calculation.',

            'stot_re' =>
                'Not classified by the current mixture calculation.',

            'aspiration' =>
                'Not classified by the current mixture calculation.',
        ];

        foreach ($hazards as $row) {
            $class =
                $row[
                    'hazard_class'
                ] ?? '';

            $category =
                $row[
                    'category'
                ] ?? '';

            $h =
                $row[
                    'h_statement'
                ] ?? '';

            $text =
                trim(
                    $class
                    . ' '
                    . $category
                    . (
                        $h
                            ? ' (' . $h . ')'
                            : ''
                    )
                );

            if ($class === 'Acute Tox.') {
                $result[
                    'acute_toxicity'
                ] = $text;

                if (
                    isset(
                        $row[
                            'calculated_value'
                        ]
                    )
                ) {
                    $result[
                        'acute_toxicity'
                    ] .=
                        ' - calculated ATE: '
                        . $row[
                            'calculated_value'
                        ]
                        . ' '
                        . (
                            $row[
                                'unit'
                            ] ?? ''
                        );
                }
            }

            if (
                in_array(
                    $class,
                    [
                        'Skin Corr.',
                        'Skin Irrit.',
                    ],
                    true
                )
            ) {
                $result[
                    'skin'
                ] = $text;
            }

            if (
                in_array(
                    $class,
                    [
                        'Eye Dam.',
                        'Eye Irrit.',
                    ],
                    true
                )
            ) {
                $result[
                    'eye'
                ] = $text;
            }

            if (
                in_array(
                    $class,
                    [
                        'Skin Sens.',
                        'Resp. Sens.',
                    ],
                    true
                )
            ) {
                $result[
                    'sensitisation'
                ] = $text;
            }

            if ($class === 'Muta.') {
                $result[
                    'mutagenicity'
                ] = $text;
            }

            if ($class === 'Carc.') {
                $result[
                    'carcinogenicity'
                ] = $text;
            }

            if (
                in_array(
                    $class,
                    [
                        'Repr.',
                        'Lact.',
                    ],
                    true
                )
            ) {
                $result[
                    'reproductive'
                ] = $text;
            }

            if ($class === 'STOT SE') {
                $result[
                    'stot_se'
                ] = $text;
            }

            if ($class === 'STOT RE') {
                $result[
                    'stot_re'
                ] = $text;
            }

            if ($class === 'Asp. Tox.') {
                $result[
                    'aspiration'
                ] = $text;
            }
        }

        return $result;
    }

    private function allRegulatoryRecords(
        array $analysis
    ): array {
        $rows = [];

        foreach (
            $analysis[
                'mixture_components'
            ] ?? []
            as $component
        ) {
            foreach (
                $component[
                    'regulatory'
                ][
                    'all_current_records'
                ] ?? []
                as $record
            ) {
                $rows[] = [
                    'source_type' =>
                        $record[
                            'source_type'
                        ] ?? null,

                    'source_name' =>
                        $record[
                            'source_name'
                        ] ?? null,

                    'source_version' =>
                        $record[
                            'source_version'
                        ] ?? null,

                    'source_date' =>
                        $record[
                            'source_date'
                        ] ?? null,

                    'source_reference' =>
                        $record[
                            'source_reference'
                        ] ?? null,

                    'source_url' =>
                        $record[
                            'source_url'
                        ] ?? null,
                ];
            }
        }

        return collect($rows)
            ->unique(
                fn ($row) =>
                    (
                        $row[
                            'source_type'
                        ]
                        ?? ''
                    )
                    . '|'
                    . (
                        $row[
                            'source_version'
                        ]
                        ?? ''
                    )
                    . '|'
                    . (
                        $row[
                            'source_reference'
                        ]
                        ?? ''
                    )
            )
            ->values()
            ->all();
    }
}
