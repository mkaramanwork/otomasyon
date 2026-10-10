<?php

namespace App\Http\Controllers;

use App\Models\RegulatorySubstance;
use App\Models\RegulatoryClassification;
use App\Models\RawMaterialSdsComponent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RegulatoryController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('q'));

        $substances = RegulatorySubstance::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('preferred_name', 'like', '%' . $search . '%')
                        ->orWhere('cas_no', 'like', '%' . $search . '%')
                        ->orWhere('ec_no', 'like', '%' . $search . '%')
                        ->orWhere('index_no', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('preferred_name')
            ->orderBy('cas_no')
            ->paginate(25)
            ->withQueryString();

        return view('sds.regulatory.index', compact(
            'substances',
            'search'
        ));
    }

    public function create()
    {
        return view('sds.regulatory.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'preferred_name' => 'required|string|max:255',
            'cas_no' => [
                'required',
                'string',
                'max:50',
                'unique:regulatory_substances,cas_no',
            ],
            'ec_no' => 'nullable|string|max:50',
            'index_no' => 'nullable|string|max:50',
            'molecular_formula' => 'nullable|string|max:255',
            'substance_type' => 'nullable|string|max:255',
            'echa_uuid' => 'nullable|string|max:255',
            'echa_substance_url' => 'nullable|string|max:2000',
        ]);

        $substance = DB::transaction(function () use ($validated) {
            $substance = RegulatorySubstance::create([
                'preferred_name' => $validated['preferred_name'],
                'cas_no' => $validated['cas_no'],
                'ec_no' => $validated['ec_no'] ?? null,
                'index_no' => $validated['index_no'] ?? null,
                'molecular_formula' => $validated['molecular_formula'] ?? null,
                'substance_type' => $validated['substance_type'] ?? null,
                'echa_uuid' => $validated['echa_uuid'] ?? null,
                'echa_substance_url' => $validated['echa_substance_url'] ?? null,
                'created_from' => 'MANUAL',
                'verification_status' => 'review_required',
                'last_echa_check_at' => null,
                'is_active' => true,
            ]);

            RawMaterialSdsComponent::whereNull('regulatory_substance_id')
                ->where('cas_no', $substance->cas_no)
                ->update([
                    'regulatory_substance_id' => $substance->id,
                    'is_verified' => false,
                ]);

            return $substance;
        });

        $matchedCount = RawMaterialSdsComponent::where(
            'regulatory_substance_id',
            $substance->id
        )->count();

        return redirect()
            ->route('sds.regulatory.show', $substance->id)
            ->with(
                'success',
                'Kimyasal madde kaydedildi. ' .
                $matchedCount .
                ' SDS bileşeni CAS numarası ile eşleştirildi.'
            );
    }

    public function show(RegulatorySubstance $substance)
    {
        $classifications = RegulatoryClassification::where(
            'regulatory_substance_id',
            $substance->id
        )
            ->orderByDesc('is_current')
            ->orderByDesc('effective_from')
            ->orderByDesc('source_date')
            ->orderByDesc('id')
            ->get();

        $matchedComponents = RawMaterialSdsComponent::where(
            'regulatory_substance_id',
            $substance->id
        )
            ->with('sdsProfile.rawMaterial')
            ->get();

        return view('sds.regulatory.show', compact(
            'substance',
            'classifications',
            'matchedComponents'
        ));
    }

    public function storeClassification(
        Request $request,
        RegulatorySubstance $substance
    ) {
        $validated = $request->validate([
            'regulation_profile' => 'required|string|max:100',
            'jurisdiction' => 'nullable|string|max:50',
            'source_type' => 'nullable|string|max:100',
            'source_name' => 'required|string|max:255',
            'source_version' => 'nullable|string|max:255',
            'source_date' => 'nullable|date',
            'source_reference' => 'nullable|string',
            'source_url' => 'nullable|string|max:2000',
            'legal_status' => 'nullable|string|max:50',
            'hazard_classifications_text' => 'nullable|string',
            'signal_word' => 'nullable|string|max:50',
            'pictograms_text' => 'nullable|string',
            'hazard_statements_text' => 'nullable|string',
            'supplemental_hazard_statements_text' => 'nullable|string',
            'm_factor_acute' => 'nullable|numeric|min:0',
            'm_factor_chronic' => 'nullable|numeric|min:0',
            'reach_registered' => 'nullable|boolean',
            'svhc' => 'nullable|boolean',
            'annex_vi' => 'nullable|boolean',
            'annex_xiv' => 'nullable|boolean',
            'annex_xvii' => 'nullable|boolean',
            'annex_vi_entry' => 'nullable|string|max:255',
            'annex_xiv_entry' => 'nullable|string|max:255',
            'annex_xvii_entry' => 'nullable|string|max:255',
            'effective_from' => 'nullable|date',
            'effective_until' => 'nullable|date',
        ]);

        $isFuture = false;

        if (!empty($validated['effective_from'])) {
            $isFuture = Carbon::parse($validated['effective_from'])
                ->startOfDay()
                ->gt(now()->startOfDay());
        }

        DB::transaction(function () use (
            $request,
            $validated,
            $substance,
            $isFuture
        ) {
            if (!$isFuture) {
                RegulatoryClassification::where(
                    'regulatory_substance_id',
                    $substance->id
                )
                    ->where(
                        'regulation_profile',
                        $validated['regulation_profile']
                    )
                    ->where('is_current', true)
                    ->update([
                        'is_current' => false,
                    ]);
            }

            RegulatoryClassification::create([
                'regulatory_substance_id' => $substance->id,
                'regulation_profile' => $validated['regulation_profile'],
                'jurisdiction' => $validated['jurisdiction'] ?? null,
                'source_type' => $validated['source_type'] ?? 'manual',
                'source_name' => $validated['source_name'],
                'source_version' => $validated['source_version'] ?? null,
                'source_date' => $validated['source_date'] ?? null,
                'source_reference' => $validated['source_reference'] ?? null,
                'source_url' => $validated['source_url'] ?? null,
                'legal_status' => $isFuture
                    ? 'future'
                    : ($validated['legal_status'] ?? 'in_force'),
                'hazard_classifications' => $this->stringToArray(
                    $validated['hazard_classifications_text'] ?? null
                ),
                'signal_word' => $validated['signal_word'] ?? null,
                'pictograms' => $this->stringToArray(
                    $validated['pictograms_text'] ?? null
                ),
                'hazard_statements' => $this->stringToArray(
                    $validated['hazard_statements_text'] ?? null
                ),
                'supplemental_hazard_statements' => $this->stringToArray(
                    $validated['supplemental_hazard_statements_text'] ?? null
                ),
                'm_factor_acute' => $validated['m_factor_acute'] ?? null,
                'm_factor_chronic' => $validated['m_factor_chronic'] ?? null,
                'reach_registered' => $request->boolean('reach_registered'),
                'svhc' => $request->boolean('svhc'),
                'annex_vi' => $request->boolean('annex_vi'),
                'annex_xiv' => $request->boolean('annex_xiv'),
                'annex_xvii' => $request->boolean('annex_xvii'),
                'annex_vi_entry' => $validated['annex_vi_entry'] ?? null,
                'annex_xiv_entry' => $validated['annex_xiv_entry'] ?? null,
                'annex_xvii_entry' => $validated['annex_xvii_entry'] ?? null,
                'effective_from' => $validated['effective_from'] ?? null,
                'effective_until' => $validated['effective_until'] ?? null,
                'checked_at' => now(),
                'is_current' => !$isFuture,
                'is_verified' => false,
            ]);

            $substance->update([
                'verification_status' => 'review_required',
            ]);
        });

        return redirect()
            ->route('sds.regulatory.show', $substance->id)
            ->with(
                'success',
                'Mevzuat sınıflandırması kaydedildi.'
            );
    }

    public function importIndex()
    {
        return view('sds.regulatory.import');
    }

public function importPreview(Request $request)
{
    $validated = $request->validate([
        'regulation_profile' => 'required|string|max:100',
        'source_name' => 'required|string|max:255',
        'source_version' => 'nullable|string|max:255',
        'source_date' => 'nullable|date',
        'import_file' => [
            'required',
            'file',
            'max:30720',
        ],
    ]);

    $file = $request->file('import_file');

    $extension = strtolower(
        $file->getClientOriginalExtension()
    );

    if (!in_array(
        $extension,
        ['csv', 'txt', 'xlsx', 'xls'],
        true
    )) {
        return back()
            ->withInput()
            ->withErrors([
                'import_file' =>
                    'Sadece CSV, TXT, XLSX veya XLS dosyaları yüklenebilir.',
            ]);
    }

    $storedPath = $file->store(
        'regulatory-imports',
        'local'
    );

    $absolutePath = Storage::disk('local')->path(
        $storedPath
    );

    $fileHash = hash_file(
        'sha256',
        $absolutePath
    );

    try {
        $parsed = $this->parseRegulatoryFile(
            $absolutePath,
            $file->getClientOriginalName(),
            [
                'regulation_profile' =>
                    $validated['regulation_profile'],

                'source_name' =>
                    $validated['source_name'],

                'source_version' =>
                    $validated['source_version']
                    ?? null,

                'source_date' =>
                    $validated['source_date']
                    ?? null,

                'file_hash' =>
                    $fileHash,

                'preview_only' =>
                    true,
            ]
        );
    } catch (\Throwable $e) {
        Storage::disk('local')->delete(
            $storedPath
        );

        return back()
            ->withInput()
            ->withErrors([
                'import_file' => $e->getMessage(),
            ]);
    }

    $effectiveProfile =
        $parsed['meta']['regulation_profile']
        ?? $validated['regulation_profile'];

    $effectiveSourceName =
        $parsed['meta']['source_name']
        ?? $validated['source_name'];

    $effectiveSourceVersion =
        $parsed['meta']['dataset_version']
        ?? (
            $validated['source_version']
            ?? null
        );

    $effectiveSourceDate =
        $parsed['meta']['source_date']
        ?? (
            $validated['source_date']
            ?? null
        );

    $token = bin2hex(
        random_bytes(20)
    );

    session()->put(
        'regulatory_imports.' . $token,
        [
            'path' =>
                $storedPath,

            'regulation_profile' =>
                $effectiveProfile,

            'source_name' =>
                $effectiveSourceName,

            'source_version' =>
                $effectiveSourceVersion,

            'source_date' =>
                $effectiveSourceDate,

            'file_name' =>
                $file->getClientOriginalName(),

            'file_hash' =>
                $fileHash,

            'parser_type' =>
                $parsed['meta']['parser_type']
                ?? 'generic',
        ]
    );

    return view(
        'sds.regulatory.import-preview',
        [
            'previewRows' =>
                $parsed['rows'],

            'summary' =>
                $parsed['summary'],

            'regulationProfile' =>
                $effectiveProfile,

            'sourceName' =>
                $effectiveSourceName,

            'sourceVersion' =>
                $effectiveSourceVersion,

            'sourceDate' =>
                $effectiveSourceDate,

            'fileName' =>
                $file->getClientOriginalName(),

            'importToken' =>
                $token,
        ]
    );
}   
    public function importStore(Request $request)
    {
        $validated = $request->validate([
            'import_token' => 'required|string|max:100',
        ]);

        $sessionKey = 'regulatory_imports.' . $validated['import_token'];
        $importData = session($sessionKey);

        if (!$importData) {
            return redirect()
                ->route('sds.regulatory.import')
                ->withErrors([
                    'import_file' =>
                        'Import önizleme oturumu bulunamadı veya süresi doldu.',
                ]);
        }

        if (
            empty($importData['path']) ||
            !Storage::disk('local')->exists($importData['path'])
        ) {
            session()->forget($sessionKey);

            return redirect()
                ->route('sds.regulatory.import')
                ->withErrors([
                    'import_file' => 'Geçici import dosyası bulunamadı.',
                ]);
        }

        $absolutePath = Storage::disk('local')->path($importData['path']);
        $currentHash = hash_file('sha256', $absolutePath);

        if (
            !empty($importData['file_hash']) &&
            $currentHash !== $importData['file_hash']
        ) {
            return redirect()
                ->route('sds.regulatory.import')
                ->withErrors([
                    'import_file' =>
                        'Önizlenen dosya ile aktarılacak dosya aynı değil. İşlem iptal edildi.',
                ]);
        }

        try {
            $parsed = $this->parseRegulatoryFile(
                $absolutePath,
                $importData['file_name'],
                $importData
            );
        } catch (\Throwable $e) {
            return redirect()
                ->route('sds.regulatory.import')
                ->withErrors([
                    'import_file' => $e->getMessage(),
                ]);
        }

        // Invalid rows are reported and skipped; valid rows may still be imported.
        // An entirely invalid file cannot be imported.
        $validCount = collect($parsed['rows'])->where('status', '!=', 'invalid')->count();
        if ($validCount === 0) {
            return redirect()->route('sds.regulatory.import')->withErrors([
                'import_file' => 'Aktarılabilecek geçerli bir satır bulunamadı.'
            ]);
        }

        $counters = [
            'new' => 0,
            'updated' => 0,
            'same' => 0,
            'linked' => 0,
            'classifications' => 0,
            'future' => 0,
        ];

        DB::transaction(function () use (
            $parsed,
            $importData,
            &$counters
        ) {
            foreach ($parsed['rows'] as $row) {
                if ($row['status'] === 'invalid') {
                    continue;
                }

                $substance = $this->findSubstanceForRow($row);

                if (!$substance) {
                    $substance = $this->createSubstanceFromRow($row);
                    $counters['new']++;
                } else {
                    $changed = $this->updateSubstanceFromRow(
                        $substance,
                        $row
                    );

                    if ($changed) {
                        $counters['updated']++;
                    }
                }

                $sourceType = $row['source_type'] ?? null;

                if ($sourceType === 'ECHA_ANNEX_VI') {
                    $counters['linked'] +=
                        $this->linkEchaSdsComponents(
                            $substance,
                            $row,
                            $parsed['identifier_counts'] ?? []
                        );
                } elseif ($sourceType !== 'ECHA_REACH_REGISTERED') {
                    $counters['linked'] +=
                        $this->linkGenericSdsComponents(
                            $substance,
                            $row
                        );
                }

                $existingExact = $this->findExactClassification(
                    $substance,
                    $row,
                    $importData['regulation_profile'],
                    $importData['source_name']
                );

                if (
                    $existingExact &&
                    $this->classificationEquivalent(
                        $existingExact,
                        $row
                    )
                ) {
                    $counters['same']++;
                    continue;
                }

                $isFuture = !empty($row['effective_from'])
                    && Carbon::parse($row['effective_from'])
                        ->startOfDay()
                        ->gt(now()->startOfDay());

                $isCurrent = array_key_exists('is_current', $row)
                    ? (bool) $row['is_current']
                    : !$isFuture;

                if ($isCurrent) {
                    $query = RegulatoryClassification::where(
                        'regulatory_substance_id',
                        $substance->id
                    )
                        ->where(
                            'regulation_profile',
                            $importData['regulation_profile']
                        )
                        ->where('is_current', true);

                    $sourceType = $row['source_type'] ?? null;

                    if ($sourceType === 'ECHA_SVHC') {
                        $query->where('source_type', 'ECHA_SVHC');

                        if (!empty($row['source_reference'])) {
                            $query->where(
                                'source_reference',
                                $row['source_reference']
                            );
                        }
                    } elseif ($sourceType === 'ECHA_ANNEX_XIV') {
                        $query->where(
                            'source_type',
                            'ECHA_ANNEX_XIV'
                        );

                        if (!empty($row['annex_xiv_entry'])) {
                            $query->where(
                                'annex_xiv_entry',
                                $row['annex_xiv_entry']
                            );
                        }
                    } elseif ($sourceType === 'ECHA_ANNEX_XVII') {
                        $query->where(
                            'source_type',
                            'ECHA_ANNEX_XVII'
                        );

                        if (!empty($row['annex_xvii_entry'])) {
                            $query->where(
                                'annex_xvii_entry',
                                $row['annex_xvii_entry']
                            );
                        }
                    } elseif (
                        $sourceType &&
                        $sourceType !== 'regulatory_import'
                    ) {
                        $query->where('source_type', $sourceType);
                    } else {
                        $query->where(
                            'source_name',
                            $importData['source_name']
                        );
                    }

                    $query->update([
                        'is_current' => false,
                    ]);
                }

                if ($existingExact && $existingExact->is_current) {
                    $existingExact->update([
                        'is_current' => false,
                    ]);
                }

                RegulatoryClassification::create([
                    'regulatory_substance_id' => $substance->id,
                    'regulation_profile' => $importData['regulation_profile'],
                    'jurisdiction' => $importData['regulation_profile']
                        === 'TR_KKDIK_SEA'
                            ? 'TR'
                            : 'EU',
                    'source_type' => $row['source_type']
                        ?? 'regulatory_import',
                    'source_name' => $importData['source_name'],
                    'source_version' => $row['source_version']
                        ?? ($importData['source_version'] ?? null),
                    'source_date' => $importData['source_date'] ?? null,
                    'source_reference' => $row['source_reference'] ?? null,
                    'source_url' => $row['source_url'] ?? null,
                    'legal_status' => $row['legal_status']
                        ?? ($isFuture ? 'future' : 'in_force'),
                    'hazard_classifications' =>
                        $row['hazard_classifications'] ?? null,
                    'signal_word' => $row['signal_word'] ?? null,
                    'pictograms' => $row['pictograms'] ?? null,
                    'hazard_statements' => $row['hazard_statements'] ?? null,
                    'supplemental_hazard_statements' =>
                        $row['supplemental_hazard_statements'] ?? null,
                    'specific_concentration_limits' =>
                        $row['specific_concentration_limits'] ?? null,
                    'ate_data' => $row['ate_data'] ?? null,
                    'm_factor_acute' => $row['m_factor_acute'] ?? null,
                    'm_factor_chronic' => $row['m_factor_chronic'] ?? null,
                    'reach_registered' => $row['reach_registered'] ?? null,
                    'svhc' => $row['svhc'] ?? null,
                    'annex_vi' => $row['annex_vi'] ?? null,
                    'annex_xiv' => $row['annex_xiv'] ?? null,
                    'annex_xvii' => $row['annex_xvii'] ?? null,
                    'annex_vi_entry' => $row['annex_vi_entry'] ?? null,
                    'annex_xiv_entry' => $row['annex_xiv_entry'] ?? null,
                    'annex_xvii_entry' => $row['annex_xvii_entry'] ?? null,
                    'regulatory_notes' => array_merge(
                        $row['regulatory_notes'] ?? [],
                        [
                            'source_file_hash' =>
                                $importData['file_hash'] ?? null,
                        ]
                    ),
                    'effective_from' => $row['effective_from'] ?? null,
                    'effective_until' => $row['effective_until'] ?? null,
                    'checked_at' => now(),
                    'is_current' => $isCurrent,
                    'is_verified' => false,
                ]);

                $counters['classifications']++;

                if ($isFuture) {
                    $counters['future']++;
                }
            }
        });

        Storage::disk('local')->delete($importData['path']);
        session()->forget($sessionKey);

        return redirect()
            ->route('sds.regulatory.index')
            ->with(
                'success',
                'Mevzuat aktarımı tamamlandı. ' .
                'Yeni madde: ' . $counters['new'] .
                ' | Güncellenen madde: ' . $counters['updated'] .
                ' | Aynı sınıflandırma: ' . $counters['same'] .
                ' | Yeni sınıflandırma: ' . $counters['classifications'] .
                ' | Gelecek tarihli: ' . $counters['future'] .
                ' | SDS eşleşmesi: ' . $counters['linked'] .
                ' | Atlanan hatalı satır: ' . ($parsed['summary']['invalid'] ?? 0)
            );
    }

    private function parseRegulatoryFile(
        string $absolutePath,
        string $originalFileName,
        array $context
    ): array {
        $extension = strtolower(
            pathinfo($originalFileName, PATHINFO_EXTENSION)
        );

        if (in_array($extension, ['xlsx', 'xls'], true)) {
            $spreadsheet = IOFactory::load($absolutePath);

            try {
                if (
                    $spreadsheet->sheetNameExists('ATP23') &&
                    $spreadsheet->sheetNameExists('History')
                ) {
                    return $this->parseEchaAnnexVi(
                        $spreadsheet,
                        $context
                    );
                }

                if ($this->isEchaReachRegisteredSpreadsheet($spreadsheet)) {
                    return $this->parseEchaReachRegistered(
                        $spreadsheet,
                        $context
                    );
                }

                return $this->parseGenericSpreadsheet(
                    $spreadsheet,
                    $context
                );
            } finally {
                $spreadsheet->disconnectWorksheets();
            }
        }

        if (in_array($extension, ['csv', 'txt'], true)) {
            if ($this->isEchaSvhcCandidateListFile($absolutePath)) {
                return $this->parseEchaSvhcCandidateList(
                    $absolutePath,
                    $context
                );
            }

            if ($this->isEchaAnnexXivAuthorisationListFile($absolutePath)) {
                return $this->parseEchaAnnexXivAuthorisationList(
                    $absolutePath,
                    $context
                );
            }

            if ($this->isEchaAnnexXviiRestrictionListFile($absolutePath)) {
                return $this->parseEchaAnnexXviiRestrictionList(
                    $absolutePath,
                    $context
                );
            }
        }

        return $this->parseGenericDelimitedFile(
            $absolutePath,
            $context
        );
    }

    private function isEchaReachRegisteredSpreadsheet($spreadsheet): bool
    {
        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            if ($worksheet->getHighestRow() < 2) {
                continue;
            }

            $headers = [];

            for ($column = 1; $column <= 10; $column++) {
                $value = trim((string) $worksheet->getCell([
                    $column,
                    1,
                ])->getValue());

                if ($value !== '') {
                    $headers[] = $value;
                }
            }

            $required = [
                'Name',
                'EC Number',
                'CAS Number',
                "Link to substance's overview page",
            ];

            if (count(array_diff($required, $headers)) === 0) {
                return true;
            }
        }

        return false;
    }

    private function parseEchaReachRegistered(
        $spreadsheet,
        array $context
    ): array {
        $worksheet = null;
        $headerMap = [];

        foreach ($spreadsheet->getWorksheetIterator() as $candidate) {
            $headers = [];

            for ($column = 1; $column <= 10; $column++) {
                $value = trim((string) $candidate->getCell([
                    $column,
                    1,
                ])->getValue());

                if ($value !== '') {
                    $headers[$value] = $column;
                }
            }

            $required = [
                'Name',
                'EC Number',
                'CAS Number',
                "Link to substance's overview page",
            ];

            if (count(array_diff($required, array_keys($headers))) === 0) {
                $worksheet = $candidate;
                $headerMap = $headers;
                break;
            }
        }

        if (!$worksheet) {
            throw new \RuntimeException(
                'ECHA REACH Registered Substances kolon yapısı bulunamadı.'
            );
        }

        $datasetVersion = $context['source_version']
            ?? ($context['source_date'] ?? null);

        $sourceDate = $context['source_date'] ?? null;
        $previewOnly = (bool) ($context['preview_only'] ?? false);
        $previewLimit = 500;

        $records = [];
        $summary = [
            'new' => 0,
            'changed' => 0,
            'same' => 0,
            'invalid' => 0,
            'total' => 0,
            'reach_breakdown' => [
                'source_record_missing' => 0,
                'name_changed' => 0,
                'ec_changed' => 0,
                'cas_conflict' => 0,
                'echa_identity_changed' => 0,
                'real_data_change' => 0,
            ],
        ];

        for (
            $rowNumber = 2;
            $rowNumber <= $worksheet->getHighestRow();
            $rowNumber++
        ) {
            $preferredName = trim((string) $worksheet->getCell([
                $headerMap['Name'],
                $rowNumber,
            ])->getValue());

            $ecNo = $this->cleanIdentifier(
                $worksheet->getCell([
                    $headerMap['EC Number'],
                    $rowNumber,
                ])->getValue()
            );

            $casNo = $this->cleanIdentifier(
                $worksheet->getCell([
                    $headerMap['CAS Number'],
                    $rowNumber,
                ])->getValue()
            );

            $sourceUrl = trim((string) $worksheet->getCell([
                $headerMap["Link to substance's overview page"],
                $rowNumber,
            ])->getValue());

            if (
                $preferredName === '' &&
                !$ecNo &&
                !$casNo &&
                $sourceUrl === ''
            ) {
                continue;
            }

            $echaUuid = $this->extractEchaChemUuid($sourceUrl);

            $row = [
                'row_number' => $rowNumber,
                'preferred_name' => $preferredName,
                'cas_no' => $casNo,
                'ec_no' => $ecNo,
                'index_no' => null,
                'echa_uuid' => $echaUuid,
                'all_cas_numbers' => array_values(array_filter([
                    $casNo,
                ])),
                'all_ec_numbers' => array_values(array_filter([
                    $ecNo,
                ])),
                'hazard_classifications' => null,
                'signal_word' => null,
                'pictograms' => null,
                'hazard_statements' => null,
                'supplemental_hazard_statements' => null,
                'specific_concentration_limits' => null,
                'ate_data' => null,
                'm_factor_acute' => null,
                'm_factor_chronic' => null,
                'reach_registered' => true,
                'svhc' => null,
                'annex_vi' => null,
                'annex_xiv' => null,
                'annex_xvii' => null,
                'annex_vi_entry' => null,
                'annex_xiv_entry' => null,
                'annex_xvii_entry' => null,
                'source_type' => 'ECHA_REACH_REGISTERED',
                'source_version' => $datasetVersion,
                'source_reference' => $echaUuid ?: $sourceUrl,
                'source_url' => $sourceUrl ?: null,
                'effective_from' => null,
                'effective_until' => null,
                'legal_status' => 'registered',
                'is_current' => true,
                'regulatory_notes' => [
                    'dataset_type' => 'ECHA REACH Registered Substances',
                    'registered_name' => $preferredName,
                    'cas_no' => $casNo,
                    'ec_no' => $ecNo,
                    'echa_uuid' => $echaUuid,
                    'overview_url' => $sourceUrl ?: null,
                    'source_file_hash' => $context['file_hash'] ?? null,
                ],
            ];

            $preview = $this->determinePreviewStatus(
                $row,
                'EU_REACH_CLP',
                'ECHA REACH Registered Substances'
            );

            $row['status'] = $preview['status'];
            $row['differences'] = $preview['differences'];
            $row['errors'] = $preview['errors'];
            $row['warnings'] = $this->identifierWarnings($row);

            $summary['total']++;
            $summary[$row['status']]++;

            foreach ($preview['change_types'] ?? [] as $changeType) {
                if (array_key_exists($changeType, $summary['reach_breakdown'])) {
                    $summary['reach_breakdown'][$changeType]++;
                }
            }

            if (
                $row['status'] === 'changed' &&
                count(array_diff(
                    $preview['change_types'] ?? [],
                    ['source_record_missing']
                )) > 0
            ) {
                $summary['reach_breakdown']['real_data_change']++;
            }

            $row['change_types'] = $preview['change_types'] ?? [];

            if (!$previewOnly || count($records) < $previewLimit) {
                $records[] = $row;
            }
        }

        if ($summary['total'] === 0) {
            throw new \RuntimeException(
                'ECHA REACH Registered Substances dosyasında aktarılabilir kayıt bulunamadı.'
            );
        }

        return [
            'rows' => $records,
            'summary' => $summary,
            'identifier_counts' => [],
            'meta' => [
                'parser_type' => 'echa_reach_registered',
                'regulation_profile' => 'EU_REACH_CLP',
                'source_name' => 'ECHA REACH Registered Substances',
                'dataset_version' => $datasetVersion,
                'source_date' => $sourceDate,
                'preview_limited' => $previewOnly && $summary['total'] > $previewLimit,
                'preview_limit' => $previewLimit,
            ],
        ];
    }

    private function extractEchaChemUuid(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        if (
            preg_match(
                '~chem\.echa\.europa\.eu/([^/]+)/overview~i',
                $url,
                $match
            )
        ) {
            return trim((string) $match[1]) ?: null;
        }

        return null;
    }

    private function parseEchaAnnexVi(
        $spreadsheet,
        array $context
    ): array {
        $mainSheet = $spreadsheet->getSheetByName('ATP23');
        $historySheet = $spreadsheet->getSheetByName('History');

        $this->validateEchaSheet($mainSheet, 3);
        $this->validateEchaSheet($historySheet, 1);

        $datasetVersion = 'ATP23';
        $today = now()->startOfDay();

        $identifierCounts = [
            'cas' => [],
            'ec' => [],
        ];

        $mainRawRows = [];

        for ($rowNumber = 4; $rowNumber <= $mainSheet->getHighestRow(); $rowNumber++) {
            $row = $this->readEchaRow(
                $mainSheet,
                $rowNumber,
                $datasetVersion,
                'ATP23',
                $context
            );

            if (!$row) {
                continue;
            }

            $mainRawRows[] = $row;

            foreach ($row['all_cas_numbers'] as $cas) {
                $identifierCounts['cas'][$cas][$row['index_no']] = true;
            }

            foreach ($row['all_ec_numbers'] as $ec) {
                $identifierCounts['ec'][$ec][$row['index_no']] = true;
            }
        }

        foreach (['cas', 'ec'] as $type) {
            foreach ($identifierCounts[$type] as $value => $indexes) {
                $identifierCounts[$type][$value] = count($indexes);
            }
        }

        $historyByIndex = [];

        for ($rowNumber = 2; $rowNumber <= $historySheet->getHighestRow(); $rowNumber++) {
            $row = $this->readEchaRow(
                $historySheet,
                $rowNumber,
                $datasetVersion,
                'History',
                $context
            );

            if (!$row || !$row['index_no']) {
                continue;
            }

            $historyByIndex[$row['index_no']][] = $row;
        }

        $records = [];

        foreach ($mainRawRows as $mainRow) {
            $effectiveFrom = $mainRow['effective_from']
                ? Carbon::parse($mainRow['effective_from'])->startOfDay()
                : null;

            if ($effectiveFrom && $effectiveFrom->gt($today)) {
                $previous = $this->findLatestApplicableHistoryRow(
                    $historyByIndex[$mainRow['index_no']] ?? [],
                    $today
                );

                if ($previous) {
                    $previous['is_current'] = true;
                    $previous['legal_status'] = 'in_force';
                    $records[] = $previous;
                }

                $mainRow['is_current'] = false;
                $mainRow['legal_status'] = 'future';
                $records[] = $mainRow;
            } else {
                $mainRow['is_current'] = true;
                $mainRow['legal_status'] = 'in_force';
                $records[] = $mainRow;
            }
        }

        $summary = [
            'new' => 0,
            'changed' => 0,
            'same' => 0,
            'invalid' => 0,
            'total' => 0,
        ];

        foreach ($records as $key => $row) {
            $preview = $this->determinePreviewStatus(
                $row,
                'EU_REACH_CLP',
                'ECHA Annex VI to CLP'
            );

            $records[$key]['status'] = $preview['status'];
            $records[$key]['differences'] = $preview['differences'];
            $records[$key]['errors'] = $preview['errors'];
            $records[$key]['warnings'] = $this->identifierWarnings($row);

            $summary['total']++;
            $summary[$preview['status']]++;
        }

        return [
            'rows' => $records,
            'summary' => $summary,
            'identifier_counts' => $identifierCounts,
            'meta' => [
                'parser_type' => 'echa_annex_vi',
                'regulation_profile' => 'EU_REACH_CLP',
                'source_name' => 'ECHA Annex VI to CLP',
                'dataset_version' => $datasetVersion,
            ],
        ];
    }

    private function isEchaAnnexXivAuthorisationListFile(
        string $absolutePath
    ): bool {
        $handle = fopen($absolutePath, 'rb');

        if (!$handle) {
            return false;
        }

        try {
            $lineNumber = 0;
            $foundTitle = false;
            $foundHeader = false;

            while (
                ($line = fgets($handle)) !== false &&
                $lineNumber < 80
            ) {
                $lineNumber++;

                $clean = trim(
                    $this->cleanCsvBom($line)
                );

                if (
                    strcasecmp(
                        $clean,
                        'Authorisation List'
                    ) === 0
                ) {
                    $foundTitle = true;
                }

                if (
                    str_contains($clean, '"Substance name"') &&
                    str_contains($clean, '"Entry No."') &&
                    str_contains($clean, '"Sunset Date"') &&
                    str_contains(
                        $clean,
                        '"Regulation amending Annex XIV"'
                    )
                ) {
                    $foundHeader = true;
                }

                if ($foundTitle && $foundHeader) {
                    return true;
                }
            }
        } finally {
            fclose($handle);
        }

        return false;
    }

    private function isEchaAnnexXviiRestrictionListFile(
        string $absolutePath
    ): bool {
        $handle = fopen($absolutePath, 'rb');

        if (!$handle) {
            return false;
        }

        try {
            $lineNumber = 0;
            $foundTitle = false;
            $foundHeader = false;

            while (
                ($line = fgets($handle)) !== false &&
                $lineNumber < 80
            ) {
                $lineNumber++;

                $clean = trim(
                    $this->cleanCsvBom($line)
                );

                if (
                    strcasecmp(
                        $clean,
                        'Restriction list'
                    ) === 0
                ) {
                    $foundTitle = true;
                }

                if (
                    str_contains($clean, '"Substance name"') &&
                    str_contains($clean, '"Entry no."') &&
                    str_contains($clean, '"Conditions"') &&
                    str_contains($clean, '"First published"')
                ) {
                    $foundHeader = true;
                }

                if ($foundTitle && $foundHeader) {
                    return true;
                }
            }
        } finally {
            fclose($handle);
        }

        return false;
    }

    private function parseEchaAnnexXviiRestrictionList(
        string $absolutePath,
        array $context
    ): array {
        $handle = fopen($absolutePath, 'rb');

        if (!$handle) {
            throw new \RuntimeException(
                'ECHA Annex XVII Restriction List dosyası açılamadı.'
            );
        }

        $header = null;
        $headerMap = [];
        $exportedAt = null;
        $records = [];
        $physicalLine = 0;

        try {
            while (($line = fgets($handle)) !== false) {
                $physicalLine++;

                $clean = trim(
                    $this->cleanCsvBom($line)
                );

                if (
                    $exportedAt === null &&
                    preg_match(
                        '/^Export date:\s*(.+)$/i',
                        $clean,
                        $match
                    )
                ) {
                    $exportedAt = trim($match[1]);
                }

                if (
                    $header === null &&
                    str_contains($clean, '"Substance name"') &&
                    str_contains($clean, '"Entry no."') &&
                    str_contains($clean, '"Conditions"') &&
                    str_contains($clean, '"First published"')
                ) {
                    $header = str_getcsv(
                        rtrim($line, "\r\n"),
                        "\t",
                        '"',
                        "\\"
                    );

                    foreach ($header as $column => $heading) {
                        $heading = trim((string) $heading);

                        if ($heading !== '') {
                            $headerMap[$heading] = $column;
                        }
                    }

                    break;
                }
            }

            if ($header === null) {
                throw new \RuntimeException(
                    'ECHA Annex XVII başlık satırı bulunamadı.'
                );
            }

            $requiredHeaders = [
                'Substance name',
                'Description',
                'EC No.',
                'CAS No.',
                'Entry no.',
                'Conditions',
                'Appendices',
                'Standards',
                'History',
                'Q&As',
                'Remarks',
                'First published',
            ];

            foreach ($requiredHeaders as $requiredHeader) {
                if (!array_key_exists($requiredHeader, $headerMap)) {
                    throw new \RuntimeException(
                        'ECHA Annex XVII kolon yapısı beklenen formatta değil. ' .
                        'Eksik kolon: ' . $requiredHeader
                    );
                }
            }

            $datasetVersion =
                $this->parseEchaSvhcExportDate($exportedAt)
                ?? ($context['source_version'] ?? null);

            while (
                ($record = fgetcsv(
                    $handle,
                    0,
                    "\t",
                    '"',
                    "\\"
                )) !== false
            ) {
                $physicalLine++;

                if (!is_array($record)) {
                    continue;
                }

                $hasValue = false;

                foreach ($record as $value) {
                    if (trim((string) $value) !== '') {
                        $hasValue = true;
                        break;
                    }
                }

                if (!$hasValue) {
                    continue;
                }

                $data = [];

                foreach ($requiredHeaders as $heading) {
                    $data[$heading] = trim(
                        (string) (
                            $record[$headerMap[$heading]]
                            ?? ''
                        )
                    );
                }

                $preferredName = trim(
                    (string) $data['Substance name']
                );

                if ($preferredName === '') {
                    continue;
                }

                $casNo = $this->cleanIdentifier(
                    $data['CAS No.'] ?? null
                );

                $ecNo = $this->cleanIdentifier(
                    $data['EC No.'] ?? null
                );

                $entryNo = trim(
                    (string) ($data['Entry no.'] ?? '')
                );

                if ($entryNo === '') {
                    continue;
                }

                $firstPublished = $this->parseEchaSvhcDate(
                    $data['First published'] ?? null
                );

                $conditionUrls = $this->extractUrls(
                    $data['Conditions'] ?? null
                );

                $appendixUrls = $this->extractUrls(
                    $data['Appendices'] ?? null
                );

                $standardUrls = $this->extractUrls(
                    $data['Standards'] ?? null
                );

                $historyUrls = $this->extractUrls(
                    $data['History'] ?? null
                );

                $qaUrls = $this->extractUrls(
                    $data['Q&As'] ?? null
                );

                $row = [
                    'row_number' => $physicalLine,
                    'preferred_name' => $preferredName,
                    'cas_no' => $casNo,
                    'ec_no' => $ecNo,
                    'index_no' => null,
                    'all_cas_numbers' => array_values(array_filter([
                        $casNo,
                    ])),
                    'all_ec_numbers' => array_values(array_filter([
                        $ecNo,
                    ])),
                    'hazard_classifications' => null,
                    'signal_word' => null,
                    'pictograms' => null,
                    'hazard_statements' => null,
                    'supplemental_hazard_statements' => null,
                    'specific_concentration_limits' => null,
                    'ate_data' => null,
                    'm_factor_acute' => null,
                    'm_factor_chronic' => null,
                    'reach_registered' => null,
                    'svhc' => null,
                    'annex_vi' => null,
                    'annex_xiv' => null,
                    'annex_xvii' => true,
                    'annex_vi_entry' => null,
                    'annex_xiv_entry' => null,
                    'annex_xvii_entry' => $entryNo,
                    'source_type' => 'ECHA_ANNEX_XVII',
                    'source_version' => $datasetVersion,
                    'source_reference' => $entryNo,
                    'source_url' => $conditionUrls[0] ?? null,
                    'effective_from' => null,
                    'effective_until' => null,
                    'legal_status' => 'in_force',
                    'is_current' => true,
                    'regulatory_notes' => [
                        'dataset_type' =>
                            'ECHA Annex XVII Restriction List',
                        'export_date' => $exportedAt ?: null,
                        'entry_no' => $entryNo,
                        'description' => trim(
                            (string) ($data['Description'] ?? '')
                        ) ?: null,
                        'conditions_raw' => trim(
                            (string) ($data['Conditions'] ?? '')
                        ) ?: null,
                        'condition_urls' => $conditionUrls,
                        'appendices_raw' => trim(
                            (string) ($data['Appendices'] ?? '')
                        ) ?: null,
                        'appendix_urls' => $appendixUrls,
                        'standards_raw' => trim(
                            (string) ($data['Standards'] ?? '')
                        ) ?: null,
                        'standard_urls' => $standardUrls,
                        'history_raw' => trim(
                            (string) ($data['History'] ?? '')
                        ) ?: null,
                        'history_urls' => $historyUrls,
                        'qas_raw' => trim(
                            (string) ($data['Q&As'] ?? '')
                        ) ?: null,
                        'qa_urls' => $qaUrls,
                        'remarks' => trim(
                            (string) ($data['Remarks'] ?? '')
                        ) ?: null,
                        'first_published' => $firstPublished,
                        'all_cas_numbers' => array_values(array_filter([
                            $casNo,
                        ])),
                        'all_ec_numbers' => array_values(array_filter([
                            $ecNo,
                        ])),
                    ],
                ];

                $preview = $this->determinePreviewStatus(
                    $row,
                    'EU_REACH_CLP',
                    'ECHA Restriction List (Annex XVII)'
                );

                $row['status'] = $preview['status'];
                $row['differences'] = $preview['differences'];
                $row['errors'] = $preview['errors'];
            $row['warnings'] = $this->identifierWarnings($row);

                $records[] = $row;
            }
        } finally {
            fclose($handle);
        }

        $summary = [
            'new' => 0,
            'changed' => 0,
            'same' => 0,
            'invalid' => 0,
            'total' => 0,
        ];

        foreach ($records as $row) {
            $summary['total']++;
            $summary[$row['status']]++;
        }

        return [
            'rows' => $records,
            'summary' => $summary,
            'identifier_counts' => [],
            'meta' => [
                'parser_type' => 'echa_annex_xvii',
                'regulation_profile' => 'EU_REACH_CLP',
                'source_name' => 'ECHA Restriction List (Annex XVII)',
                'dataset_version' => $datasetVersion,
                'source_date' => $datasetVersion,
            ],
        ];
    }

    private function parseEchaAnnexXivAuthorisationList(
        string $absolutePath,
        array $context
    ): array {
        $handle = fopen($absolutePath, 'rb');

        if (!$handle) {
            throw new \RuntimeException(
                'ECHA Annex XIV Authorisation List dosyası açılamadı.'
            );
        }

        $header = null;
        $headerMap = [];
        $exportedAt = null;
        $records = [];
        $physicalLine = 0;

        try {
            while (($line = fgets($handle)) !== false) {
                $physicalLine++;

                $clean = trim(
                    $this->cleanCsvBom($line)
                );

                if (
                    $exportedAt === null &&
                    preg_match(
                        '/^Export date:\s*(.+)$/i',
                        $clean,
                        $match
                    )
                ) {
                    $exportedAt = trim($match[1]);
                }

                if (
                    $header === null &&
                    str_contains($clean, '"Substance name"') &&
                    str_contains($clean, '"Entry No."') &&
                    str_contains($clean, '"Sunset Date"') &&
                    str_contains(
                        $clean,
                        '"Regulation amending Annex XIV"'
                    )
                ) {
                    $header = str_getcsv(
                        rtrim($line, "\r\n"),
                        "\t",
                        '"',
                        "\\"
                    );

                    foreach ($header as $column => $heading) {
                        $heading = trim((string) $heading);

                        if ($heading !== '') {
                            $headerMap[$heading] = $column;
                        }
                    }

                    break;
                }
            }

            if ($header === null) {
                throw new \RuntimeException(
                    'ECHA Annex XIV başlık satırı bulunamadı.'
                );
            }

            $requiredHeaders = [
                'Substance name',
                'Description',
                'EC No.',
                'CAS No.',
                'Entry No.',
                'Latest application date',
                'Latest application date (text)',
                'Sunset Date',
                'Sunset date (text)',
                'Exempted (categories of) uses',
                'Intrinsic property',
                'Review Periods',
                'Date of inclusion in Authorisation List',
                'Regulation amending Annex XIV',
                'Remarks',
                'First published',
            ];

            foreach ($requiredHeaders as $requiredHeader) {
                if (!array_key_exists($requiredHeader, $headerMap)) {
                    throw new \RuntimeException(
                        'ECHA Annex XIV kolon yapısı beklenen formatta değil. ' .
                        'Eksik kolon: ' . $requiredHeader
                    );
                }
            }

            $datasetVersion =
                $this->parseEchaSvhcExportDate($exportedAt)
                ?? ($context['source_version'] ?? null);

            while (
                ($record = fgetcsv(
                    $handle,
                    0,
                    "\t",
                    '"',
                    "\\"
                )) !== false
            ) {
                $physicalLine++;

                if (!is_array($record)) {
                    continue;
                }

                $hasValue = false;

                foreach ($record as $value) {
                    if (trim((string) $value) !== '') {
                        $hasValue = true;
                        break;
                    }
                }

                if (!$hasValue) {
                    continue;
                }

                $data = [];

                foreach ($requiredHeaders as $heading) {
                    $data[$heading] = trim(
                        (string) (
                            $record[$headerMap[$heading]]
                            ?? ''
                        )
                    );
                }

                $preferredName = trim(
                    (string) $data['Substance name']
                );

                if ($preferredName === '') {
                    continue;
                }

                $casNo = $this->cleanIdentifier(
                    $data['CAS No.'] ?? null
                );

                $ecNo = $this->cleanIdentifier(
                    $data['EC No.'] ?? null
                );

                $entryNo = trim(
                    (string) ($data['Entry No.'] ?? '')
                );

                if ($entryNo === '') {
                    continue;
                }

                $effectiveFrom = $this->parseEchaSvhcDate(
                    $data['Date of inclusion in Authorisation List']
                    ?? null
                );

                $latestApplicationDate = $this->parseEchaSvhcDate(
                    $data['Latest application date'] ?? null
                );

                $sunsetDate = $this->parseEchaSvhcDate(
                    $data['Sunset Date'] ?? null
                );

                $firstPublished = $this->parseEchaSvhcDate(
                    $data['First published'] ?? null
                );

                $regulationRaw = trim(
                    (string) (
                        $data['Regulation amending Annex XIV']
                        ?? ''
                    )
                );

                $regulationUrls = $this->extractUrls(
                    $regulationRaw
                );

                $regulationText = trim(
                    html_entity_decode(
                        strip_tags($regulationRaw),
                        ENT_QUOTES | ENT_HTML5,
                        'UTF-8'
                    )
                );

                $row = [
                    'row_number' => $physicalLine,
                    'preferred_name' => $preferredName,
                    'cas_no' => $casNo,
                    'ec_no' => $ecNo,
                    'index_no' => null,
                    'all_cas_numbers' => array_values(
                        array_filter([$casNo])
                    ),
                    'all_ec_numbers' => array_values(
                        array_filter([$ecNo])
                    ),
                    'hazard_classifications' => null,
                    'signal_word' => null,
                    'pictograms' => null,
                    'hazard_statements' => null,
                    'supplemental_hazard_statements' => null,
                    'specific_concentration_limits' => null,
                    'ate_data' => null,
                    'm_factor_acute' => null,
                    'm_factor_chronic' => null,
                    'reach_registered' => null,
                    'svhc' => null,
                    'annex_vi' => null,
                    'annex_xiv' => true,
                    'annex_xvii' => null,
                    'annex_vi_entry' => null,
                    'annex_xiv_entry' => $entryNo,
                    'annex_xvii_entry' => null,
                    'source_type' => 'ECHA_ANNEX_XIV',
                    'source_version' => $datasetVersion,
                    'source_reference' => $entryNo,
                    'source_url' => $regulationUrls[0] ?? null,
                    'effective_from' => $effectiveFrom,
                    'effective_until' => null,
                    'legal_status' => 'in_force',
                    'is_current' => true,
                    'regulatory_notes' => [
                        'dataset_type' =>
                            'ECHA Annex XIV Authorisation List',
                        'export_date' => $exportedAt ?: null,
                        'entry_no' => $entryNo,
                        'description' => trim(
                            (string) ($data['Description'] ?? '')
                        ) ?: null,
                        'latest_application_date' =>
                            $latestApplicationDate,
                        'latest_application_date_text' => trim(
                            (string) (
                                $data['Latest application date (text)']
                                ?? ''
                            )
                        ) ?: null,
                        'sunset_date' => $sunsetDate,
                        'sunset_date_text' => trim(
                            (string) (
                                $data['Sunset date (text)']
                                ?? ''
                            )
                        ) ?: null,
                        'intrinsic_property' =>
                            $this->splitEchaSvhcMultiValue(
                                $data['Intrinsic property'] ?? null
                            ),
                        'exempted_uses' => trim(
                            (string) (
                                $data['Exempted (categories of) uses']
                                ?? ''
                            )
                        ) ?: null,
                        'review_periods' => trim(
                            (string) ($data['Review Periods'] ?? '')
                        ) ?: null,
                        'date_of_inclusion' => $effectiveFrom,
                        'regulation_amending_annex_xiv' =>
                            $regulationText ?: null,
                        'regulation_urls' => $regulationUrls,
                        'remarks' => trim(
                            (string) ($data['Remarks'] ?? '')
                        ) ?: null,
                        'first_published' => $firstPublished,
                        'all_cas_numbers' => array_values(
                            array_filter([$casNo])
                        ),
                        'all_ec_numbers' => array_values(
                            array_filter([$ecNo])
                        ),
                    ],
                ];

                $preview = $this->determinePreviewStatus(
                    $row,
                    'EU_REACH_CLP',
                    'ECHA Authorisation List (Annex XIV)'
                );

                $row['status'] = $preview['status'];
                $row['differences'] = $preview['differences'];
                $row['errors'] = $preview['errors'];
            $row['warnings'] = $this->identifierWarnings($row);

                $records[] = $row;
            }
        } finally {
            fclose($handle);
        }

        $summary = [
            'new' => 0,
            'changed' => 0,
            'same' => 0,
            'invalid' => 0,
            'total' => 0,
        ];

        foreach ($records as $row) {
            $summary['total']++;
            $summary[$row['status']]++;
        }

        return [
            'rows' => $records,
            'summary' => $summary,
            'identifier_counts' => [],
            'meta' => [
                'parser_type' => 'echa_annex_xiv',
                'regulation_profile' => 'EU_REACH_CLP',
                'source_name' =>
                    'ECHA Authorisation List (Annex XIV)',
                'dataset_version' => $datasetVersion,
            ],
        ];
    }

    private function isEchaSvhcCandidateListFile(
        string $absolutePath
    ): bool {
        $handle = fopen($absolutePath, 'rb');

        if (!$handle) {
            return false;
        }

        try {
            $lineNumber = 0;
            $foundTitle = false;
            $foundHeader = false;

            while (($line = fgets($handle)) !== false && $lineNumber < 40) {
                $lineNumber++;
                $clean = trim($this->cleanCsvBom($line));

                if (
                    stripos(
                        $clean,
                        'Candidate List of SVHC for authorisation'
                    ) !== false
                ) {
                    $foundTitle = true;
                }

                if (
                    str_contains($clean, '"Substance name"') &&
                    str_contains($clean, '"Reason for inclusion"') &&
                    str_contains($clean, '"Date of inclusion"') &&
                    str_contains($clean, '"Decision"')
                ) {
                    $foundHeader = true;
                }

                if ($foundTitle && $foundHeader) {
                    return true;
                }
            }
        } finally {
            fclose($handle);
        }

        return false;
    }

    private function parseEchaSvhcCandidateList(
        string $absolutePath,
        array $context
    ): array {
        $handle = fopen($absolutePath, 'rb');

        if (!$handle) {
            throw new \RuntimeException(
                'ECHA SVHC Candidate List dosyası açılamadı.'
            );
        }

        $header = null;
        $headerMap = [];
        $exportedAt = null;
        $rawRows = [];
        $physicalLine = 0;

        try {
            while (($line = fgets($handle)) !== false) {
                $physicalLine++;
                $clean = trim($this->cleanCsvBom($line));

                if (
                    $exportedAt === null &&
                    preg_match(
                        '/^Export date:\s*(.+)$/i',
                        $clean,
                        $match
                    )
                ) {
                    $exportedAt = trim($match[1]);
                }

                if (
                    $header === null &&
                    str_contains($clean, '"Substance name"') &&
                    str_contains($clean, '"Reason for inclusion"') &&
                    str_contains($clean, '"Date of inclusion"') &&
                    str_contains($clean, '"Decision"')
                ) {
                    $header = str_getcsv(
                        rtrim($line, "\r\n"),
                        "\t",
                        '"',
                        "\\"
                    );

                    foreach ($header as $column => $heading) {
                        $heading = trim((string) $heading);

                        if ($heading !== '') {
                            $headerMap[$heading] = $column;
                        }
                    }

                    break;
                }
            }

            if ($header === null) {
                throw new \RuntimeException(
                    'ECHA SVHC Candidate List başlık satırı bulunamadı.'
                );
            }

            $requiredHeaders = [
                'Substance name',
                'Description',
                'EC No.',
                'CAS No.',
                'Reason for inclusion',
                'Date of inclusion',
                'Decision',
                'IUCLID dataset',
                'Support document',
                'Response to comments',
                'Remarks',
            ];

            foreach ($requiredHeaders as $requiredHeader) {
                if (!array_key_exists($requiredHeader, $headerMap)) {
                    throw new \RuntimeException(
                        'ECHA SVHC kolon yapısı beklenen formatta değil. ' .
                        'Eksik kolon: ' . $requiredHeader
                    );
                }
            }

            while (
                ($record = fgetcsv(
                    $handle,
                    0,
                    "\t",
                    '"',
                    "\\"
                )) !== false
            ) {
                $physicalLine++;

                if (!is_array($record)) {
                    continue;
                }

                $hasValue = false;

                foreach ($record as $value) {
                    if (trim((string) $value) !== '') {
                        $hasValue = true;
                        break;
                    }
                }

                if (!$hasValue) {
                    continue;
                }

                $data = [];

                foreach ($requiredHeaders as $heading) {
                    $data[$heading] = trim(
                        (string) (
                            $record[$headerMap[$heading]]
                            ?? ''
                        )
                    );
                }

                $preferredName = trim($data['Substance name']);

                if ($preferredName === '') {
                    continue;
                }

                $casNo = $this->cleanIdentifier(
                    $data['CAS No.'] ?? null
                );

                $ecNo = $this->cleanIdentifier(
                    $data['EC No.'] ?? null
                );

                $reasonForInclusion =
                    $this->splitEchaSvhcMultiValue(
                        $data['Reason for inclusion'] ?? null
                    );

                $decisionUrls = $this->extractUrls(
                    $data['Decision'] ?? null
                );

                $iuclidUrls = $this->extractUrls(
                    $data['IUCLID dataset'] ?? null
                );

                $supportUrls = $this->extractUrls(
                    $data['Support document'] ?? null
                );

                $responseUrls = $this->extractUrls(
                    $data['Response to comments'] ?? null
                );

                $effectiveFrom = $this->parseEchaSvhcDate(
                    $data['Date of inclusion'] ?? null
                );

                $sourceReference =
                    $this->buildEchaSvhcSourceReference(
                        $decisionUrls,
                        $data['Decision'] ?? null,
                        $casNo,
                        $ecNo,
                        $preferredName
                    );

                $rawRows[] = [
                    'row_number' => $physicalLine,
                    'preferred_name' => $preferredName,
                    'cas_no' => $casNo,
                    'ec_no' => $ecNo,
                    'index_no' => null,
                    'all_cas_numbers' => array_values(array_filter([
                        $casNo,
                    ])),
                    'all_ec_numbers' => array_values(array_filter([
                        $ecNo,
                    ])),
                    'hazard_classifications' => null,
                    'signal_word' => null,
                    'pictograms' => null,
                    'hazard_statements' => null,
                    'supplemental_hazard_statements' => null,
                    'specific_concentration_limits' => null,
                    'ate_data' => null,
                    'm_factor_acute' => null,
                    'm_factor_chronic' => null,
                    'reach_registered' => null,
                    'svhc' => true,
                    'annex_vi' => null,
                    'annex_xiv' => null,
                    'annex_xvii' => null,
                    'annex_vi_entry' => null,
                    'annex_xiv_entry' => null,
                    'annex_xvii_entry' => null,
                    'source_type' => 'ECHA_SVHC',
                    'source_version' => null,
                    'source_reference' => $sourceReference,
                    'source_url' => $decisionUrls[0] ?? null,
                    'effective_from' => $effectiveFrom,
                    'effective_until' => null,
                    'legal_status' => 'in_force',
                    'is_current' => true,
                    'regulatory_notes' => [
                        'source_substance_names' => [
                            $preferredName,
                        ],
                        'descriptions' => array_values(array_filter([
                            trim($data['Description'] ?? ''),
                        ])),
                        'reason_for_inclusion' => $reasonForInclusion,
                        'decision_urls' => $decisionUrls,
                        'iuclid_dataset_urls' => $iuclidUrls,
                        'support_document_urls' => $supportUrls,
                        'response_to_comments_urls' => $responseUrls,
                        'remarks' => array_values(array_filter([
                            trim($data['Remarks'] ?? ''),
                        ])),
                        'date_of_inclusion' => $effectiveFrom,
                        'raw_date_of_inclusion' =>
                            trim($data['Date of inclusion'] ?? ''),
                        'candidate_list_row_numbers' => [
                            $physicalLine,
                        ],
                        'source_file_hash' =>
                            $context['file_hash'] ?? null,
                    ],
                    'status' => 'new',
                    'differences' => [],
                    'errors' => [],
                ];
            }
        } finally {
            fclose($handle);
        }

        if (!count($rawRows)) {
            throw new \RuntimeException(
                'ECHA SVHC Candidate List içinde aktarılabilir kayıt bulunamadı.'
            );
        }

        $exportDate = $this->parseEchaSvhcExportDate(
            $exportedAt
        );

        $datasetVersion = $exportDate
            ? 'SVHC-' . $exportDate
            : 'SVHC';

        $groupedRows = [];

        foreach ($rawRows as $row) {
            $groupKey = $this->echaSvhcGroupKey($row);

            if (!isset($groupedRows[$groupKey])) {
                $row['source_version'] = $datasetVersion;
                $groupedRows[$groupKey] = $row;
                continue;
            }

            $existing = $groupedRows[$groupKey];

            $existing['preferred_name'] =
                $this->choosePreferredSvhcName(
                    $existing,
                    $row
                );

            if (!$existing['cas_no'] && $row['cas_no']) {
                $existing['cas_no'] = $row['cas_no'];
            }

            if (!$existing['ec_no'] && $row['ec_no']) {
                $existing['ec_no'] = $row['ec_no'];
            }

            $existing['all_cas_numbers'] =
                $this->mergeUniqueValues(
                    $existing['all_cas_numbers'] ?? [],
                    $row['all_cas_numbers'] ?? []
                );

            $existing['all_ec_numbers'] =
                $this->mergeUniqueValues(
                    $existing['all_ec_numbers'] ?? [],
                    $row['all_ec_numbers'] ?? []
                );

            foreach (
                [
                    'source_substance_names',
                    'descriptions',
                    'reason_for_inclusion',
                    'decision_urls',
                    'iuclid_dataset_urls',
                    'support_document_urls',
                    'response_to_comments_urls',
                    'remarks',
                    'candidate_list_row_numbers',
                ]
                as $noteKey
            ) {
                $existing['regulatory_notes'][$noteKey] =
                    $this->mergeUniqueValues(
                        $existing['regulatory_notes'][$noteKey] ?? [],
                        $row['regulatory_notes'][$noteKey] ?? []
                    );
            }

            $groupedRows[$groupKey] = $existing;
        }

        $records = array_values($groupedRows);

        $summary = [
            'new' => 0,
            'changed' => 0,
            'same' => 0,
            'invalid' => 0,
            'total' => 0,
        ];

        foreach ($records as $key => $row) {
            $preview = $this->determinePreviewStatus(
                $row,
                'EU_REACH_CLP',
                'ECHA Candidate List of SVHC for Authorisation'
            );

            $records[$key]['status'] = $preview['status'];
            $records[$key]['differences'] = $preview['differences'];
            $records[$key]['errors'] = $preview['errors'];
            $records[$key]['warnings'] = $this->identifierWarnings($row);

            $summary['total']++;
            $summary[$preview['status']]++;
        }

        return [
            'rows' => $records,
            'summary' => $summary,
            'identifier_counts' => [],
            'meta' => [
                'parser_type' => 'echa_svhc_candidate_list',
                'regulation_profile' => 'EU_REACH_CLP',
                'source_name' =>
                    'ECHA Candidate List of SVHC for Authorisation',
                'dataset_version' => $datasetVersion,
                'source_date' => $exportDate,
                'raw_row_count' => count($rawRows),
                'grouped_row_count' => count($records),
                'exported_at' => $exportedAt,
            ],
        ];
    }

    private function parseEchaSvhcDate($value): ?string
    {
        $value = trim((string) $value);

        if ($value === '' || $value === '-') {
            return null;
        }

        foreach (['d-M-Y', 'd-M-y', 'Y-m-d'] as $format) {
            try {
                $date = Carbon::createFromFormat($format, $value);

                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            } catch (\Throwable $e) {
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function parseEchaSvhcExportDate(
        ?string $value
    ): ?string {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        foreach (
            [
                'd-M-Y H:i:s',
                'd-M-Y H:i',
                'd-M-Y',
            ]
            as $format
        ) {
            try {
                $date = Carbon::createFromFormat($format, $value);

                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            } catch (\Throwable $e) {
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function splitEchaSvhcMultiValue(
        $value
    ): array {
        $value = trim((string) $value);

        if ($value === '' || $value === '-') {
            return [];
        }

        $parts = preg_split(
            '/"+|\r\n|\r|\n/',
            $value
        );

        $parts = array_values(array_unique(array_filter(
            array_map(
                fn ($item) => trim((string) $item),
                $parts
            ),
            fn ($item) => $item !== '' && $item !== '-'
        )));

        return $parts;
    }

    private function extractUrls($value): array
    {
        $value = trim((string) $value);

        if ($value === '') {
            return [];
        }

        preg_match_all(
            '~https?://[^\s"]+~i',
            $value,
            $matches
        );

        return array_values(array_unique(array_filter(
            array_map(
                fn ($url) => rtrim(
                    trim((string) $url),
                    ".,;)"
                ),
                $matches[0] ?? []
            )
        )));
    }

    private function buildEchaSvhcSourceReference(
        array $decisionUrls,
        ?string $rawDecision,
        ?string $casNo,
        ?string $ecNo,
        string $preferredName
    ): string {
        $decisionSeed = $decisionUrls[0]
            ?? trim((string) $rawDecision);

        if ($decisionSeed === '') {
            $decisionSeed = 'candidate-list';
        }

        $decisionKey = $decisionSeed;

        if (str_contains($decisionSeed, '/')) {
            $decisionKey = basename(
                parse_url(
                    $decisionSeed,
                    PHP_URL_PATH
                ) ?: $decisionSeed
            );
        }

        $identity = $casNo
            ? 'CAS:' . $casNo
            : (
                $ecNo
                    ? 'EC:' . $ecNo
                    : 'NAME:' . $this->normalizeSvhcName(
                        $preferredName
                    )
            );

        return 'SVHC:' .
            Str::limit($decisionKey, 80, '') .
            ':' .
            substr(
                hash('sha256', $identity),
                0,
                16
            );
    }

    private function echaSvhcGroupKey(
        array $row
    ): string {
        $notes = $row['regulatory_notes'] ?? [];
        $decisionUrls = $notes['decision_urls'] ?? [];

        $decision = $decisionUrls[0]
            ?? ($row['source_url'] ?? '')
            ?? '';

        $identity = !empty($row['cas_no'])
            ? 'CAS:' . $row['cas_no']
            : (
                !empty($row['ec_no'])
                    ? 'EC:' . $row['ec_no']
                    : 'NAME:' . $this->normalizeSvhcName(
                        $row['preferred_name'] ?? ''
                    )
            );

        return hash(
            'sha256',
            $decision . '|' . $identity
        );
    }

    private function normalizeSvhcName(
        ?string $value
    ): string {
        $value = mb_strtolower(
            trim((string) $value),
            'UTF-8'
        );

        return preg_replace(
            '/\s+/u',
            ' ',
            $value
        ) ?? $value;
    }

    private function choosePreferredSvhcName(
        array $existing,
        array $incoming
    ): string {
        $existingScore = 0;
        $incomingScore = 0;

        if (!empty($existing['ec_no'])) {
            $existingScore += 2;
        }

        if (!empty($existing['cas_no'])) {
            $existingScore += 1;
        }

        if (!empty($incoming['ec_no'])) {
            $incomingScore += 2;
        }

        if (!empty($incoming['cas_no'])) {
            $incomingScore += 1;
        }

        if ($incomingScore > $existingScore) {
            return $incoming['preferred_name']
                ?? $existing['preferred_name']
                ?? '';
        }

        return $existing['preferred_name']
            ?? $incoming['preferred_name']
            ?? '';
    }

    private function mergeUniqueValues(
        array $first,
        array $second
    ): array {
        $result = [];

        foreach (array_merge($first, $second) as $value) {
            if (
                $value === null ||
                (is_string($value) && trim($value) === '')
            ) {
                continue;
            }

            $key = is_scalar($value)
                ? (string) $value
                : json_encode($value);

            if (!array_key_exists($key, $result)) {
                $result[$key] = $value;
            }
        }

        return array_values($result);
    }

    private function validateEchaSheet(
        Worksheet $sheet,
        int $headerRow
    ): void {
        $expected = [
            'Index No',
            'ATP',
            'CELEX',
            'Chemical Name',
            'EC No',
            'CAS No',
            'Hazard Class and Category Code(s)',
            'Classification Hazard Statement Code(s)',
            'Labelling Pictogram, Signal Word Code(s)',
            'Labelling Hazard Statement Code(s)',
            'Labelling Suppl. Hazard Statement Code(s)',
            'M, SCL, ATE',
            'Notes',
            'Comment',
            'In application',
            'EUR-Lex Link',
        ];

        foreach ($expected as $offset => $heading) {
            $actual = trim((string) $sheet->getCell([
                $offset + 1,
                $headerRow,
            ])->getValue());

            if ($actual !== $heading) {
                throw new \RuntimeException(
                    $sheet->getTitle() .
                    ' sayfasının ECHA Annex VI kolon yapısı beklenen formatta değil. ' .
                    'Beklenen kolon: ' . $heading .
                    ' | Okunan: ' . ($actual ?: '-')
                );
            }
        }
    }

    private function readEchaRow(
        Worksheet $sheet,
        int $rowNumber,
        string $datasetVersion,
        string $datasetSheet,
        array $context
    ): ?array {
        $indexNo = $this->cleanIdentifier(
            $sheet->getCell([1, $rowNumber])->getValue()
        );

        $chemicalName = trim((string) $sheet->getCell([
            4,
            $rowNumber,
        ])->getValue());

        if (!$indexNo && $chemicalName === '') {
            return null;
        }

        $atp = trim((string) $sheet->getCell([
            2,
            $rowNumber,
        ])->getValue());

        $celex = trim((string) $sheet->getCell([
            3,
            $rowNumber,
        ])->getValue());

        $allEcNumbers = $this->parseEchaIdentifiers(
            $sheet->getCell([5, $rowNumber])->getValue()
        );

        $allCasNumbers = $this->parseEchaIdentifiers(
            $sheet->getCell([6, $rowNumber])->getValue()
        );

        $hazardClassifications = $this->linesToArray(
            $sheet->getCell([7, $rowNumber])->getValue()
        );

        $classificationHazardStatements = $this->linesToArray(
            $sheet->getCell([8, $rowNumber])->getValue()
        );

        $labelCodes = $this->linesToArray(
            $sheet->getCell([9, $rowNumber])->getValue()
        );

        $labellingHazardStatements = $this->linesToArray(
            $sheet->getCell([10, $rowNumber])->getValue()
        );

        $supplementalHazardStatements = $this->linesToArray(
            $sheet->getCell([11, $rowNumber])->getValue()
        );

        $rawLimitData = trim((string) $sheet->getCell([
            12,
            $rowNumber,
        ])->getValue());

        $notes = trim((string) $sheet->getCell([
            13,
            $rowNumber,
        ])->getValue());

        $comment = trim((string) $sheet->getCell([
            14,
            $rowNumber,
        ])->getValue());

        $effectiveFrom = $this->echaDateCellToYmd(
            $sheet->getCell([15, $rowNumber])
        );

        $sourceUrl = trim((string) $sheet->getCell([
            16,
            $rowNumber,
        ])->getValue());

        $pictograms = [];
        $signalWord = null;

        foreach ($labelCodes ?? [] as $code) {
            if (preg_match('/^GHS0?[1-9]$/i', $code)) {
                $normalized = strtoupper($code);

                if (preg_match('/^GHS([1-9])$/', $normalized, $match)) {
                    $normalized = 'GHS0' . $match[1];
                }

                $pictograms[] = $normalized;
                continue;
            }

            if (strcasecmp($code, 'Dgr') === 0) {
                $signalWord = 'Danger';
            }

            if (strcasecmp($code, 'Wng') === 0) {
                $signalWord = 'Warning';
            }
        }

        $pictograms = array_values(array_unique($pictograms));

        $limitData = $this->parseEchaLimitData(
            $rawLimitData,
            $hazardClassifications
        );

        $effectiveDate = $effectiveFrom
            ? Carbon::parse($effectiveFrom)->startOfDay()
            : null;

        $isFuture = $effectiveDate
            ? $effectiveDate->gt(now()->startOfDay())
            : false;

        return [
            'row_number' => $rowNumber,
            'preferred_name' => $chemicalName,
            'cas_no' => $allCasNumbers[0] ?? null,
            'ec_no' => $allEcNumbers[0] ?? null,
            'index_no' => $indexNo,
            'all_cas_numbers' => $allCasNumbers,
            'all_ec_numbers' => $allEcNumbers,
            'hazard_classifications' => $hazardClassifications,
            'signal_word' => $signalWord,
            'pictograms' => count($pictograms) ? $pictograms : null,
            'hazard_statements' => $classificationHazardStatements,
            'supplemental_hazard_statements' => $supplementalHazardStatements,
            'specific_concentration_limits' =>
                $limitData['specific_concentration_limits'],
            'ate_data' => $limitData['ate_data'],
            'm_factor_acute' => $limitData['m_factor_acute'],
            'm_factor_chronic' => $limitData['m_factor_chronic'],
            'reach_registered' => null,
            'svhc' => null,
            'annex_vi' => true,
            'annex_xiv' => null,
            'annex_xvii' => null,
            'annex_vi_entry' => $indexNo,
            'annex_xiv_entry' => null,
            'annex_xvii_entry' => null,
            'source_type' => 'ECHA_ANNEX_VI',
            'source_version' => $atp ?: $datasetVersion,
            'source_reference' => $indexNo,
            'source_url' => $sourceUrl ?: null,
            'effective_from' => $effectiveFrom,
            'effective_until' => null,
            'legal_status' => $isFuture ? 'future' : 'in_force',
            'is_current' => !$isFuture,
            'regulatory_notes' => [
                'dataset_version' => $datasetVersion,
                'dataset_sheet' => $datasetSheet,
                'celex' => $celex ?: null,
                'all_cas_numbers' => $allCasNumbers,
                'all_ec_numbers' => $allEcNumbers,
                'labelling_hazard_statements' =>
                    $labellingHazardStatements,
                'raw_m_scl_ate' => $rawLimitData ?: null,
                'm_factor_values' => $limitData['m_factor_values'],
                'm_factor_ambiguous' => $limitData['m_factor_ambiguous'],
                'notes' => $notes ?: null,
                'comment' => $comment ?: null,
                'in_application' => $effectiveFrom,
                'eur_lex_link' => $sourceUrl ?: null,
                'source_file_hash' => $context['file_hash'] ?? null,
            ],
            'status' => 'new',
            'differences' => [],
            'errors' => [],
        ];
    }

    private function findLatestApplicableHistoryRow(
        array $rows,
        Carbon $today
    ): ?array {
        $candidates = array_filter(
            $rows,
            function ($row) use ($today) {
                if (empty($row['effective_from'])) {
                    return false;
                }

                return Carbon::parse($row['effective_from'])
                    ->startOfDay()
                    ->lte($today);
            }
        );

        if (!count($candidates)) {
            return null;
        }

        usort($candidates, function ($a, $b) {
            $aDate = $a['effective_from'] ?? '0000-00-00';
            $bDate = $b['effective_from'] ?? '0000-00-00';

            if ($aDate === $bDate) {
                return ($a['row_number'] ?? 0)
                    <=> ($b['row_number'] ?? 0);
            }

            return strcmp($aDate, $bDate);
        });

        return end($candidates) ?: null;
    }

    private function parseEchaIdentifiers($value): array
    {
        $value = trim((string) $value);

        if ($value === '' || $value === '-') {
            return [];
        }

        $parts = preg_split('/[\r\n;]+/', $value);
        $result = [];

        foreach ($parts as $part) {
            $part = preg_replace('/\s*\[\d+\]\s*$/', '', trim($part));
            $part = $this->cleanIdentifier($part);

            if (!$part || $part === '-') {
                continue;
            }

            $result[] = $part;
        }

        return array_values(array_unique($result));
    }

    private function parseEchaLimitData(
        ?string $raw,
        ?array $hazardClassifications
    ): array {
        $raw = trim((string) $raw);

        if ($raw === '') {
            return [
                'specific_concentration_limits' => null,
                'ate_data' => null,
                'm_factor_acute' => null,
                'm_factor_chronic' => null,
                'm_factor_values' => [],
                'm_factor_ambiguous' => false,
            ];
        }

        $lines = array_values(array_filter(
            array_map('trim', preg_split('/\r\n|\r|\n/', $raw)),
            fn ($line) => $line !== ''
        ));

        $sclLines = [];
        $ateRows = [];
        $mValues = [];
        $markers = [];

        foreach ($lines as $line) {
            if (preg_match('/\bATE\s*=\s*([0-9]+(?:[.,][0-9]+)?)/i', $line, $match)) {
                $route = null;

                if (preg_match('/^(oral|dermal|inhalation)\s*:/i', $line, $routeMatch)) {
                    $route = strtolower($routeMatch[1]);
                }

                $ateRows[] = [
                    'route' => $route,
                    'value' => (float) str_replace(',', '.', $match[1]),
                    'raw' => $line,
                ];

                continue;
            }

            if (preg_match('/^M\s*=\s*([0-9]+(?:[.,][0-9]+)?)/i', $line, $match)) {
                $mValues[] = (float) str_replace(',', '.', $match[1]);
                continue;
            }

            if ($line === '*' || preg_match('/^\*+$/', $line)) {
                $markers[] = $line;
                continue;
            }

            if (
                str_contains($line, '%') ||
                preg_match('/\bH\d{3}[A-Za-z]*\b/', $line)
            ) {
                $sclLines[] = $line;
                continue;
            }

            $markers[] = $line;
        }

        $classes = $hazardClassifications ?? [];
        $hasAcute = count(array_filter(
            $classes,
            fn ($item) => str_contains(
                strtolower($item),
                'aquatic acute 1'
            )
        )) > 0;

        $hasChronic = count(array_filter(
            $classes,
            fn ($item) => str_contains(
                strtolower($item),
                'aquatic chronic 1'
            )
        )) > 0;

        $mAcute = null;
        $mChronic = null;
        $mAmbiguous = false;

        if (count($mValues) >= 2 && $hasAcute && $hasChronic) {
            $mAcute = $mValues[0];
            $mChronic = $mValues[1];
        } elseif (count($mValues) === 1 && $hasAcute && !$hasChronic) {
            $mAcute = $mValues[0];
        } elseif (count($mValues) === 1 && !$hasAcute && $hasChronic) {
            $mChronic = $mValues[0];
        } elseif (count($mValues) > 0) {
            $mAmbiguous = true;
        }

        $scl = null;

        if (count($sclLines) || count($markers)) {
            $scl = [
                'raw_lines' => $sclLines,
                'markers' => $markers,
            ];
        }

        return [
            'specific_concentration_limits' => $scl,
            'ate_data' => count($ateRows) ? $ateRows : null,
            'm_factor_acute' => $mAcute,
            'm_factor_chronic' => $mChronic,
            'm_factor_values' => $mValues,
            'm_factor_ambiguous' => $mAmbiguous,
        ];
    }

    private function echaDateCellToYmd(Cell $cell): ?string
    {
        $value = $cell->getValue();

        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (is_numeric($value) && ExcelDate::isDateTime($cell)) {
            return ExcelDate::excelToDateTimeObject($value)
                ->format('Y-m-d');
        }

        try {
            return Carbon::parse((string) $value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function hasTrustedSourceIdentity(array $row): bool
    {
        $sourceType = trim((string) ($row['source_type'] ?? ''));
        $reference = trim((string) ($row['source_reference'] ?? ''));
        $uuid = trim((string) ($row['echa_uuid'] ?? ''));
        return ($sourceType !== '' && $sourceType !== 'regulatory_import' && $reference !== '')
            || ($sourceType === 'ECHA_REACH_REGISTERED' && $uuid !== '');
    }

    private function identifierWarnings(array $row): array
    {
        $warnings = [];
        if (!$this->cleanIdentifier($row['cas_no'] ?? null)) {
            $warnings[] = 'CAS numarası yok.';
        }
        if (!$this->cleanIdentifier($row['ec_no'] ?? null)) {
            $warnings[] = 'EC numarası yok.';
        }
        if (!$this->cleanIdentifier($row['cas_no'] ?? null) &&
            !$this->cleanIdentifier($row['ec_no'] ?? null)) {
            $warnings[] = $this->hasTrustedSourceIdentity($row)
                ? 'Kaynak kimliği ile aktarılacak; otomatik CAS eşleştirmesi yapılmayacak.'
                : 'Kimlik yetersiz; aktarım dışı.';
        }
        return $warnings;
    }

    private function determinePreviewStatus(
        array $row,
        string $regulationProfile,
        string $sourceName
    ): array {
        $errors = [];
        $differences = [];

        $sourceType = $row['source_type'] ?? null;

        if ($sourceType === 'ECHA_REACH_REGISTERED') {
            return $this->determineReachRegisteredPreviewStatus(
                $row,
                $regulationProfile,
                $sourceName
            );
        }

        if (trim((string) ($row['preferred_name'] ?? '')) === '') {
            $errors[] = 'Kimyasal adı eksik.';
        }

        $hasIdentifier =
            !empty($row['cas_no']) ||
            !empty($row['ec_no']) ||
            !empty($row['index_no']);

        if (!$hasIdentifier && !$this->hasTrustedSourceIdentity($row)) {
            $errors[] = 'CAS/EC/Index yok ve güvenilir kaynak kimliği bulunamadı.';
        }

        if (count($errors)) {
            return [
                'status' => 'invalid',
                'differences' => [],
                'errors' => $errors,
            ];
        }

        $substance = $this->findSubstanceForRow($row);

        if (!$substance) {
            return [
                'status' => 'new',
                'differences' => [],
                'errors' => [],
            ];
        }

        if (
            !in_array(
                $sourceType,
                [
                    'ECHA_SVHC',
                    'ECHA_ANNEX_XIV',
                    'ECHA_ANNEX_XVII',
                    'ECHA_REACH_REGISTERED',
                ],
                true
            ) &&
            trim((string) $substance->preferred_name)
            !== trim((string) ($row['preferred_name'] ?? ''))
        ) {
            $differences[] = 'Kimyasal adı değişmiş.';
        }

        if (
            !empty($row['index_no']) &&
            trim((string) $substance->index_no)
            !== trim((string) $row['index_no'])
        ) {
            $differences[] = 'Index No değişmiş.';
        }

        $classification = $this->findExactClassification(
            $substance,
            $row,
            $regulationProfile,
            $sourceName
        );

        if (!$classification) {
            $differences[] = 'Bu kaynak/sürüm için sınıflandırma kaydı bulunmuyor.';
        } else {
            $differences = array_merge(
                $differences,
                $this->classificationDifferences(
                    $classification,
                    $row
                )
            );
        }

        return [
            'status' => count($differences) ? 'changed' : 'same',
            'differences' => $differences,
            'errors' => [],
        ];
    }

    private function determineReachRegisteredPreviewStatus(
        array $row,
        string $regulationProfile,
        string $sourceName
    ): array {
        $errors = [];
        $differences = [];
        $changeTypes = [];

        if (trim((string) ($row['preferred_name'] ?? '')) === '') {
            $errors[] = 'Kimyasal adı eksik.';
        }

        $hasIdentifier =
            !empty($row['cas_no']) ||
            !empty($row['ec_no']) ||
            !empty($row['echa_uuid']) ||
            !empty($row['source_reference']);

        if (!$hasIdentifier) {
            $errors[] = 'CAS, EC veya ECHA kimliğinden en az biri gerekli.';
        }

        if (count($errors)) {
            return [
                'status' => 'invalid',
                'differences' => [],
                'errors' => $errors,
                'change_types' => [],
            ];
        }

        $substance = $this->findSubstanceForRow($row);

        if (!$substance) {
            return [
                'status' => 'new',
                'differences' => [],
                'errors' => [],
                'change_types' => [],
            ];
        }

        $incomingName = trim((string) ($row['preferred_name'] ?? ''));
        $currentName = trim((string) $substance->preferred_name);

        if ($incomingName !== '' && $currentName !== $incomingName) {
            $differences[] = 'ECHA REACH kayıtlı madde adı farklı.';
            $changeTypes[] = 'name_changed';
        }

        $incomingEc = $this->cleanIdentifier($row['ec_no'] ?? null);
        $currentEc = $this->cleanIdentifier($substance->ec_no);

        if ($incomingEc && $currentEc && $incomingEc !== $currentEc) {
            $differences[] = 'EC No farklı: mevcut ' . $currentEc . ' / ECHA ' . $incomingEc . '.';
            $changeTypes[] = 'ec_changed';
        }

        $incomingCas = $this->cleanIdentifier($row['cas_no'] ?? null);
        $currentCas = $this->cleanIdentifier($substance->cas_no);

        if ($incomingCas && $currentCas && $incomingCas !== $currentCas) {
            $differences[] = 'CAS No çakışması: mevcut ' . $currentCas . ' / ECHA ' . $incomingCas . '. Otomatik değiştirilmez.';
            $changeTypes[] = 'cas_conflict';
        }

        $incomingUuid = trim((string) (
            $row['echa_uuid']
            ?? $row['source_reference']
            ?? ''
        ));
        $currentUuid = trim((string) $substance->echa_uuid);
        $incomingUrl = trim((string) ($row['source_url'] ?? ''));
        $currentUrl = trim((string) $substance->echa_substance_url);

        if (
            ($incomingUuid !== '' && $currentUuid !== $incomingUuid) ||
            ($incomingUrl !== '' && $currentUrl !== $incomingUrl)
        ) {
            $differences[] = 'ECHA CHEM kimliği veya overview bağlantısı güncellenecek.';
            $changeTypes[] = 'echa_identity_changed';
        }

        $classification = $this->findExactClassification(
            $substance,
            $row,
            $regulationProfile,
            $sourceName
        );

        if (!$classification) {
            $differences[] = 'Madde mevcut; yalnızca REACH Registered kaynak kaydı henüz yok.';
            $changeTypes[] = 'source_record_missing';
        } else {
            $classificationDiffs = $this->classificationDifferences(
                $classification,
                $row
            );

            if (count($classificationDiffs)) {
                $differences = array_merge($differences, $classificationDiffs);
            }
        }

        $changeTypes = array_values(array_unique($changeTypes));

        return [
            'status' => count($differences) ? 'changed' : 'same',
            'differences' => $differences,
            'errors' => [],
            'change_types' => $changeTypes,
        ];
    }

    private function findSubstanceForRow(array $row): ?RegulatorySubstance
    {
        $sourceType = $row['source_type'] ?? null;
        $indexNo = $this->cleanIdentifier($row['index_no'] ?? null);
        $casNo = $this->cleanIdentifier($row['cas_no'] ?? null);
        $ecNo = $this->cleanIdentifier($row['ec_no'] ?? null);

        if ($sourceType === 'ECHA_REACH_REGISTERED') {
            $echaUuid = trim((string) (
                $row['echa_uuid']
                ?? $row['source_reference']
                ?? ''
            ));

            if ($echaUuid !== '') {
                $substance = RegulatorySubstance::where(
                    'echa_uuid',
                    $echaUuid
                )->first();

                if ($substance) {
                    return $substance;
                }
            }
        }

        if ($indexNo) {
            $substance = RegulatorySubstance::where(
                'index_no',
                $indexNo
            )->first();

            if ($substance) {
                return $substance;
            }
        }

        if ($casNo && $ecNo) {
            $substance = RegulatorySubstance::where(
                'cas_no',
                $casNo
            )
                ->where('ec_no', $ecNo)
                ->first();

            if ($substance) {
                return $substance;
            }
        }

        if ($ecNo) {
            $query = RegulatorySubstance::where(
                'ec_no',
                $ecNo
            );

            if ($indexNo) {
                $query->where(function ($q) use ($indexNo) {
                    $q->whereNull('index_no')
                        ->orWhere('index_no', $indexNo);
                });
            }

            $substance = $query->first();

            if ($substance) {
                return $substance;
            }
        }

        if ($casNo) {
            $substance = RegulatorySubstance::where(
                'cas_no',
                $casNo
            )->first();

            if ($substance) {
                if (
                    !$indexNo ||
                    !$substance->index_no ||
                    $substance->index_no === $indexNo
                ) {
                    return $substance;
                }
            }
        }

        // Missing identifiers: match only by exact source type, source reference AND name.
        // Never merge distinct CAS-less substance groups by name alone.
        if (!$indexNo && !$casNo && !$ecNo && $this->hasTrustedSourceIdentity($row)) {
            $ref = trim((string) ($row['source_reference'] ?? ''));
            $name = trim((string) ($row['preferred_name'] ?? ''));
            $existingId = DB::table('regulatory_classifications as rc')
                ->join('regulatory_substances as rs', 'rs.id', '=', 'rc.regulatory_substance_id')
                ->where('rc.source_type', $sourceType)
                ->where('rc.source_reference', $ref)
                ->where('rs.preferred_name', $name)
                ->orderByDesc('rc.id')
                ->value('rs.id');
            if ($existingId) {
                return RegulatorySubstance::find($existingId);
            }
        }

        return null;
    }

    private function createSubstanceFromRow(array $row): RegulatorySubstance
    {
        $sourceType = $row['source_type'] ?? null;

        $casNo = $this->safeUniqueCas(
            $row['cas_no'] ?? null,
            null
        );

        $createdFrom = match ($sourceType) {
            'ECHA_ANNEX_VI' => 'ECHA_ANNEX_VI',
            'ECHA_SVHC' => 'ECHA_SVHC',
            'ECHA_ANNEX_XIV' => 'ECHA_ANNEX_XIV',
            'ECHA_ANNEX_XVII' => 'ECHA_ANNEX_XVII',
            'ECHA_REACH_REGISTERED' => 'ECHA_REACH_REGISTERED',
            default => 'REGULATORY_IMPORT',
        };

        $isEchaSource = in_array(
            $sourceType,
            [
                'ECHA_ANNEX_VI',
                'ECHA_SVHC',
                'ECHA_ANNEX_XIV',
                'ECHA_ANNEX_XVII',
                'ECHA_REACH_REGISTERED',
            ],
            true
        );

        return RegulatorySubstance::create([
            'preferred_name' => $row['preferred_name'] ?? null,
            'cas_no' => $casNo,
            'ec_no' => $row['ec_no'] ?? null,
            'index_no' => $row['index_no'] ?? null,
            'echa_uuid' => $row['echa_uuid'] ?? null,
            'echa_substance_url' => $row['source_url'] ?? null,
            'created_from' => $createdFrom,
            'verification_status' => 'review_required',
            'last_echa_check_at' => $isEchaSource
                ? now()
                : null,
            'is_active' => true,
        ]);
    }

    private function updateSubstanceFromRow(
        RegulatorySubstance $substance,
        array $row
    ): bool {
        $sourceType = $row['source_type'] ?? null;

        if ($sourceType === 'ECHA_REACH_REGISTERED') {
            return $this->updateSubstanceFromReachRegistered(
                $substance,
                $row
            );
        }

        $hasCurrentReachIdentity = RegulatoryClassification::where(
            'regulatory_substance_id',
            $substance->id
        )
            ->where('source_type', 'ECHA_REACH_REGISTERED')
            ->where('is_current', true)
            ->exists();

        $preferredName = $row['preferred_name']
            ?? $substance->preferred_name;

        if (
            $hasCurrentReachIdentity ||
            in_array(
                $sourceType,
                [
                    'ECHA_SVHC',
                    'ECHA_ANNEX_XIV',
                    'ECHA_ANNEX_XVII',
                ],
                true
            )
        ) {
            if (trim((string) $substance->preferred_name) !== '') {
                $preferredName = $substance->preferred_name;
            }
        }

        $contentPayload = [
            'preferred_name' => $preferredName,
            'index_no' => $row['index_no'] ?: $substance->index_no,
            'verification_status' => $substance->verification_status,
            'is_active' => true,
        ];

        if (!$hasCurrentReachIdentity) {
            $contentPayload['ec_no'] = $row['ec_no'] ?: $substance->ec_no;

            $safeCas = $this->safeUniqueCas(
                $row['cas_no'] ?? null,
                $substance->id
            );

            if ($safeCas) {
                $contentPayload['cas_no'] = $safeCas;
            }
        }

        $dirty = false;

        foreach ($contentPayload as $key => $value) {
            if ((string) $substance->{$key} !== (string) $value) {
                $dirty = true;
                break;
            }
        }

        $payload = $contentPayload;

        if (
            in_array(
                $sourceType,
                [
                    'ECHA_ANNEX_VI',
                    'ECHA_SVHC',
                    'ECHA_ANNEX_XIV',
                    'ECHA_ANNEX_XVII',
                ],
                true
            )
        ) {
            $payload['last_echa_check_at'] = now();
        }

        $substance->update($payload);

        return $dirty;
    }

    private function updateSubstanceFromReachRegistered(
        RegulatorySubstance $substance,
        array $row
    ): bool {
        $payload = [
            'preferred_name' => trim((string) ($row['preferred_name'] ?? ''))
                ?: $substance->preferred_name,
            'echa_uuid' => trim((string) ($row['echa_uuid'] ?? ''))
                ?: $substance->echa_uuid,
            'echa_substance_url' => trim((string) ($row['source_url'] ?? ''))
                ?: $substance->echa_substance_url,
            'verification_status' => $substance->verification_status,
            'last_echa_check_at' => now(),
            'is_active' => true,
        ];

        $incomingEc = $this->cleanIdentifier($row['ec_no'] ?? null);
        $currentEc = $this->cleanIdentifier($substance->ec_no);

        if (!$currentEc && $incomingEc) {
            $payload['ec_no'] = $incomingEc;
        }

        $incomingCas = $this->cleanIdentifier($row['cas_no'] ?? null);
        $currentCas = $this->cleanIdentifier($substance->cas_no);

        if (!$currentCas && $incomingCas) {
            $safeCas = $this->safeUniqueCas(
                $incomingCas,
                $substance->id
            );

            if ($safeCas) {
                $payload['cas_no'] = $safeCas;
            }
        }

        $dirty = false;

        foreach ($payload as $key => $value) {
            if ($key === 'last_echa_check_at') {
                continue;
            }

            if ((string) $substance->{$key} !== (string) $value) {
                $dirty = true;
                break;
            }
        }

        $substance->update($payload);

        return $dirty;
    }

    private function safeUniqueCas(
        ?string $casNo,
        ?int $ignoreId
    ): ?string {
        $casNo = $this->cleanIdentifier($casNo);

        if (!$casNo) {
            return null;
        }

        $query = RegulatorySubstance::where('cas_no', $casNo);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists()
            ? null
            : $casNo;
    }

    private function linkEchaSdsComponents(
        RegulatorySubstance $substance,
        array $row,
        array $identifierCounts
    ): int {
        $linked = 0;

        if (!empty($row['index_no'])) {
            $linked += RawMaterialSdsComponent::whereNull(
                'regulatory_substance_id'
            )
                ->where('index_no', $row['index_no'])
                ->update([
                    'regulatory_substance_id' => $substance->id,
                    'is_verified' => false,
                ]);
        }

        foreach ($row['all_cas_numbers'] ?? [] as $cas) {
            if (($identifierCounts['cas'][$cas] ?? 0) !== 1) {
                continue;
            }

            $linked += RawMaterialSdsComponent::whereNull(
                'regulatory_substance_id'
            )
                ->where('cas_no', $cas)
                ->update([
                    'regulatory_substance_id' => $substance->id,
                    'is_verified' => false,
                ]);
        }

        foreach ($row['all_ec_numbers'] ?? [] as $ec) {
            if (($identifierCounts['ec'][$ec] ?? 0) !== 1) {
                continue;
            }

            $linked += RawMaterialSdsComponent::whereNull(
                'regulatory_substance_id'
            )
                ->where('ec_no', $ec)
                ->update([
                    'regulatory_substance_id' => $substance->id,
                    'is_verified' => false,
                ]);
        }

        return $linked;
    }

    private function linkGenericSdsComponents(
        RegulatorySubstance $substance,
        array $row
    ): int {
        $linked = 0;

        if (!empty($row['index_no'])) {
            $linked += RawMaterialSdsComponent::whereNull(
                'regulatory_substance_id'
            )
                ->where('index_no', $row['index_no'])
                ->update([
                    'regulatory_substance_id' => $substance->id,
                    'is_verified' => false,
                ]);
        }

        if (!empty($row['cas_no'])) {
            $linked += RawMaterialSdsComponent::whereNull(
                'regulatory_substance_id'
            )
                ->where('cas_no', $row['cas_no'])
                ->update([
                    'regulatory_substance_id' => $substance->id,
                    'is_verified' => false,
                ]);
        }

        if (!empty($row['ec_no'])) {
            $linked += RawMaterialSdsComponent::whereNull(
                'regulatory_substance_id'
            )
                ->where('ec_no', $row['ec_no'])
                ->update([
                    'regulatory_substance_id' => $substance->id,
                    'is_verified' => false,
                ]);
        }

        return $linked;
    }

    private function findExactClassification(
        RegulatorySubstance $substance,
        array $row,
        string $regulationProfile,
        string $sourceName
    ): ?RegulatoryClassification {
        $query = RegulatoryClassification::where(
            'regulatory_substance_id',
            $substance->id
        )
            ->where('regulation_profile', $regulationProfile);

        $sourceType = $row['source_type'] ?? null;

        if ($sourceType === 'ECHA_ANNEX_VI') {
            $query->where('source_type', 'ECHA_ANNEX_VI')
                ->where(
                    'source_reference',
                    $row['source_reference'] ?? null
                )
                ->where(
                    'source_version',
                    $row['source_version'] ?? null
                );
        } elseif ($sourceType === 'ECHA_SVHC') {
            $query->where('source_type', 'ECHA_SVHC');

            if (!empty($row['source_reference'])) {
                $query->where(
                    'source_reference',
                    $row['source_reference']
                );
            }

            if (!empty($row['effective_from'])) {
                $query->whereDate(
                    'effective_from',
                    $row['effective_from']
                );
            }
        } elseif ($sourceType === 'ECHA_ANNEX_XIV') {
            $query->where(
                'source_type',
                'ECHA_ANNEX_XIV'
            );

            if (!empty($row['annex_xiv_entry'])) {
                $query->where(
                    'annex_xiv_entry',
                    $row['annex_xiv_entry']
                );
            }

            if (!empty($row['effective_from'])) {
                $query->whereDate(
                    'effective_from',
                    $row['effective_from']
                );
            }
        } elseif ($sourceType === 'ECHA_ANNEX_XVII') {
            $query->where(
                'source_type',
                'ECHA_ANNEX_XVII'
            );

            if (!empty($row['annex_xvii_entry'])) {
                $query->where(
                    'annex_xvii_entry',
                    $row['annex_xvii_entry']
                );
            }
        } elseif ($sourceType === 'ECHA_REACH_REGISTERED') {
            $query->where(
                'source_type',
                'ECHA_REACH_REGISTERED'
            );

            if (!empty($row['source_reference'])) {
                $query->where(
                    'source_reference',
                    $row['source_reference']
                );
            }
        } else {
            $query->where('source_name', $sourceName);

            if (!empty($row['source_version'])) {
                $query->where(
                    'source_version',
                    $row['source_version']
                );
            }
        }

        return $query->orderByDesc('id')->first();
    }

    private function classificationEquivalent(
        RegulatoryClassification $classification,
        array $row
    ): bool {
        return count(
            $this->classificationDifferences(
                $classification,
                $row
            )
        ) === 0;
    }

    private function classificationDifferences(
        RegulatoryClassification $classification,
        array $row
    ): array {
        $differences = [];

        if (
            $this->normalizeArray($classification->hazard_classifications)
            !==
            $this->normalizeArray($row['hazard_classifications'] ?? null)
        ) {
            $differences[] = 'Tehlike sınıflandırması değişmiş.';
        }

        if (
            trim((string) $classification->signal_word)
            !== trim((string) ($row['signal_word'] ?? ''))
        ) {
            $differences[] = 'Signal Word değişmiş.';
        }

        if (
            $this->normalizeArray($classification->pictograms)
            !==
            $this->normalizeArray($row['pictograms'] ?? null)
        ) {
            $differences[] = 'Piktogramlar değişmiş.';
        }

        if (
            $this->normalizeArray($classification->hazard_statements)
            !==
            $this->normalizeArray($row['hazard_statements'] ?? null)
        ) {
            $differences[] = 'H kodları değişmiş.';
        }

        if (
            $this->normalizeArray(
                $classification->supplemental_hazard_statements
            )
            !==
            $this->normalizeArray(
                $row['supplemental_hazard_statements'] ?? null
            )
        ) {
            $differences[] = 'EUH kodları değişmiş.';
        }

        if (
            $this->normalizeJson(
                $classification->specific_concentration_limits
            )
            !==
            $this->normalizeJson(
                $row['specific_concentration_limits'] ?? null
            )
        ) {
            $differences[] = 'SCL verisi değişmiş.';
        }

        if (
            $this->normalizeJson($classification->ate_data)
            !==
            $this->normalizeJson($row['ate_data'] ?? null)
        ) {
            $differences[] = 'ATE verisi değişmiş.';
        }

        if (
            $this->normalizeNumber($classification->m_factor_acute)
            !==
            $this->normalizeNumber($row['m_factor_acute'] ?? null)
        ) {
            $differences[] = 'Akut M-Factor değişmiş.';
        }

        if (
            $this->normalizeNumber($classification->m_factor_chronic)
            !==
            $this->normalizeNumber($row['m_factor_chronic'] ?? null)
        ) {
            $differences[] = 'Kronik M-Factor değişmiş.';
        }

        foreach (
            [
                'reach_registered',
                'svhc',
                'annex_vi',
                'annex_xiv',
                'annex_xvii',
            ]
            as $field
        ) {
            if (
                $this->normalizeBoolean($classification->{$field})
                !==
                $this->normalizeBoolean($row[$field] ?? null)
            ) {
                $differences[] = $field . ' durumu değişmiş.';
            }
        }

        foreach (
            [
                'annex_vi_entry',
                'annex_xiv_entry',
                'annex_xvii_entry',
            ]
            as $field
        ) {
            if (
                trim((string) $classification->{$field})
                !== trim((string) ($row[$field] ?? ''))
            ) {
                $differences[] = $field . ' değişmiş.';
            }
        }

        if (
            $this->normalizeDateValue($classification->effective_from)
            !== $this->normalizeDateValue($row['effective_from'] ?? null)
        ) {
            $differences[] = 'Yürürlük tarihi değişmiş.';
        }

        if (($row['source_type'] ?? null) === 'ECHA_ANNEX_VI') {
            $existingNotes = $this->semanticEchaNotes(
                $classification->regulatory_notes
            );

            $incomingNotes = $this->semanticEchaNotes(
                $row['regulatory_notes'] ?? null
            );

            if (
                $this->normalizeJson($existingNotes)
                !==
                $this->normalizeJson($incomingNotes)
            ) {
                $differences[] = 'ECHA kayıt notları veya kimlik bilgileri değişmiş.';
            }
        }

        if (($row['source_type'] ?? null) === 'ECHA_ANNEX_XIV') {
            $existingNotes = $this->semanticEchaAnnexXivNotes(
                $classification->regulatory_notes
            );

            $incomingNotes = $this->semanticEchaAnnexXivNotes(
                $row['regulatory_notes'] ?? null
            );

            if (
                $this->normalizeJson($existingNotes)
                !==
                $this->normalizeJson($incomingNotes)
            ) {
                $differences[] = 'Annex XIV kayıt detayları değişmiş.';
            }
        }

        if (($row['source_type'] ?? null) === 'ECHA_ANNEX_XVII') {
            $existingNotes = $this->semanticEchaAnnexXviiNotes(
                $classification->regulatory_notes
            );

            $incomingNotes = $this->semanticEchaAnnexXviiNotes(
                $row['regulatory_notes'] ?? null
            );

            if (
                $this->normalizeJson($existingNotes)
                !==
                $this->normalizeJson($incomingNotes)
            ) {
                $differences[] = 'Annex XVII kayıt detayları değişmiş.';
            }
        }

        if (($row['source_type'] ?? null) === 'ECHA_SVHC') {
            $existingNotes = $this->semanticSvhcNotes(
                $classification->regulatory_notes
            );

            $incomingNotes = $this->semanticSvhcNotes(
                $row['regulatory_notes'] ?? null
            );

            if (
                $this->normalizeJson($existingNotes)
                !==
                $this->normalizeJson($incomingNotes)
            ) {
                $differences[] =
                    'SVHC Candidate List kayıt bilgileri değişmiş.';
            }
        }

        if (($row['source_type'] ?? null) === 'ECHA_REACH_REGISTERED') {
            $existingNotes = $this->semanticReachRegisteredNotes(
                $classification->regulatory_notes
            );

            $incomingNotes = $this->semanticReachRegisteredNotes(
                $row['regulatory_notes'] ?? null
            );

            if (
                $this->normalizeJson($existingNotes)
                !==
                $this->normalizeJson($incomingNotes)
            ) {
                $differences[] = 'REACH Registered kaynak kimlik bilgileri değişmiş.';
            }
        }

        return $differences;
    }

    private function semanticReachRegisteredNotes($notes): array
    {
        if (!is_array($notes)) {
            return [];
        }

        $keys = [
            'registered_name',
            'cas_no',
            'ec_no',
            'echa_uuid',
            'overview_url',
        ];

        $result = [];

        foreach ($keys as $key) {
            if (array_key_exists($key, $notes)) {
                $result[$key] = $notes[$key];
            }
        }

        return $result;
    }

    private function semanticEchaAnnexXviiNotes($notes): array
    {
        if (!is_array($notes)) {
            return [];
        }

        $keys = [
            'entry_no',
            'description',
            'conditions_raw',
            'condition_urls',
            'appendices_raw',
            'appendix_urls',
            'standards_raw',
            'standard_urls',
            'history_raw',
            'history_urls',
            'qas_raw',
            'qa_urls',
            'remarks',
            'first_published',
            'all_cas_numbers',
            'all_ec_numbers',
        ];

        $result = [];

        foreach ($keys as $key) {
            if (array_key_exists($key, $notes)) {
                $result[$key] = $notes[$key];
            }
        }

        return $result;
    }

    private function semanticEchaAnnexXivNotes($notes): array
    {
        if (!is_array($notes)) {
            return [];
        }

        $keys = [
            'entry_no',
            'description',
            'latest_application_date',
            'latest_application_date_text',
            'sunset_date',
            'sunset_date_text',
            'intrinsic_property',
            'exempted_uses',
            'review_periods',
            'date_of_inclusion',
            'regulation_amending_annex_xiv',
            'regulation_urls',
            'remarks',
            'first_published',
            'all_cas_numbers',
            'all_ec_numbers',
        ];

        $result = [];

        foreach ($keys as $key) {
            if (array_key_exists($key, $notes)) {
                $result[$key] = $notes[$key];
            }
        }

        return $result;
    }

    private function semanticEchaNotes($notes): array
    {
        if (!is_array($notes)) {
            return [];
        }

        $keys = [
            'celex',
            'all_cas_numbers',
            'all_ec_numbers',
            'labelling_hazard_statements',
            'raw_m_scl_ate',
            'm_factor_values',
            'm_factor_ambiguous',
            'notes',
            'comment',
            'in_application',
            'eur_lex_link',
        ];

        $result = [];

        foreach ($keys as $key) {
            if (array_key_exists($key, $notes)) {
                $result[$key] = $notes[$key];
            }
        }

        return $result;
    }

    private function semanticSvhcNotes($notes): array
    {
        if (!is_array($notes)) {
            return [];
        }

        $keys = [
            'source_substance_names',
            'descriptions',
            'reason_for_inclusion',
            'decision_urls',
            'iuclid_dataset_urls',
            'support_document_urls',
            'response_to_comments_urls',
            'remarks',
            'date_of_inclusion',
        ];

        $result = [];

        foreach ($keys as $key) {
            if (array_key_exists($key, $notes)) {
                $result[$key] = $notes[$key];
            }
        }

        return $result;
    }

    private function parseGenericDelimitedFile(
        string $absolutePath,
        array $context
    ): array {
        $lines = file(
            $absolutePath,
            FILE_IGNORE_NEW_LINES
        );

        if (!$lines) {
            throw new \RuntimeException('Dosya boş veya okunamadı.');
        }

        $firstNonEmpty = null;
        $firstIndex = null;

        foreach ($lines as $index => $line) {
            $candidate = trim($this->cleanCsvBom($line));

            if ($candidate !== '') {
                $firstNonEmpty = $candidate;
                $firstIndex = $index;
                break;
            }
        }

        if ($firstNonEmpty === null) {
            throw new \RuntimeException('Dosya boş.');
        }

        $delimiter = null;
        $startIndex = $firstIndex;

        if (preg_match('/^sep=(.)$/i', $firstNonEmpty, $match)) {
            $delimiter = $match[1];
            $startIndex = $firstIndex + 1;
        } else {
            $counts = [
                ';' => substr_count($firstNonEmpty, ';'),
                ',' => substr_count($firstNonEmpty, ','),
                "\t" => substr_count($firstNonEmpty, "\t"),
            ];

            arsort($counts);
            $delimiter = array_key_first($counts);
        }

        if (!$delimiter) {
            throw new \RuntimeException(
                'CSV ayırıcı karakteri bulunamadı.'
            );
        }

        $matrix = [];

        for ($i = $startIndex; $i < count($lines); $i++) {
            if (trim($lines[$i]) === '') {
                continue;
            }

            $matrix[] = $this->parseCsvLine(
                $lines[$i],
                $delimiter
            );
        }

        return $this->parseGenericMatrix(
            $matrix,
            $context
        );
    }

    private function parseGenericSpreadsheet(
        $spreadsheet,
        array $context
    ): array {
        $bestMatrix = [];
        $bestScore = -1;

        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            $matrix = $worksheet->toArray(
                null,
                true,
                true,
                false
            );

            $header = $this->findGenericHeaderRow($matrix);

            if ($header && $header['score'] > $bestScore) {
                $bestScore = $header['score'];
                $bestMatrix = $matrix;
            }
        }

        if (!count($bestMatrix)) {
            throw new \RuntimeException(
                'Excel dosyasında uygun mevzuat tablosu bulunamadı.'
            );
        }

        return $this->parseGenericMatrix(
            $bestMatrix,
            $context
        );
    }

    private function parseGenericMatrix(
        array $matrix,
        array $context
    ): array {
        $headerResult = $this->findGenericHeaderRow($matrix);

        if (!$headerResult) {
            throw new \RuntimeException(
                'Mevzuat başlık satırı bulunamadı.'
            );
        }

        $headers = $headerResult['headers'];
        $headerIndex = $headerResult['index'];
        $rows = [];
        $summary = [
            'new' => 0,
            'changed' => 0,
            'same' => 0,
            'invalid' => 0,
            'total' => 0,
        ];

        for ($i = $headerIndex + 1; $i < count($matrix); $i++) {
            $raw = $matrix[$i];

            if ($this->rowIsEmpty($raw)) {
                continue;
            }

            $data = [];

            foreach ($headers as $column => $canonical) {
                if (!$canonical) {
                    continue;
                }

                $data[$canonical] = trim((string) ($raw[$column] ?? ''));
            }

            $row = [
                'row_number' => $i + 1,
                'preferred_name' => trim((string) ($data['preferred_name'] ?? '')),
                'cas_no' => $this->cleanIdentifier($data['cas_no'] ?? null),
                'ec_no' => $this->cleanIdentifier($data['ec_no'] ?? null),
                'index_no' => $this->cleanIdentifier($data['index_no'] ?? null),
                'all_cas_numbers' => array_values(array_filter([
                    $this->cleanIdentifier($data['cas_no'] ?? null),
                ])),
                'all_ec_numbers' => array_values(array_filter([
                    $this->cleanIdentifier($data['ec_no'] ?? null),
                ])),
                'hazard_classifications' => $this->pipeToArray(
                    $data['hazard_classifications'] ?? null
                ),
                'signal_word' => trim((string) ($data['signal_word'] ?? '')) ?: null,
                'pictograms' => $this->pipeToArray(
                    $data['pictograms'] ?? null
                ),
                'hazard_statements' => $this->pipeToArray(
                    $data['hazard_statements'] ?? null
                ),
                'supplemental_hazard_statements' => $this->pipeToArray(
                    $data['supplemental_hazard_statements'] ?? null
                ),
                'specific_concentration_limits' => $this->jsonToArray(
                    $data['specific_concentration_limits'] ?? null
                ),
                'ate_data' => $this->jsonToArray(
                    $data['ate_data'] ?? null
                ),
                'm_factor_acute' => $this->nullableNumber(
                    $data['m_factor_acute'] ?? null
                ),
                'm_factor_chronic' => $this->nullableNumber(
                    $data['m_factor_chronic'] ?? null
                ),
                'reach_registered' => $this->toNullableBoolean(
                    $data['reach_registered'] ?? null
                ),
                'svhc' => $this->toNullableBoolean(
                    $data['svhc'] ?? null
                ),
                'annex_vi' => $this->toNullableBoolean(
                    $data['annex_vi'] ?? null
                ),
                'annex_xiv' => $this->toNullableBoolean(
                    $data['annex_xiv'] ?? null
                ),
                'annex_xvii' => $this->toNullableBoolean(
                    $data['annex_xvii'] ?? null
                ),
                'annex_vi_entry' => trim((string) ($data['annex_vi_entry'] ?? '')) ?: null,
                'annex_xiv_entry' => trim((string) ($data['annex_xiv_entry'] ?? '')) ?: null,
                'annex_xvii_entry' => trim((string) ($data['annex_xvii_entry'] ?? '')) ?: null,
                'source_type' => 'regulatory_import',
                'source_version' => $context['source_version'] ?? null,
                'source_reference' => null,
                'source_url' => null,
                'effective_from' => null,
                'effective_until' => null,
                'legal_status' => 'in_force',
                'is_current' => true,
                'regulatory_notes' => [],
            ];

            $preview = $this->determinePreviewStatus(
                $row,
                $context['regulation_profile'],
                $context['source_name']
            );

            $row['status'] = $preview['status'];
            $row['differences'] = $preview['differences'];
            $row['errors'] = $preview['errors'];
            $row['warnings'] = $this->identifierWarnings($row);

            $rows[] = $row;
            $summary['total']++;
            $summary[$row['status']]++;
        }

        return [
            'rows' => $rows,
            'summary' => $summary,
            'identifier_counts' => [],
            'meta' => [
                'parser_type' => 'generic',
                'regulation_profile' => $context['regulation_profile'],
                'source_name' => $context['source_name'],
                'dataset_version' => $context['source_version'] ?? null,
            ],
        ];
    }

    private function findGenericHeaderRow(array $matrix): ?array
    {
        $best = null;

        foreach (array_slice($matrix, 0, 80, true) as $index => $row) {
            $headers = [];
            $score = 0;

            foreach ($row as $column => $cell) {
                $canonical = $this->canonicalHeader($cell);
                $headers[$column] = $canonical;

                if ($canonical) {
                    $score++;
                }
            }

            $hasName = in_array('preferred_name', $headers, true);
            $hasIdentity = in_array('cas_no', $headers, true)
                || in_array('ec_no', $headers, true)
                || in_array('index_no', $headers, true);

            if ($hasName && $hasIdentity && $score >= 2) {
                if (!$best || $score > $best['score']) {
                    $best = [
                        'index' => $index,
                        'headers' => $headers,
                        'score' => $score,
                    ];
                }
            }
        }

        return $best;
    }

    private function canonicalHeader($header): ?string
    {
        $header = trim((string) $header);

        if ($header === '') {
            return null;
        }

        $slug = strtolower(Str::ascii($header));
        $slug = preg_replace('/[^a-z0-9]+/', '_', $slug);
        $slug = trim(preg_replace('/_+/', '_', $slug), '_');

        $map = [
            'preferred_name' => 'preferred_name',
            'chemical_name' => 'preferred_name',
            'substance_name' => 'preferred_name',
            'cas_no' => 'cas_no',
            'cas_number' => 'cas_no',
            'ec_no' => 'ec_no',
            'ec_number' => 'ec_no',
            'index_no' => 'index_no',
            'index_number' => 'index_no',
            'hazard_classifications' => 'hazard_classifications',
            'signal_word' => 'signal_word',
            'pictograms' => 'pictograms',
            'hazard_statements' => 'hazard_statements',
            'supplemental_hazard_statements' => 'supplemental_hazard_statements',
            'specific_concentration_limits' => 'specific_concentration_limits',
            'ate_data' => 'ate_data',
            'm_factor_acute' => 'm_factor_acute',
            'm_factor_chronic' => 'm_factor_chronic',
            'reach_registered' => 'reach_registered',
            'svhc' => 'svhc',
            'annex_vi' => 'annex_vi',
            'annex_xiv' => 'annex_xiv',
            'annex_xvii' => 'annex_xvii',
            'annex_vi_entry' => 'annex_vi_entry',
            'annex_xiv_entry' => 'annex_xiv_entry',
            'annex_xvii_entry' => 'annex_xvii_entry',
        ];

        return $map[$slug] ?? null;
    }

    private function parseCsvLine(
        string $line,
        string $delimiter
    ): array {
        $line = trim($this->cleanCsvBom($line));

        if ($line === '') {
            return [];
        }

        $parsed = str_getcsv($line, $delimiter);

        if (
            count($parsed) === 1 &&
            str_contains((string) ($parsed[0] ?? ''), $delimiter)
        ) {
            $parsed = str_getcsv(
                trim((string) $parsed[0]),
                $delimiter
            );
        }

        return array_map(
            fn ($value) => trim(
                (string) $value,
                " \t\n\r\0\x0B\"'"
            ),
            $parsed
        );
    }

    private function cleanCsvBom(string $value): string
    {
        return preg_replace('/^\xEF\xBB\xBF/', '', $value);
    }

    private function cleanIdentifier($value): ?string
    {
        $value = trim((string) $value);

        if ($value === '' || $value === '-') {
            return null;
        }

        return preg_replace('/\s+/', '', $value);
    }

    private function linesToArray($value): ?array
    {
        $value = trim((string) $value);

        if ($value === '' || $value === '-') {
            return null;
        }

        $items = array_values(array_unique(array_filter(
            array_map(
                'trim',
                preg_split('/\r\n|\r|\n/', $value)
            ),
            fn ($item) => $item !== '' && $item !== '-'
        )));

        return count($items) ? $items : null;
    }

    private function stringToArray(?string $value): ?array
    {
        if (!$value) {
            return null;
        }

        $items = array_values(array_filter(array_map(
            'trim',
            preg_split('/[,;]+/', $value)
        )));

        return count($items) ? $items : null;
    }

    private function pipeToArray(?string $value): ?array
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $items = array_values(array_filter(array_map(
            'trim',
            explode('|', $value)
        ), fn ($item) => $item !== ''));

        return count($items) ? $items : null;
    }

    private function jsonToArray(?string $value)
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $decoded = json_decode($value, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        return $decoded;
    }

    private function nullableNumber($value): ?float
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $value = str_replace(',', '.', trim((string) $value));

        if (!is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }

    private function toNullableBoolean($value): ?bool
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $value = mb_strtolower(trim((string) $value), 'UTF-8');

        if (in_array($value, [
            '1',
            'true',
            'yes',
            'evet',
            'e',
            'var',
        ], true)) {
            return true;
        }

        if (in_array($value, [
            '0',
            'false',
            'no',
            'hayır',
            'hayir',
            'h',
            'yok',
        ], true)) {
            return false;
        }

        return null;
    }

    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function normalizeArray($value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $value = array_map(
            fn ($item) => trim((string) $item),
            $value
        );

        $value = array_values(array_filter(
            $value,
            fn ($item) => $item !== ''
        ));

        sort($value, SORT_STRING);

        return $value;
    }

    private function normalizeJson($value): string
    {
        if ($value === null || $value === [] || $value === false) {
            return '';
        }

        if (is_array($value)) {
            $value = $this->recursiveSort($value);
        }

        return json_encode(
            $value,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES |
            JSON_PRESERVE_ZERO_FRACTION
        );
    }

    private function recursiveSort(array $value): array
    {
        foreach ($value as $key => $item) {
            if (is_array($item)) {
                $value[$key] = $this->recursiveSort($item);
            }
        }

        if (
            count($value) > 0 &&
            array_keys($value) !== range(0, count($value) - 1)
        ) {
            ksort($value);
        } else {
            usort($value, function ($a, $b) {
                return strcmp(
                    json_encode($a, JSON_UNESCAPED_UNICODE),
                    json_encode($b, JSON_UNESCAPED_UNICODE)
                );
            });
        }

        return $value;
    }


    private function normalizeDateValue($value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return trim((string) $value);
        }
    }

    private function normalizeNumber($value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        if (!is_numeric($value)) {
            return '';
        }

        return (string) (float) $value;
    }

    private function normalizeBoolean($value): string
    {
        if ($value === null) {
            return '';
        }

        if ($value === false || $value === 0 || $value === '0') {
            return '0';
        }

        return '1';
    }
}
