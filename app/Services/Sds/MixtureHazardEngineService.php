<?php

namespace App\Services\Sds;

class MixtureHazardEngineService
{
    public function classify(array $analysis, array $physicalProperties = []): array
    {
        $issues = [];
        $ruleDecisions = [];

        $components = $this->buildComponents(
            $analysis['mixture_components'] ?? []
        );

        $components = $this->applyAnnexViNotes(
            $components,
            $ruleDecisions,
            $issues
        );

        $results = [];

        $this->evaluateAcuteToxicity(
            $components,
            $results,
            $issues
        );

        $this->evaluateSkinAndEye(
            $components,
            $results,
            $issues
        );

        $this->evaluateSensitisation(
            $components,
            $results,
            $issues
        );

        $this->evaluateCmr(
            $components,
            $results,
            $issues
        );

        $this->evaluateStot(
            $components,
            $results,
            $issues
        );

        $this->evaluateAspiration(
            $components,
            $results,
            $issues,
            $physicalProperties,
            $ruleDecisions
        );

        $this->evaluateAquatic(
            $components,
            $results
        );

        $results = collect($results)
            ->unique(function ($row) {
                return
                    ($row['hazard_class'] ?? '') . '|' .
                    ($row['category'] ?? '') . '|' .
                    ($row['h_statement'] ?? '');
            })
            ->values()
            ->all();

        $hazardStatements = collect($results)
            ->pluck('h_statement')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $pictograms = $this->derivePictograms($results);
        $signalWord = $this->deriveSignalWord($results);

        $collectorErrorCount = (int) (
            $analysis['summary']['error_count'] ?? 0
        );

        $engineErrorCount = collect($issues)
            ->where('severity', 'error')
            ->count();

        return [
            'status' => [
                'calculation_completed' => true,
                'review_required' => true,
                'collector_error_count' => $collectorErrorCount,
                'engine_error_count' => $engineErrorCount,
                'ready_for_section_2_3' =>
                    $collectorErrorCount === 0 &&
                    $engineErrorCount === 0,
            ],

            'classification' => [
                'hazard_classes' => $results,
                'hazard_statements' => $hazardStatements,
                'pictograms' => $pictograms,
                'signal_word' => $signalWord,
            ],

            'components_used' => $components,
            'rule_decisions' => $ruleDecisions,
            'issues' => array_values($issues),

            'notes' => [
                'Automatic preliminary mixture classification.',
                'Annex VI special notes are evaluated before generic mixture rules.',
                'Specific concentration limits take precedence when a usable SCL is available.',
                'Physical hazards are not inferred from composition alone.',
                'Human review is required before SDS approval.',
            ],
        ];
    }

    private function buildComponents(array $rows): array
    {
        $result = [];

        foreach ($rows as $row) {
            $records =
                $row['regulatory']['requested_profile_records']
                ?? [];

            $recordsWithHazardData = collect($records)
                ->filter(function ($record) {
                    return
                        !empty($record['hazard_classifications']) ||
                        !empty($record['hazard_statements']) ||
                        !empty($record['specific_concentration_limits']) ||
                        !empty($record['ate_data']) ||
                        ($record['m_factor_acute'] ?? null) !== null ||
                        ($record['m_factor_chronic'] ?? null) !== null;
                })
                ->values();

            $regulatoryClasses = $recordsWithHazardData
                ->flatMap(
                    fn ($record) =>
                        $record['hazard_classifications'] ?? []
                )
                ->filter()
                ->unique()
                ->values()
                ->all();

            $regulatoryH = $recordsWithHazardData
                ->flatMap(
                    fn ($record) =>
                        $record['hazard_statements'] ?? []
                )
                ->filter()
                ->unique()
                ->values()
                ->all();

            $regulatoryScl = $recordsWithHazardData
                ->flatMap(function ($record) {
                    return $this->flattenScl(
                        $record['specific_concentration_limits']
                        ?? []
                    );
                })
                ->filter()
                ->values()
                ->all();

            $regulatoryAte = $recordsWithHazardData
                ->flatMap(function ($record) {
                    $value = $record['ate_data'] ?? [];

                    return is_array($value)
                        ? $value
                        : [];
                })
                ->filter()
                ->values()
                ->all();

            $supplier = $row['supplier'] ?? [];

            $classes = count($regulatoryClasses)
                ? $regulatoryClasses
                : ($supplier['hazard_classifications'] ?? []);

            $hStatements = count($regulatoryH)
                ? $regulatoryH
                : ($supplier['h_statements'] ?? []);

            $scl = count($regulatoryScl)
                ? $regulatoryScl
                : $this->flattenScl(
                    $supplier['specific_concentration_limits'] ?? []
                );

            $ate = count($regulatoryAte)
                ? $regulatoryAte
                : ($supplier['ate_data'] ?? []);

            $mAcute = $recordsWithHazardData
                ->pluck('m_factor_acute')
                ->filter(fn ($v) => $v !== null)
                ->first();

            $mChronic = $recordsWithHazardData
                ->pluck('m_factor_chronic')
                ->filter(fn ($v) => $v !== null)
                ->first();

            if ($mAcute === null) {
                $mAcute = $supplier['m_factor_acute'] ?? null;
            }

            if ($mChronic === null) {
                $mChronic = $supplier['m_factor_chronic'] ?? null;
            }

            $result[] = [
                'raw_material_id' =>
                    $row['raw_material_id'] ?? null,

                'raw_material_name' =>
                    $row['raw_material_name'] ?? null,

                'sds_component_id' =>
                    $row['sds_component_id'] ?? null,

                'regulatory_substance_id' =>
                    $row['regulatory_substance_id'] ?? null,

                'chemical_name' =>
                    $row['official_preferred_name']
                    ?? $row['chemical_name']
                    ?? null,

                'cas_no' =>
                    $row['official_cas_no']
                    ?? $row['cas_no']
                    ?? null,

                'effective_min' => $this->number(
                    $row['effective_concentration_min']
                    ?? null
                ),

                'effective_max' => $this->number(
                    $row['effective_concentration_max']
                    ?? null
                ),

                'hazard_classifications' =>
                    $this->normalizeStrings($classes),

                'hazard_statements' =>
                    $this->normalizeStrings($hStatements),

                'specific_concentration_limits' =>
                    $this->normalizeStrings($scl),

                'ate_data' =>
                    is_array($ate)
                        ? $ate
                        : [],

                'm_factor_acute' =>
                    $this->number($mAcute) ?? 1.0,

                'm_factor_chronic' =>
                    $this->number($mChronic) ?? 1.0,

                'classification_source' =>
                    count($regulatoryClasses)
                        ? 'regulatory'
                        : 'supplier_sds',

                'supplier_regulatory_notes' =>
                    is_array(
                        $supplier['regulatory_notes'] ?? null
                    )
                        ? $supplier['regulatory_notes']
                        : [],

                'supplier_note' =>
                    $supplier['supplier_note'] ?? null,

                'regulatory_records' =>
                    is_array($records)
                        ? $records
                        : [],
            ];
        }

        return $result;
    }

    private function applyAnnexViNotes(
        array $components,
        array &$decisions,
        array &$issues
    ): array {
        foreach ($components as $index => $component) {
            $notes = $this->annexViNotes(
                $component['regulatory_records']
            );

            if (empty($notes)) {
                continue;
            }

            foreach ($notes as $note) {
                $decision = $this->evaluateAnnexNote(
                    $component,
                    $note
                );

                if (!$decision) {
                    continue;
                }

                $decisions[] = $decision;

                if (
                    ($decision['action'] ?? null)
                    === 'exclude_carcinogenicity'
                ) {
                    $components[$index]['hazard_classifications'] =
                        array_values(
                            array_filter(
                                $components[$index]
                                    ['hazard_classifications'],
                                fn ($value) =>
                                    !preg_match(
                                        '/\bcarc\.?/i',
                                        $value
                                    )
                            )
                        );

                    $components[$index]['hazard_statements'] =
                        array_values(
                            array_filter(
                                $components[$index]
                                    ['hazard_statements'],
                                fn ($value) =>
                                    !preg_match(
                                        '/^H35[01]/i',
                                        trim($value)
                                    )
                            )
                        );
                }

                if (
                    ($decision['action'] ?? null)
                    === 'exclude_carc_mut'
                ) {
                    $components[$index]['hazard_classifications'] =
                        array_values(
                            array_filter(
                                $components[$index]
                                    ['hazard_classifications'],
                                fn ($value) =>
                                    !preg_match(
                                        '/\b(carc|muta)\.?/i',
                                        $value
                                    )
                            )
                        );

                    $components[$index]['hazard_statements'] =
                        array_values(
                            array_filter(
                                $components[$index]
                                    ['hazard_statements'],
                                fn ($value) =>
                                    !preg_match(
                                        '/^(H34[01]|H35[01])/i',
                                        trim($value)
                                    )
                            )
                        );
                }

                if (
                    ($decision['status'] ?? null)
                    === 'review_required'
                ) {
                    $issues[] = [
                        // Evidence missing: retain conservative hazard classification,
                        // but do not block preparation of the SDS.
                        'severity' => strtoupper($note) === 'L' ? 'warning' : 'error',
                        'code' =>
                            'ANNEX_VI_NOTE_' .
                            strtoupper($note) .
                            '_EVIDENCE_REQUIRED',
                        'message' =>
                            $decision['message'],
                        'context' => [
                            'chemical_name' =>
                                $component['chemical_name'],
                            'cas_no' =>
                                $component['cas_no'],
                            'note' => strtoupper($note),
                            'raw_material_id' => $component['raw_material_id'] ?? null,
                            'sds_component_id' => $component['sds_component_id'] ?? null,
                        ],
                    ];
                }
            }
        }

        return $components;
    }

    private function evaluateAnnexNote(
        array $component,
        string $note
    ): ?array {
        $note = strtoupper($note);

        $base = [
            'raw_material_name' =>
                $component['raw_material_name'] ?? null,
            'raw_material_id' => $component['raw_material_id'] ?? null,
            'sds_component_id' => $component['sds_component_id'] ?? null,
            'chemical_name' =>
                $component['chemical_name'] ?? null,
            'cas_no' =>
                $component['cas_no'] ?? null,
            'note' => $note,
        ];

        if ($note === 'L') {
            $evidence = $this->noteLEvidence($component);

            if ($evidence['proven'] === true) {
                return array_merge($base, [
                    'status' => 'condition_met',
                    'action' =>
                        'exclude_carcinogenicity',
                    'message' =>
                        'Note L: IP 346 ile DMSO ekstraktının %3 altında olduğu kanıtlandı; harmonize kanserojen sınıflandırması uygulanmadı.',
                    'evidence' => $evidence,
                ]);
            }

            if ($evidence['proven'] === false) {
                return array_merge($base, [
                    'status' => 'condition_not_met',
                    'action' => 'keep_classification',
                    'message' =>
                        'Note L koşulu sağlanmadı; harmonize kanserojen sınıflandırması korunuyor.',
                    'evidence' => $evidence,
                ]);
            }

            return array_merge($base, [
                'status' => 'review_required',
                'action' => 'keep_classification',
                'message' =>
                    'Note L mevcut ancak IP 346 / DMSO < %3 kanıtı bulunamadı. H350 kanıt gelene kadar korunur. SDS mevcut ihtiyatlı sınıflandırmayla hazırlanabilir; kanıt sonradan eklenirse yeniden hesaplanır.',
                'evidence' => $evidence,
            ]);
        }

        if (in_array($note, ['J', 'P'], true)) {
            $evidence = $this->numericEvidence(
                $component,
                ['benzene_percent', 'benzene_w_w_percent'],
                '/benzene.*?(?:<|less\s+than)\s*([0-9]+(?:[.,][0-9]+)?)\s*%/i',
                0.1
            );

            if ($evidence['proven'] === true) {
                return array_merge($base, [
                    'status' => 'condition_met',
                    'action' => 'exclude_carc_mut',
                    'message' =>
                        "Note {$note}: benzene <%0.1 koşulu sağlandı; ilgili harmonize Carc./Muta. sınıflandırması uygulanmadı.",
                    'evidence' => $evidence,
                ]);
            }

            return array_merge($base, [
                'status' => 'review_required',
                'action' => 'keep_classification',
                'message' =>
                    "Note {$note} için benzene <%0.1 kanıtı bulunamadı; harmonize sınıflandırma korunur.",
                'evidence' => $evidence,
            ]);
        }

        if ($note === 'K') {
            $evidence = $this->numericEvidence(
                $component,
                [
                    'butadiene_percent',
                    '1_3_butadiene_percent',
                ],
                '/(?:1,3-|1\.3-|1\s*3\s*)?butadiene.*?(?:<|less\s+than)\s*([0-9]+(?:[.,][0-9]+)?)\s*%/i',
                0.1
            );

            if ($evidence['proven'] === true) {
                return array_merge($base, [
                    'status' => 'condition_met',
                    'action' => 'exclude_carc_mut',
                    'message' =>
                        'Note K: 1,3-butadiene <%0.1 koşulu sağlandı; ilgili harmonize Carc./Muta. sınıflandırması uygulanmadı.',
                    'evidence' => $evidence,
                ]);
            }

            return array_merge($base, [
                'status' => 'review_required',
                'action' => 'keep_classification',
                'message' =>
                    'Note K için 1,3-butadiene <%0.1 kanıtı bulunamadı; harmonize sınıflandırma korunur.',
                'evidence' => $evidence,
            ]);
        }

        if ($note === 'M') {
            $evidence = $this->numericEvidence(
                $component,
                [
                    'benzo_a_pyrene_percent',
                    'benzoapyrene_percent',
                ],
                '/benzo\s*\[?a\]?\s*[- ]?pyrene.*?(?:<|less\s+than)\s*([0-9]+(?:[.,][0-9]+)?)\s*%/i',
                0.005
            );

            if ($evidence['proven'] === true) {
                return array_merge($base, [
                    'status' => 'condition_met',
                    'action' =>
                        'exclude_carcinogenicity',
                    'message' =>
                        'Note M: benzo[a]pyrene <%0.005 koşulu sağlandı; harmonize kanserojen sınıflandırması uygulanmadı.',
                    'evidence' => $evidence,
                ]);
            }

            return array_merge($base, [
                'status' => 'review_required',
                'action' => 'keep_classification',
                'message' =>
                    'Note M için benzo[a]pyrene <%0.005 kanıtı bulunamadı; harmonize kanserojen sınıflandırması korunur.',
                'evidence' => $evidence,
            ]);
        }

        if ($note === 'N') {
            $data =
                $component['supplier_regulatory_notes']
                ?? [];

            $historyKnown = filter_var(
                $data['full_refining_history_known']
                    ?? false,
                FILTER_VALIDATE_BOOLEAN
            );

            $startingNonCarc = filter_var(
                $data[
                    'starting_substance_non_carcinogenic'
                ] ?? false,
                FILTER_VALIDATE_BOOLEAN
            );

            if ($historyKnown && $startingNonCarc) {
                return array_merge($base, [
                    'status' => 'condition_met',
                    'action' =>
                        'exclude_carcinogenicity',
                    'message' =>
                        'Note N koşulları sağlandı; harmonize kanserojen sınıflandırması uygulanmadı.',
                    'evidence' => [
                        'full_refining_history_known' =>
                            true,
                        'starting_substance_non_carcinogenic' =>
                            true,
                    ],
                ]);
            }

            return array_merge($base, [
                'status' => 'review_required',
                'action' => 'keep_classification',
                'message' =>
                    'Note N için tam rafinasyon geçmişi ve başlangıç maddesinin kanserojen olmadığını gösteren kanıt eksik.',
                'evidence' => [
                    'full_refining_history_known' =>
                        $historyKnown,
                    'starting_substance_non_carcinogenic' =>
                        $startingNonCarc,
                ],
            ]);
        }

        return null;
    }

    private function noteLEvidence(
        array $component
    ): array {
        $notes =
            $component['supplier_regulatory_notes']
            ?? [];

        $confirmedBelowThree = filter_var(
            $notes['ip346_dmso_below_3_confirmed']
                ?? false,
            FILTER_VALIDATE_BOOLEAN
        );

        if ($confirmedBelowThree) {
            return [
                'proven' => true,
                'source' => 'component.regulatory_notes',
                'key' => 'ip346_dmso_below_3_confirmed',
                'value' => '<3',
                'criterion' => '< 3%',
            ];
        }

        foreach (
            [
                'ip346_dmso_extract_percent',
                'dmso_extract_percent',
                'dmso_percent',
            ]
            as $key
        ) {
            if (
                array_key_exists($key, $notes) &&
                $this->number($notes[$key]) !== null
            ) {
                $value = $this->number($notes[$key]);

                return [
                    'proven' => $value < 3.0,
                    'source' => 'component.regulatory_notes',
                    'key' => $key,
                    'value' => $value,
                    'criterion' => '< 3%',
                ];
            }
        }

        $text = trim(
            (string) (
                $component['supplier_note']
                ?? ''
            )
        );

        if ($text !== '') {
            if (
                preg_match(
                    '/(?:IP\s*346|DMSO).*?(?:less\s+than|below|<)\s*3(?:[.,]0+)?\s*%/i',
                    $text
                )
            ) {
                return [
                    'proven' => true,
                    'source' => 'supplier_note',
                    'value' => '<3',
                    'criterion' => '< 3%',
                ];
            }

            if (
                preg_match(
                    '/(?:IP\s*346|DMSO).*?([0-9]+(?:[.,][0-9]+)?)\s*%/i',
                    $text,
                    $match
                )
            ) {
                $value = $this->number($match[1]);

                return [
                    'proven' =>
                        $value !== null
                            ? $value < 3.0
                            : null,
                    'source' => 'supplier_note',
                    'value' => $value,
                    'criterion' => '< 3%',
                ];
            }
        }

        return [
            'proven' => null,
            'source' => null,
            'criterion' => '< 3%',
        ];
    }

    private function numericEvidence(
        array $component,
        array $keys,
        string $textPattern,
        float $limit
    ): array {
        $notes =
            $component['supplier_regulatory_notes']
            ?? [];

        foreach ($keys as $key) {
            if (
                array_key_exists($key, $notes) &&
                $this->number($notes[$key]) !== null
            ) {
                $value = $this->number($notes[$key]);

                return [
                    'proven' => $value < $limit,
                    'source' =>
                        'component.regulatory_notes',
                    'key' => $key,
                    'value' => $value,
                    'criterion' => '< ' . $limit . '%',
                ];
            }
        }

        $text = trim(
            (string) (
                $component['supplier_note']
                ?? ''
            )
        );

        if (
            $text !== '' &&
            preg_match(
                $textPattern,
                $text,
                $match
            )
        ) {
            $value = $this->number(
                $match[1] ?? null
            );

            return [
                'proven' =>
                    $value !== null
                        ? $value <= $limit
                        : true,
                'source' => 'supplier_note',
                'value' => $value,
                'criterion' => '< ' . $limit . '%',
            ];
        }

        return [
            'proven' => null,
            'source' => null,
            'criterion' => '< ' . $limit . '%',
        ];
    }

    private function annexViNotes(
        array $records
    ): array {
        $found = [];

        foreach ($records as $record) {
            if (
                ($record['source_type'] ?? null)
                !== 'ECHA_ANNEX_VI'
            ) {
                continue;
            }

            $notes =
                $record['regulatory_notes']['notes']
                ?? null;

            if (!$notes) {
                continue;
            }

            $text = strtoupper(
                trim((string) $notes)
            );

            if (
                preg_match_all(
                    '/\bNOTE\s+([J-NPR])\b/i',
                    $text,
                    $matches
                )
            ) {
                foreach ($matches[1] as $letter) {
                    $found[] = strtoupper($letter);
                }
            }

            foreach (
                preg_split(
                    '/[\s,;\/|]+/',
                    $text
                ) ?: []
                as $token
            ) {
                $token = trim($token);

                if (
                    preg_match(
                        '/^[J-NPR]$/',
                        $token
                    )
                ) {
                    $found[] = $token;
                }
            }
        }

        return array_values(
            array_unique($found)
        );
    }

    private function evaluateAcuteToxicity(
        array $components,
        array &$results,
        array &$issues
    ): void {
        foreach (['oral', 'dermal'] as $route) {
            $denominator = 0.0;
            $unknownAcute = 0.0;

            foreach ($components as $component) {
                $c = $component['effective_max'];

                if ($c === null || $c <= 0) {
                    continue;
                }

                $ate = $this->componentAteForRoute(
                    $component,
                    $route
                );

                if ($ate !== null && $ate > 0) {
                    $denominator += $c / $ate;
                    continue;
                }

                $category =
                    $this->acuteCategoryFromClasses(
                        $component[
                            'hazard_classifications'
                        ],
                        $route
                    );

                if ($category !== null) {
                    $pointEstimate =
                        $this->acutePointEstimate(
                            $route,
                            $category
                        );

                    if ($pointEstimate !== null) {
                        $denominator +=
                            $c / $pointEstimate;
                    } else {
                        $unknownAcute += $c;
                    }
                }
            }

            if ($denominator <= 0) {
                continue;
            }

            $numerator = $unknownAcute > 10
                ? max(
                    0.0,
                    100.0 - $unknownAcute
                )
                : 100.0;

            $ateMix = $numerator / $denominator;

            $category =
                $this->acuteMixtureCategory(
                    $route,
                    $ateMix
                );

            if ($category !== null) {
                $results[] = [
                    'hazard_class' => 'Acute Tox.',
                    'category' => (string) $category,
                    'route' => $route,
                    'h_statement' =>
                        $this->acuteHCode(
                            $route,
                            $category
                        ),
                    'method' => 'ATE calculation',
                    'calculated_value' =>
                        round($ateMix, 6),
                    'unit' => 'mg/kg',
                ];
            }

            if ($unknownAcute >= 1) {
                $issues[] = [
                    'severity' => 'warning',
                    'code' =>
                        'UNKNOWN_ACUTE_TOXICITY',
                    'message' =>
                        ucfirst($route) .
                        ' akut toksisite hesabında kullanılabilir ATE verisi olmayan bileşenler bulunmaktadır.',
                    'context' => [
                        'unknown_percentage' =>
                            round(
                                $unknownAcute,
                                6
                            ),
                    ],
                ];
            }
        }
    }

    private function evaluateSkinAndEye(
        array $components,
        array &$results,
        array &$issues
    ): void {
        $skinCorr = 0.0;
        $skinIrrit = 0.0;
        $eyeDam = 0.0;
        $eyeIrrit = 0.0;

        foreach ($components as $component) {
            $c = $component['effective_max'];

            if ($c === null) {
                continue;
            }

            $classes =
                $component['hazard_classifications'];

            $handledSkinCorr = $this->applyDirectScl(
                $component,
                ['skin corr'],
                'H314',
                $c,
                'Skin Corr.',
                '1',
                $results,
                $issues
            );

            $handledSkinIrrit = $this->applyDirectScl(
                $component,
                ['skin irrit'],
                'H315',
                $c,
                'Skin Irrit.',
                '2',
                $results,
                $issues
            );

            $handledEyeDam = $this->applyDirectScl(
                $component,
                ['eye dam'],
                'H318',
                $c,
                'Eye Dam.',
                '1',
                $results,
                $issues
            );

            $handledEyeIrrit = $this->applyDirectScl(
                $component,
                ['eye irrit'],
                'H319',
                $c,
                'Eye Irrit.',
                '2',
                $results,
                $issues
            );

            if (
                !$handledSkinCorr &&
                $this->hasClass(
                    $classes,
                    '/skin\s*corr/i'
                )
            ) {
                $skinCorr += $c;
            }

            if (
                !$handledSkinIrrit &&
                $this->hasClass(
                    $classes,
                    '/skin\s*irrit/i'
                )
            ) {
                $skinIrrit += $c;
            }

            if (
                !$handledEyeDam &&
                $this->hasClass(
                    $classes,
                    '/eye\s*dam/i'
                )
            ) {
                $eyeDam += $c;
            }

            if (
                !$handledEyeIrrit &&
                $this->hasClass(
                    $classes,
                    '/eye\s*irrit/i'
                )
            ) {
                $eyeIrrit += $c;
            }
        }

        if ($skinCorr >= 5.0) {
            $results[] = [
                'hazard_class' => 'Skin Corr.',
                'category' => '1',
                'h_statement' => 'H314',
                'method' =>
                    'generic summation rule',
                'calculated_value' =>
                    round($skinCorr, 6),
            ];
        } elseif (
            (10 * $skinCorr + $skinIrrit)
            >= 10.0
        ) {
            $results[] = [
                'hazard_class' => 'Skin Irrit.',
                'category' => '2',
                'h_statement' => 'H315',
                'method' =>
                    'generic summation rule',
                'calculated_value' =>
                    round(
                        10 * $skinCorr +
                        $skinIrrit,
                        6
                    ),
            ];
        }

        $eyeSevere = $eyeDam + $skinCorr;

        if ($eyeSevere >= 3.0) {
            $results[] = [
                'hazard_class' => 'Eye Dam.',
                'category' => '1',
                'h_statement' => 'H318',
                'method' =>
                    'generic summation rule',
                'calculated_value' =>
                    round($eyeSevere, 6),
            ];
        } elseif (
            (10 * $eyeSevere + $eyeIrrit)
            >= 10.0
        ) {
            $results[] = [
                'hazard_class' => 'Eye Irrit.',
                'category' => '2',
                'h_statement' => 'H319',
                'method' =>
                    'generic summation rule',
                'calculated_value' =>
                    round(
                        10 * $eyeSevere +
                        $eyeIrrit,
                        6
                    ),
            ];
        }
    }

    private function evaluateSensitisation(
        array $components,
        array &$results,
        array &$issues
    ): void {
        foreach ($components as $component) {
            $c = $component['effective_max'];

            if ($c === null) {
                continue;
            }

            $classes =
                $component['hazard_classifications'];

            if (
                $this->hasClass(
                    $classes,
                    '/resp\.?\s*sens\.?\s*1a/i'
                )
            ) {
                $this->applyThresholdResult(
                    $component,
                    $c,
                    ['resp sens'],
                    'H334',
                    0.1,
                    'Resp. Sens.',
                    '1A',
                    $results,
                    $issues
                );
            } elseif (
                $this->hasClass(
                    $classes,
                    '/resp\.?\s*sens/i'
                )
            ) {
                $this->applyThresholdResult(
                    $component,
                    $c,
                    ['resp sens'],
                    'H334',
                    1.0,
                    'Resp. Sens.',
                    '1',
                    $results,
                    $issues
                );
            }

            if (
                $this->hasClass(
                    $classes,
                    '/skin\s*sens\.?\s*1a/i'
                )
            ) {
                $this->applyThresholdResult(
                    $component,
                    $c,
                    ['skin sens'],
                    'H317',
                    0.1,
                    'Skin Sens.',
                    '1A',
                    $results,
                    $issues
                );
            } elseif (
                $this->hasClass(
                    $classes,
                    '/skin\s*sens/i'
                )
            ) {
                $this->applyThresholdResult(
                    $component,
                    $c,
                    ['skin sens'],
                    'H317',
                    1.0,
                    'Skin Sens.',
                    '1',
                    $results,
                    $issues
                );
            }
        }
    }

    private function evaluateCmr(
        array $components,
        array &$results,
        array &$issues
    ): void {
        foreach ($components as $component) {
            $c = $component['effective_max'];

            if ($c === null) {
                continue;
            }

            $classes =
                $component['hazard_classifications'];

            if (
                $this->hasClass(
                    $classes,
                    '/muta\.?\s*1a/i'
                )
            ) {
                $this->applyThresholdResult(
                    $component,
                    $c,
                    ['muta 1a', 'muta. 1a'],
                    'H340',
                    0.1,
                    'Muta.',
                    '1A',
                    $results,
                    $issues
                );
            } elseif (
                $this->hasClass(
                    $classes,
                    '/muta\.?\s*1b/i'
                )
            ) {
                $this->applyThresholdResult(
                    $component,
                    $c,
                    ['muta 1b', 'muta. 1b'],
                    'H340',
                    0.1,
                    'Muta.',
                    '1B',
                    $results,
                    $issues
                );
            } elseif (
                $this->hasClass(
                    $classes,
                    '/muta\.?\s*2/i'
                )
            ) {
                $this->applyThresholdResult(
                    $component,
                    $c,
                    ['muta 2', 'muta. 2'],
                    'H341',
                    1.0,
                    'Muta.',
                    '2',
                    $results,
                    $issues
                );
            }

            if (
                $this->hasClass(
                    $classes,
                    '/carc\.?\s*1a/i'
                )
            ) {
                $this->applyThresholdResult(
                    $component,
                    $c,
                    ['carc 1a', 'carc. 1a'],
                    'H350',
                    0.1,
                    'Carc.',
                    '1A',
                    $results,
                    $issues
                );
            } elseif (
                $this->hasClass(
                    $classes,
                    '/carc\.?\s*1b/i'
                )
            ) {
                $this->applyThresholdResult(
                    $component,
                    $c,
                    ['carc 1b', 'carc. 1b'],
                    'H350',
                    0.1,
                    'Carc.',
                    '1B',
                    $results,
                    $issues
                );
            } elseif (
                $this->hasClass(
                    $classes,
                    '/carc\.?\s*2/i'
                )
            ) {
                $this->applyThresholdResult(
                    $component,
                    $c,
                    ['carc 2', 'carc. 2'],
                    'H351',
                    1.0,
                    'Carc.',
                    '2',
                    $results,
                    $issues
                );
            }

            if (
                $this->hasClass(
                    $classes,
                    '/repr\.?\s*1a/i'
                )
            ) {
                $this->applyThresholdResult(
                    $component,
                    $c,
                    ['repr 1a', 'repr. 1a'],
                    'H360',
                    0.3,
                    'Repr.',
                    '1A',
                    $results,
                    $issues
                );
            } elseif (
                $this->hasClass(
                    $classes,
                    '/repr\.?\s*1b/i'
                )
            ) {
                $this->applyThresholdResult(
                    $component,
                    $c,
                    ['repr 1b', 'repr. 1b'],
                    'H360',
                    0.3,
                    'Repr.',
                    '1B',
                    $results,
                    $issues
                );
            } elseif (
                $this->hasClass(
                    $classes,
                    '/repr\.?\s*2/i'
                )
            ) {
                $this->applyThresholdResult(
                    $component,
                    $c,
                    ['repr 2', 'repr. 2'],
                    'H361',
                    3.0,
                    'Repr.',
                    '2',
                    $results,
                    $issues
                );
            }

            if (
                $this->hasClass(
                    $classes,
                    '/lact/i'
                )
            ) {
                $this->applyThresholdResult(
                    $component,
                    $c,
                    ['lact'],
                    'H362',
                    0.3,
                    'Lact.',
                    'Additional category',
                    $results,
                    $issues
                );
            }
        }
    }

    private function evaluateStot(
        array $components,
        array &$results,
        array &$issues
    ): void {
        foreach ($components as $component) {
            $c = $component['effective_max'];

            if ($c === null) {
                continue;
            }

            $classes =
                $component['hazard_classifications'];

            $h =
                $component['hazard_statements'];

            if (
                $this->hasClass(
                    $classes,
                    '/stot\s*se\s*1/i'
                )
            ) {
                $cat1 = $this->thresholdInfo(
                    $component,
                    ['stot se 1'],
                    'H370',
                    10.0,
                    $issues
                );

                $cat2 = $this->thresholdInfo(
                    $component,
                    ['stot se 2'],
                    'H371',
                    1.0,
                    $issues,
                    false
                );

                if ($c >= $cat1['threshold']) {
                    $results[] = $this->thresholdResult(
                        'STOT SE',
                        '1',
                        'H370',
                        $c,
                        $cat1
                    );
                } elseif (
                    $c >= $cat2['threshold']
                ) {
                    $results[] = $this->thresholdResult(
                        'STOT SE',
                        '2',
                        'H371',
                        $c,
                        $cat2
                    );
                }
            } elseif (
                $this->hasClass(
                    $classes,
                    '/stot\s*se\s*2/i'
                )
            ) {
                $this->applyThresholdResult(
                    $component,
                    $c,
                    ['stot se 2'],
                    'H371',
                    10.0,
                    'STOT SE',
                    '2',
                    $results,
                    $issues
                );
            }

            if (
                $this->hasClass(
                    $classes,
                    '/stot\s*se\s*3/i'
                )
            ) {
                if (in_array('H335', $h, true)) {
                    $this->applyThresholdResult(
                        $component,
                        $c,
                        ['stot se 3'],
                        'H335',
                        20.0,
                        'STOT SE',
                        '3',
                        $results,
                        $issues
                    );
                }

                if (in_array('H336', $h, true)) {
                    $this->applyThresholdResult(
                        $component,
                        $c,
                        ['stot se 3'],
                        'H336',
                        20.0,
                        'STOT SE',
                        '3',
                        $results,
                        $issues
                    );
                }
            }

            if (
                $this->hasClass(
                    $classes,
                    '/stot\s*re\s*1/i'
                )
            ) {
                $cat1 = $this->thresholdInfo(
                    $component,
                    ['stot re 1'],
                    'H372',
                    10.0,
                    $issues
                );

                $cat2 = $this->thresholdInfo(
                    $component,
                    ['stot re 2'],
                    'H373',
                    1.0,
                    $issues,
                    false
                );

                if ($c >= $cat1['threshold']) {
                    $results[] = $this->thresholdResult(
                        'STOT RE',
                        '1',
                        'H372',
                        $c,
                        $cat1
                    );
                } elseif (
                    $c >= $cat2['threshold']
                ) {
                    $results[] = $this->thresholdResult(
                        'STOT RE',
                        '2',
                        'H373',
                        $c,
                        $cat2
                    );
                }
            } elseif (
                $this->hasClass(
                    $classes,
                    '/stot\s*re\s*2/i'
                )
            ) {
                $this->applyThresholdResult(
                    $component,
                    $c,
                    ['stot re 2'],
                    'H373',
                    10.0,
                    'STOT RE',
                    '2',
                    $results,
                    $issues
                );
            }
        }
    }

    private function evaluateAspiration(
        array $components,
        array &$results,
        array &$issues,
        array $physicalProperties,
        array &$ruleDecisions
    ): void {
        $aspirantTotal = 0.0;

        foreach ($components as $component) {
            if (
                $this->hasClass(
                    $component[
                        'hazard_classifications'
                    ],
                    '/asp\.?\s*tox\.?\s*1/i'
                )
            ) {
                $aspirantTotal +=
                    $component['effective_max']
                    ?? 0.0;
            }
        }

        if ($aspirantTotal < 10.0) {
            return;
        }

        $viscosity = $this->number(
            $physicalProperties[
                'kinematic_viscosity_40_cst'
            ] ?? null
        );

        if ($viscosity === null) {
            $issues[] = [
                'severity' => 'error',
                'code' =>
                    'ASPIRATION_VISCOSITY_REQUIRED',
                'message' =>
                    'Aspiration hazard değerlendirmesi için bitmiş ürünün 40 °C kinematik viskozitesi gerekir.',
                'context' => [
                    'aspiration_category_1_components_total' =>
                        round(
                            $aspirantTotal,
                            6
                        ),
                    'required_property' =>
                        'kinematic_viscosity_40_cst',
                    'classification_condition' =>
                        'Asp. Tox. 1 bileşenleri toplamı >= %10 ve bitmiş ürün viskozitesi <= 20.5 mm2/s',
                ],
            ];

            return;
        }

        if ($viscosity <= 20.5) {
            $results[] = [
                'hazard_class' =>
                    'Asp. Tox.',
                'category' =>
                    '1',
                'h_statement' =>
                    'H304',
                'method' =>
                    'mixture aspiration rule',
                'calculated_value' =>
                    round(
                        $viscosity,
                        6
                    ),
                'unit' =>
                    'mm2/s @ 40 C',
            ];

            $ruleDecisions[] = [
                'raw_material_name' =>
                    null,
                'chemical_name' =>
                    'Finished mixture',
                'cas_no' =>
                    null,
                'note' =>
                    'ASPIRATION',
                'status' =>
                    'condition_met',
                'action' =>
                    'classify_asp_tox_1',
                'message' =>
                    'Asp. Tox. 1 bileşenleri toplamı %10 veya üzerinde ve 40 °C kinematik viskozite 20.5 mm2/s veya altında; H304 uygulandı.',
                'evidence' => [
                    'aspirant_total_percent' =>
                        round(
                            $aspirantTotal,
                            6
                        ),
                    'kinematic_viscosity_40_cst' =>
                        $viscosity,
                ],
            ];

            return;
        }

        $ruleDecisions[] = [
            'raw_material_name' =>
                null,
            'chemical_name' =>
                'Finished mixture',
            'cas_no' =>
                null,
            'note' =>
                'ASPIRATION',
            'status' =>
                'condition_not_met',
            'action' =>
                'no_aspiration_classification',
            'message' =>
                'Asp. Tox. 1 bileşenleri toplamı %10 veya üzerinde ancak 40 °C kinematik viskozite 20.5 mm2/s üzerinde; karışım H304 olarak sınıflandırılmadı.',
            'evidence' => [
                'aspirant_total_percent' =>
                    round(
                        $aspirantTotal,
                        6
                    ),
                'kinematic_viscosity_40_cst' =>
                    $viscosity,
            ],
        ];
    }

    private function evaluateAquatic(
        array $components,
        array &$results
    ): void {
        $acute1Weighted = 0.0;
        $chronic1Weighted = 0.0;
        $chronic1Raw = 0.0;
        $chronic2 = 0.0;
        $chronic3 = 0.0;
        $chronic4 = 0.0;

        foreach ($components as $component) {
            $c =
                $component['effective_max']
                ?? 0.0;

            $classes =
                $component['hazard_classifications'];

            if (
                $this->hasClass(
                    $classes,
                    '/aquatic\s*acute\s*1/i'
                )
            ) {
                $acute1Weighted +=
                    $c *
                    max(
                        1.0,
                        (float)
                        $component[
                            'm_factor_acute'
                        ]
                    );
            }

            if (
                $this->hasClass(
                    $classes,
                    '/aquatic\s*chronic\s*1/i'
                )
            ) {
                $m = max(
                    1.0,
                    (float)
                    $component[
                        'm_factor_chronic'
                    ]
                );

                $chronic1Weighted +=
                    $c * $m;

                $chronic1Raw += $c;
            }

            if (
                $this->hasClass(
                    $classes,
                    '/aquatic\s*chronic\s*2/i'
                )
            ) {
                $chronic2 += $c;
            }

            if (
                $this->hasClass(
                    $classes,
                    '/aquatic\s*chronic\s*3/i'
                )
            ) {
                $chronic3 += $c;
            }

            if (
                $this->hasClass(
                    $classes,
                    '/aquatic\s*chronic\s*4/i'
                )
            ) {
                $chronic4 += $c;
            }
        }

        if ($acute1Weighted >= 25.0) {
            $results[] = [
                'hazard_class' =>
                    'Aquatic Acute',
                'category' => '1',
                'h_statement' => 'H400',
                'method' =>
                    'M-factor summation',
                'calculated_value' =>
                    round(
                        $acute1Weighted,
                        6
                    ),
            ];
        }

        if ($chronic1Weighted >= 25.0) {
            $results[] = [
                'hazard_class' =>
                    'Aquatic Chronic',
                'category' => '1',
                'h_statement' => 'H410',
                'method' =>
                    'M-factor summation',
                'calculated_value' =>
                    round(
                        $chronic1Weighted,
                        6
                    ),
            ];

            return;
        }

        $chronic2Score =
            10 * $chronic1Weighted +
            $chronic2;

        if ($chronic2Score >= 25.0) {
            $results[] = [
                'hazard_class' =>
                    'Aquatic Chronic',
                'category' => '2',
                'h_statement' => 'H411',
                'method' =>
                    'summation',
                'calculated_value' =>
                    round(
                        $chronic2Score,
                        6
                    ),
            ];

            return;
        }

        $chronic3Score =
            100 * $chronic1Weighted +
            10 * $chronic2 +
            $chronic3;

        if ($chronic3Score >= 25.0) {
            $results[] = [
                'hazard_class' =>
                    'Aquatic Chronic',
                'category' => '3',
                'h_statement' => 'H412',
                'method' =>
                    'summation',
                'calculated_value' =>
                    round(
                        $chronic3Score,
                        6
                    ),
            ];

            return;
        }

        $chronic4Score =
            $chronic1Raw +
            $chronic2 +
            $chronic3 +
            $chronic4;

        if ($chronic4Score >= 25.0) {
            $results[] = [
                'hazard_class' =>
                    'Aquatic Chronic',
                'category' => '4',
                'h_statement' => 'H413',
                'method' =>
                    'summation',
                'calculated_value' =>
                    round(
                        $chronic4Score,
                        6
                    ),
            ];
        }
    }

    private function applyDirectScl(
        array $component,
        array $needles,
        string $hCode,
        float $concentration,
        string $hazardClass,
        string $defaultCategory,
        array &$results,
        array &$issues
    ): bool {
        $info = $this->specificThreshold(
            $component,
            $needles,
            $hCode
        );

        if (!$info['relevant']) {
            return false;
        }

        if ($info['threshold'] === null) {
            $issues[] = [
                'severity' => 'error',
                'code' => 'SCL_UNPARSED',
                'message' =>
                    'İlgili SCL satırı bulundu ancak konsantrasyon eşiği güvenli şekilde okunamadı.',
                'context' => [
                    'chemical_name' =>
                        $component['chemical_name'],
                    'cas_no' =>
                        $component['cas_no'],
                    'h_code' => $hCode,
                    'raw_line' =>
                        $info['line'],
                ],
            ];

            return true;
        }

        if (
            $concentration >=
            $info['threshold']
        ) {
            $results[] = [
                'hazard_class' =>
                    $hazardClass,
                'category' =>
                    $this->categoryFromSclLine(
                        $info['line']
                    ) ?? $defaultCategory,
                'h_statement' => $hCode,
                'method' =>
                    'specific concentration limit',
                'calculated_value' =>
                    round(
                        $concentration,
                        6
                    ),
                'threshold' =>
                    $info['threshold'],
                'rule_details' =>
                    $info['line'],
            ];
        }

        return true;
    }

    private function applyThresholdResult(
        array $component,
        float $concentration,
        array $needles,
        string $hCode,
        float $genericLimit,
        string $hazardClass,
        string $category,
        array &$results,
        array &$issues
    ): void {
        $info = $this->thresholdInfo(
            $component,
            $needles,
            $hCode,
            $genericLimit,
            $issues
        );

        if (
            $concentration <
            $info['threshold']
        ) {
            return;
        }

        $results[] = $this->thresholdResult(
            $hazardClass,
            $category,
            $hCode,
            $concentration,
            $info
        );
    }

    private function thresholdInfo(
        array $component,
        array $needles,
        string $hCode,
        float $genericLimit,
        array &$issues,
        bool $reportUnparsed = true
    ): array {
        $specific = $this->specificThreshold(
            $component,
            $needles,
            $hCode
        );

        if (
            $specific['relevant'] &&
            $specific['threshold'] === null
        ) {
            if ($reportUnparsed) {
                $issues[] = [
                    'severity' => 'error',
                    'code' => 'SCL_UNPARSED',
                    'message' =>
                        'İlgili SCL satırı bulundu ancak konsantrasyon eşiği güvenli şekilde okunamadı. Generic limit otomatik kullanılmadı.',
                    'context' => [
                        'chemical_name' =>
                            $component['chemical_name'],
                        'cas_no' =>
                            $component['cas_no'],
                        'h_code' => $hCode,
                        'raw_line' =>
                            $specific['line'],
                    ],
                ];
            }

            return [
                'threshold' => INF,
                'method' =>
                    'specific concentration limit - review required',
                'line' =>
                    $specific['line'],
            ];
        }

        if (
            $specific['threshold'] !== null
        ) {
            return [
                'threshold' =>
                    $specific['threshold'],
                'method' =>
                    'specific concentration limit',
                'line' =>
                    $specific['line'],
            ];
        }

        return [
            'threshold' => $genericLimit,
            'method' =>
                'generic concentration limit',
            'line' => null,
        ];
    }

    private function thresholdResult(
        string $hazardClass,
        string $category,
        string $hCode,
        float $concentration,
        array $info
    ): array {
        return [
            'hazard_class' => $hazardClass,
            'category' => $category,
            'h_statement' => $hCode,
            'method' =>
                $info['method'],
            'calculated_value' =>
                round(
                    $concentration,
                    6
                ),
            'threshold' =>
                is_finite(
                    $info['threshold']
                )
                    ? $info['threshold']
                    : null,
            'rule_details' =>
                $info['line'],
        ];
    }

    private function specificThreshold(
        array $component,
        array $needles,
        string $hCode
    ): array {
        $lines =
            $component[
                'specific_concentration_limits'
            ] ?? [];

        foreach ($lines as $line) {
            $line = trim(
                (string) $line
            );

            if ($line === '') {
                continue;
            }

            $haystack =
                mb_strtolower($line);

            $relevant =
                stripos(
                    $line,
                    $hCode
                ) !== false;

            if (!$relevant) {
                foreach ($needles as $needle) {
                    if (
                        str_contains(
                            $haystack,
                            mb_strtolower($needle)
                        )
                    ) {
                        $relevant = true;
                        break;
                    }
                }
            }

            if (!$relevant) {
                continue;
            }

            $normalized = str_replace(
                ['≥', '≤', '=>', '=<'],
                ['>=', '<=', '>=', '<='],
                $line
            );

            if (
                preg_match(
                    '/\bC\s*>=\s*([0-9]+(?:[.,][0-9]+)?)\s*%/iu',
                    $normalized,
                    $match
                )
            ) {
                return [
                    'relevant' => true,
                    'threshold' =>
                        $this->number($match[1]),
                    'line' => $line,
                ];
            }

            if (
                preg_match(
                    '/([0-9]+(?:[.,][0-9]+)?)\s*%\s*<=\s*C\b/iu',
                    $normalized,
                    $match
                )
            ) {
                return [
                    'relevant' => true,
                    'threshold' =>
                        $this->number($match[1]),
                    'line' => $line,
                ];
            }

            if (
                preg_match(
                    '/(?:^|[:;])\s*([0-9]+(?:[.,][0-9]+)?)\s*%\s*(?:<=|<)\s*C/iu',
                    $normalized,
                    $match
                )
            ) {
                return [
                    'relevant' => true,
                    'threshold' =>
                        $this->number($match[1]),
                    'line' => $line,
                ];
            }

            return [
                'relevant' => true,
                'threshold' => null,
                'line' => $line,
            ];
        }

        return [
            'relevant' => false,
            'threshold' => null,
            'line' => null,
        ];
    }

    private function categoryFromSclLine(
        ?string $line
    ): ?string {
        if (!$line) {
            return null;
        }

        if (
            preg_match(
                '/\b(?:Skin\s*Corr|Eye\s*Dam|Skin\s*Irrit|Eye\s*Irrit)\.?\s*([12][ABC]?)/i',
                $line,
                $match
            )
        ) {
            return strtoupper(
                $match[1]
            );
        }

        return null;
    }

    private function componentAteForRoute(
        array $component,
        string $route
    ): ?float {
        foreach (
            $component['ate_data']
            as $ate
        ) {
            if (!is_array($ate)) {
                continue;
            }

            $ateRoute = strtolower(
                trim(
                    (string) (
                        $ate['route'] ?? ''
                    )
                )
            );

            if ($ateRoute !== $route) {
                continue;
            }

            $value = $this->number(
                $ate['value'] ?? null
            );

            if (
                $value !== null &&
                $value > 0
            ) {
                return $value;
            }
        }

        return null;
    }

    private function acuteCategoryFromClasses(
        array $classes,
        string $route
    ): ?int {
        foreach ($classes as $class) {
            $normalized =
                strtolower($class);

            if (
                !str_contains(
                    $normalized,
                    'acute tox'
                )
            ) {
                continue;
            }

            if (
                str_contains(
                    $normalized,
                    'oral'
                ) &&
                $route !== 'oral'
            ) {
                continue;
            }

            if (
                str_contains(
                    $normalized,
                    'dermal'
                ) &&
                $route !== 'dermal'
            ) {
                continue;
            }

            if (
                preg_match(
                    '/acute\s*tox\.?\s*([1-4])/i',
                    $class,
                    $match
                )
            ) {
                return (int)
                    $match[1];
            }
        }

        return null;
    }

    private function acutePointEstimate(
        string $route,
        int $category
    ): ?float {
        $map = [
            'oral' => [
                1 => 0.5,
                2 => 5.0,
                3 => 100.0,
                4 => 500.0,
            ],
            'dermal' => [
                1 => 5.0,
                2 => 50.0,
                3 => 300.0,
                4 => 1100.0,
            ],
        ];

        return $map[$route][$category]
            ?? null;
    }

    private function acuteMixtureCategory(
        string $route,
        float $ate
    ): ?int {
        if ($route === 'oral') {
            if ($ate <= 5) {
                return 1;
            }

            if ($ate <= 50) {
                return 2;
            }

            if ($ate <= 300) {
                return 3;
            }

            if ($ate <= 2000) {
                return 4;
            }

            return null;
        }

        if ($route === 'dermal') {
            if ($ate <= 50) {
                return 1;
            }

            if ($ate <= 200) {
                return 2;
            }

            if ($ate <= 1000) {
                return 3;
            }

            if ($ate <= 2000) {
                return 4;
            }

            return null;
        }

        return null;
    }

    private function acuteHCode(
        string $route,
        int $category
    ): string {
        if ($route === 'oral') {
            return match ($category) {
                1, 2 => 'H300',
                3 => 'H301',
                4 => 'H302',
                default => '',
            };
        }

        return match ($category) {
            1, 2 => 'H310',
            3 => 'H311',
            4 => 'H312',
            default => '',
        };
    }

    private function derivePictograms(
        array $results
    ): array {
        $pictograms = [];

        foreach ($results as $row) {
            $class =
                $row['hazard_class'] ?? '';

            $category =
                (string) (
                    $row['category'] ?? ''
                );

            if (
                $class === 'Acute Tox.' &&
                in_array(
                    $category,
                    ['1', '2', '3'],
                    true
                )
            ) {
                $pictograms[] = 'GHS06';
            }

            if (
                $class === 'Acute Tox.' &&
                $category === '4'
            ) {
                $pictograms[] = 'GHS07';
            }

            if (
                in_array(
                    $class,
                    [
                        'Skin Corr.',
                        'Eye Dam.',
                    ],
                    true
                )
            ) {
                $pictograms[] = 'GHS05';
            }

            if (
                in_array(
                    $class,
                    [
                        'Skin Irrit.',
                        'Eye Irrit.',
                        'Skin Sens.',
                    ],
                    true
                )
            ) {
                $pictograms[] = 'GHS07';
            }

            if (
                in_array(
                    $class,
                    [
                        'Resp. Sens.',
                        'Muta.',
                        'Carc.',
                        'Repr.',
                        'STOT SE',
                        'STOT RE',
                        'Asp. Tox.',
                    ],
                    true
                )
            ) {
                $pictograms[] = 'GHS08';
            }

            if (
                in_array(
                    $class,
                    [
                        'Aquatic Acute',
                        'Aquatic Chronic',
                    ],
                    true
                ) &&
                in_array(
                    $category,
                    ['1', '2'],
                    true
                )
            ) {
                $pictograms[] = 'GHS09';
            }
        }

        return array_values(
            array_unique($pictograms)
        );
    }

    private function deriveSignalWord(
        array $results
    ): ?string {
        $danger = false;
        $warning = false;

        foreach ($results as $row) {
            $class =
                $row['hazard_class'] ?? '';

            $category =
                (string) (
                    $row['category'] ?? ''
                );

            if (
                (
                    $class === 'Acute Tox.' &&
                    in_array(
                        $category,
                        ['1', '2', '3'],
                        true
                    )
                ) ||
                in_array(
                    $class,
                    [
                        'Skin Corr.',
                        'Eye Dam.',
                        'Resp. Sens.',
                    ],
                    true
                ) ||
                (
                    in_array(
                        $class,
                        [
                            'Muta.',
                            'Carc.',
                            'Repr.',
                        ],
                        true
                    ) &&
                    str_starts_with(
                        $category,
                        '1'
                    )
                ) ||
                (
                    in_array(
                        $class,
                        [
                            'STOT SE',
                            'STOT RE',
                        ],
                        true
                    ) &&
                    $category === '1'
                ) ||
                $class === 'Asp. Tox.'
            ) {
                $danger = true;
            } else {
                $warning = true;
            }
        }

        if ($danger) {
            return 'Danger';
        }

        if ($warning) {
            return 'Warning';
        }

        return null;
    }

    private function flattenScl(
        mixed $value
    ): array {
        if (!is_array($value)) {
            return [];
        }

        if (
            isset($value['raw_lines']) &&
            is_array($value['raw_lines'])
        ) {
            return $value['raw_lines'];
        }

        return collect($value)
            ->flatten()
            ->filter(
                fn ($v) =>
                    is_scalar($v) &&
                    trim((string) $v) !== ''
            )
            ->map(
                fn ($v) =>
                    trim((string) $v)
            )
            ->values()
            ->all();
    }

    private function normalizeStrings(
        mixed $value
    ): array {
        if (
            $value === null ||
            $value === ''
        ) {
            return [];
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        return collect($value)
            ->flatten()
            ->map(
                fn ($v) =>
                    trim((string) $v)
            )
            ->filter(
                fn ($v) =>
                    $v !== ''
            )
            ->unique()
            ->values()
            ->all();
    }

    private function hasClass(
        array $classes,
        string $pattern
    ): bool {
        foreach ($classes as $class) {
            if (
                preg_match(
                    $pattern,
                    $class
                )
            ) {
                return true;
            }
        }

        return false;
    }

    private function number(
        mixed $value
    ): ?float {
        if (
            $value === null ||
            $value === ''
        ) {
            return null;
        }

        if (is_string($value)) {
            $value = str_replace(
                ',',
                '.',
                trim($value)
            );
        }

        return is_numeric($value)
            ? (float) $value
            : null;
    }
}
