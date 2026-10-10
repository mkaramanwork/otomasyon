<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\RawMaterialSdsProfile;
use App\Models\RawMaterialSdsComponent;
use App\Models\RegulatorySubstance;
use App\Models\RegulatoryClassification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RawMaterialSdsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('q');

        $rawMaterials = RawMaterial::with('currentSdsProfile')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('id', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('sds.index', compact('rawMaterials', 'search'));
    }

    public function create(RawMaterial $rawMaterial)
    {
        $rawMaterial->load('currentSdsProfile');

        return view('sds.create', compact('rawMaterial'));
    }

    public function store(Request $request, RawMaterial $rawMaterial)
    {
        $validated = $request->validate([
            'supplier_name' => 'nullable|string|max:255',
            'supplier_product_name' => 'nullable|string|max:255',
            'sds_language' => 'required|string|max:10',
            'regulation_profile' => 'nullable|string|max:100',
            'supplier_sds_date' => 'nullable|date',
            'supplier_sds_revision' => 'nullable|string|max:255',
            'supplier_sds_version' => 'nullable|string|max:255',
            'supplier_sds_file' => 'nullable|file|mimes:pdf,doc,docx|max:20480',
            'substance_or_mixture' => 'nullable|in:substance,mixture',
            'source_name' => 'nullable|string|max:255',
            'source_date' => 'nullable|date',
        ]);

        $currentProfiles = RawMaterialSdsProfile::where('raw_material_id', $rawMaterial->id)
            ->where('is_current', true)
            ->get();

        foreach ($currentProfiles as $profile) {
            $profile->update([
                'is_current' => false,
                'updated_by' => Auth::id(),
            ]);
        }

        $filePath = null;

        if ($request->hasFile('supplier_sds_file')) {
            $filePath = $request->file('supplier_sds_file')
                ->store('sds/raw-materials', 'public');
        }

        RawMaterialSdsProfile::create([
            'raw_material_id' => $rawMaterial->id,
            'supplier_name' => $validated['supplier_name'] ?? null,
            'supplier_product_name' => $validated['supplier_product_name'] ?? null,
            'sds_language' => $validated['sds_language'],
            'regulation_profile' => $validated['regulation_profile'] ?? null,
            'supplier_sds_date' => $validated['supplier_sds_date'] ?? null,
            'supplier_sds_revision' => $validated['supplier_sds_revision'] ?? null,
            'supplier_sds_version' => $validated['supplier_sds_version'] ?? null,
            'supplier_sds_file' => $filePath,
            'substance_or_mixture' => $validated['substance_or_mixture'] ?? null,
            'source_name' => $validated['source_name'] ?? null,
            'source_date' => $validated['source_date'] ?? null,
            'is_current' => true,
            'is_verified' => false,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route('sds.raw-materials.index')
            ->with('success', 'Hammadde SDS ana kartı başarıyla oluşturuldu.');
    }

    public function show(
        RawMaterial $rawMaterial,
        RawMaterialSdsProfile $profile
    ) {
        if ($profile->raw_material_id != $rawMaterial->id) {
            abort(404);
        }

        $profile->load([
            'components.regulatorySubstance'
        ]);

        $substanceIds = $profile->components
            ->pluck('regulatory_substance_id')
            ->filter()
            ->unique()
            ->values();

        $regulatoryBySubstance = collect();

        if ($substanceIds->isNotEmpty()) {
            $regulatoryBySubstance = RegulatoryClassification::whereIn(
                'regulatory_substance_id',
                $substanceIds
            )
                ->where('is_current', true)
                ->get()
                ->groupBy('regulatory_substance_id');
        }

        return view('sds.show', compact(
            'rawMaterial',
            'profile',
            'regulatoryBySubstance'
        ));
    }

    /** SDS edit uses the existing view and keeps historical profiles read-only. */
    public function edit(RawMaterial $rawMaterial, RawMaterialSdsProfile $profile)
    {
        abort_unless((int) $profile->raw_material_id === (int) $rawMaterial->id, 404);
        abort_unless((bool) $profile->is_current, 403, 'Eski SDS revizyonlari degistirilemez.');
        return view('sds.edit', compact('rawMaterial', 'profile'));
    }

    /** Save changes as a NEW current SDS snapshot, retaining all older components. */
    public function update(Request $request, RawMaterial $rawMaterial, RawMaterialSdsProfile $profile)
    {
        abort_unless((int) $profile->raw_material_id === (int) $rawMaterial->id, 404);
        $validated = $request->validate([
            'supplier_name' => 'nullable|string|max:255',
            'supplier_product_name' => 'nullable|string|max:255',
            'sds_language' => 'required|in:tr,en,de,fr,es',
            'regulation_profile' => 'nullable|in:TR_KKDIK_SEA,EU_REACH_CLP',
            'supplier_sds_date' => 'nullable|date',
            'supplier_sds_revision' => 'nullable|string|max:255',
            'supplier_sds_version' => 'nullable|string|max:255',
            'supplier_sds_file' => 'nullable|file|mimes:pdf,doc,docx|max:20480',
            'substance_or_mixture' => 'nullable|in:substance,mixture',
            'cas_no' => 'nullable|string|max:50',
            'ec_no' => 'nullable|string|max:50',
            'index_no' => 'nullable|string|max:50',
            'reach_registration_no' => 'nullable|string|max:255',
            'kkdik_registration_no' => 'nullable|string|max:255',
            'source_name' => 'nullable|string|max:255',
            'source_date' => 'nullable|date',
        ]);
        $next = DB::transaction(function () use ($request, $rawMaterial, $profile, $validated) {
            // Serialize concurrent revision attempts on this raw-material record.
            $current = RawMaterialSdsProfile::query()
                ->where('raw_material_id', $rawMaterial->id)
                ->where('is_current', true)
                ->lockForUpdate()->first();
            abort_unless($current && (int) $current->id === (int) $profile->id, 409,
                'Bu SDS baska bir kullanici tarafindan guncellenmis. Sayfayi yenileyin.');

            $new = $profile->replicate();
            foreach ($validated as $key => $value) {
                if ($key !== 'supplier_sds_file') $new->{$key} = $value;
            }
            if ($request->hasFile('supplier_sds_file')) {
                $new->supplier_sds_file = $request->file('supplier_sds_file')
                    ->store('sds/raw-materials', 'public');
            }
            // Revision number is supplier-defined; never silently fabricate one.
            // Distinct profile ID provides reliable internal version tracking.
            $new->is_current = true;
            $new->is_verified = false;
            $new->created_by = Auth::id();
            $new->updated_by = Auth::id();
            $current->is_current = false;
            $current->updated_by = Auth::id();
            $current->save();
            $new->save();
            foreach ($profile->components as $component) {
                $copy = $component->replicate();
                $copy->raw_material_sds_profile_id = $new->id;
                // Evidence remains attached to the exact raw-material component.
                $copy->save();
            }
            return $new;
        });
        return redirect()->route('sds.raw-materials.show', [$rawMaterial->id, $next->id])
            ->with('success', 'Yeni SDS versiyonu kaydedildi. Onceki kayit korundu. Etkilenen receteleri yeniden analiz edin.');
    }

    public function history(RawMaterial $rawMaterial)
    {
        $profiles = RawMaterialSdsProfile::query()
            ->where('raw_material_id', $rawMaterial->id)
            ->orderByDesc('id')->get();
        return view('sds.history', compact('rawMaterial', 'profiles'));
    }

    public function searchRegulatory(Request $request)
    {
        $cas = trim((string) $request->get('cas'));
        $ec = trim((string) $request->get('ec'));
        $index = trim((string) $request->get('index'));
        $name = trim((string) $request->get('name'));

        if (
            $cas === '' &&
            $ec === '' &&
            $index === '' &&
            mb_strlen($name) < 2
        ) {
            return response()->json([]);
        }

        $query = RegulatorySubstance::query();

        $query->where(function ($q) use ($cas, $ec, $index, $name) {
            $hasCondition = false;

            if ($cas !== '') {
                $q->orWhere('cas_no', $cas);
                $hasCondition = true;
            }

            if ($ec !== '') {
                $q->orWhere('ec_no', $ec);
                $hasCondition = true;
            }

            if ($index !== '') {
                $q->orWhere('index_no', $index);
                $hasCondition = true;
            }

            if (mb_strlen($name) >= 2) {
                $q->orWhere(
                    'preferred_name',
                    'like',
                    '%' . $name . '%'
                );
                $hasCondition = true;
            }

            if (!$hasCondition) {
                $q->whereRaw('1 = 0');
            }
        });

        $substances = $query
            ->limit(15)
            ->get();

        if ($substances->isEmpty()) {
            return response()->json([]);
        }

        $classifications = RegulatoryClassification::whereIn(
            'regulatory_substance_id',
            $substances->pluck('id')
        )
            ->where('is_current', true)
            ->get()
            ->groupBy('regulatory_substance_id');

        $results = $substances->map(function ($substance) use (
            $classifications,
            $cas,
            $ec,
            $index,
            $name
        ) {
            $records = $classifications->get(
                $substance->id,
                collect()
            );

            $annexViRecords = $records->filter(
                fn ($item) => (bool) $item->annex_vi
            );

            $trRecords = $records->filter(
                fn ($item) =>
                    $item->regulation_profile === 'TR_KKDIK_SEA'
            );

            $classificationRecords = $annexViRecords->isNotEmpty()
                ? $annexViRecords
                : $trRecords;

            $hazardClassifications = $classificationRecords
                ->flatMap(function ($item) {
                    $value = $item->hazard_classifications;

                    if (is_array($value)) {
                        return $value;
                    }

                    if (is_string($value) && trim($value) !== '') {
                        return preg_split('/[,;]+/', $value);
                    }

                    return [];
                })
                ->map(fn ($value) => trim((string) $value))
                ->filter()
                ->unique()
                ->values()
                ->all();

            $hazardStatements = $classificationRecords
                ->flatMap(function ($item) {
                    $value = $item->hazard_statements;

                    if (is_array($value)) {
                        return $value;
                    }

                    if (is_string($value) && trim($value) !== '') {
                        return preg_split('/[,;]+/', $value);
                    }

                    return [];
                })
                ->map(fn ($value) => trim((string) $value))
                ->filter()
                ->unique()
                ->values()
                ->all();

            $annexVi = $annexViRecords->isNotEmpty();

            $svhc = $records->contains(
                fn ($item) => (bool) $item->svhc
            );

            $annexXiv = $records->contains(
                fn ($item) => (bool) $item->annex_xiv
            );

            $annexXvii = $records->contains(
                fn ($item) => (bool) $item->annex_xvii
            );

            $hasTr = $trRecords->isNotEmpty();

            $score = 0;

            if ($cas !== '' && $substance->cas_no === $cas) {
                $score += 100;
            }

            if ($index !== '' && $substance->index_no === $index) {
                $score += 90;
            }

            if ($ec !== '' && $substance->ec_no === $ec) {
                $score += 80;
            }

            if (
                $name !== '' &&
                mb_strtolower(trim((string) $substance->preferred_name)) ===
                mb_strtolower($name)
            ) {
                $score += 40;
            }

            return [
                'id' => $substance->id,
                'preferred_name' => $substance->preferred_name,
                'cas_no' => $substance->cas_no,
                'ec_no' => $substance->ec_no,
                'index_no' => $substance->index_no,
                'annex_vi' => $annexVi,
                'svhc' => $svhc,
                'annex_xiv' => $annexXiv,
                'annex_xvii' => $annexXvii,
                'tr_kkdik_sea' => $hasTr,
                'hazard_classifications' => $hazardClassifications,
                'hazard_statements' => $hazardStatements,
                'classification_source' =>
                    $annexViRecords->isNotEmpty()
                        ? 'ECHA Annex VI'
                        : (
                            $trRecords->isNotEmpty()
                                ? 'TR KKDİK / SEA'
                                : null
                        ),
                'score' => $score,
            ];
        })
            ->sortByDesc('score')
            ->values();

        return response()->json($results);
    }

    public function storeComponent(
        Request $request,
        RawMaterial $rawMaterial,
        RawMaterialSdsProfile $profile
    ) {
        if ($profile->raw_material_id != $rawMaterial->id) {
            abort(404);
        }

        $validated = $request->validate([
            'chemical_name' => 'required|string|max:255',
            'cas_no' => 'nullable|string|max:50',
            'ec_no' => 'nullable|string|max:50',
            'index_no' => 'nullable|string|max:50',
            'regulatory_substance_id' =>
                'nullable|exists:regulatory_substances,id',
            'concentration_min' =>
                'nullable|numeric|min:0|max:100',
            'concentration_max' =>
                'nullable|numeric|min:0|max:100',
            'hazard_classifications_text' =>
                'nullable|string',
            'h_statements_text' =>
                'nullable|string',
            'source_page' =>
                'nullable|integer|min:1',
            'supplier_note' =>
                'nullable|string',
        ]);

        if (
            isset($validated['concentration_min']) &&
            isset($validated['concentration_max']) &&
            $validated['concentration_min'] >
            $validated['concentration_max']
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'concentration_min' =>
                        'Minimum konsantrasyon maksimum değerden büyük olamaz.'
                ]);
        }

        $hazardClassifications = $this->splitTextList(
            $validated['hazard_classifications_text'] ?? null
        );

        $hStatements = $this->splitTextList(
            $validated['h_statements_text'] ?? null
        );

        RawMaterialSdsComponent::create([
            'raw_material_sds_profile_id' =>
                $profile->id,
            'regulatory_substance_id' =>
                $validated['regulatory_substance_id'] ?? null,
            'chemical_name' =>
                $validated['chemical_name'],
            'cas_no' =>
                $validated['cas_no'] ?? null,
            'ec_no' =>
                $validated['ec_no'] ?? null,
            'index_no' =>
                $validated['index_no'] ?? null,
            'concentration_min' =>
                $validated['concentration_min'] ?? null,
            'concentration_max' =>
                $validated['concentration_max'] ?? null,
            'concentration_unit' =>
                '%',
            'hazard_classifications' =>
                $hazardClassifications,
            'h_statements' =>
                $hStatements,
            'source_section' =>
                '3',
            'source_page' =>
                $validated['source_page'] ?? null,
            'supplier_note' =>
                $validated['supplier_note'] ?? null,
            'is_disclosed' =>
                true,
            'is_verified' =>
                false,
        ]);

        return redirect()
            ->route('sds.raw-materials.show', [
                $rawMaterial->id,
                $profile->id
            ])
            ->with(
                'success',
                'Bölüm 3 bileşeni başarıyla eklendi.'
            );
    }

    public function attachRegulatory(
        Request $request,
        RawMaterial $rawMaterial,
        RawMaterialSdsProfile $profile,
        RawMaterialSdsComponent $component
    ) {
        $this->validateComponentOwnership(
            $rawMaterial,
            $profile,
            $component
        );

        $validated = $request->validate([
            'regulatory_substance_id' =>
                'required|exists:regulatory_substances,id',
        ]);

        $component->update([
            'regulatory_substance_id' =>
                $validated['regulatory_substance_id'],
            'is_verified' =>
                false,
        ]);

        return back()->with(
            'success',
            'Bileşen resmî mevzuat maddesiyle eşleştirildi.'
        );
    }

    public function detachRegulatory(
        RawMaterial $rawMaterial,
        RawMaterialSdsProfile $profile,
        RawMaterialSdsComponent $component
    ) {
        $this->validateComponentOwnership(
            $rawMaterial,
            $profile,
            $component
        );

        $component->update([
            'regulatory_substance_id' => null,
            'is_verified' => false,
        ]);

        return back()->with(
            'success',
            'Mevzuat eşleşmesi kaldırıldı.'
        );
    }

    public function storeKkdikClassification(
        Request $request,
        RawMaterial $rawMaterial,
        RawMaterialSdsProfile $profile,
        RawMaterialSdsComponent $component
    ) {
        $this->validateComponentOwnership(
            $rawMaterial,
            $profile,
            $component
        );

        if (!$component->regulatory_substance_id) {
            return back()->with(
                'error',
                'KKDİK / SEA verisi eklenmeden önce bileşeni resmî maddeyle eşleştirin.'
            );
        }

        $validated = $request->validate([
            'kkdik_registration_no' =>
                'nullable|string|max:255',
            'tr_hazard_classifications_text' =>
                'nullable|string',
            'tr_h_statements_text' =>
                'nullable|string',
            'tr_source_name' =>
                'required|string|max:255',
            'tr_source_date' =>
                'nullable|date',
            'tr_regulatory_note' =>
                'nullable|string',
        ]);

        $hazardClassifications = $this->splitTextList(
            $validated['tr_hazard_classifications_text'] ?? null
        );

        $hStatements = $this->splitTextList(
            $validated['tr_h_statements_text'] ?? null
        );

        $notes = [
            'kkdik_registration_no' =>
                $validated['kkdik_registration_no'] ?? null,
            'note' =>
                $validated['tr_regulatory_note'] ?? null,
            'entered_from' =>
                'raw_material_sds_component',
            'raw_material_sds_component_id' =>
                $component->id,
            'raw_material_sds_profile_id' =>
                $profile->id,
        ];

        RegulatoryClassification::create([
            'regulatory_substance_id' =>
                $component->regulatory_substance_id,
            'regulation_profile' =>
                'TR_KKDIK_SEA',
            'jurisdiction' =>
                'TR',
            'source_type' =>
                'MANUAL_KKDIK_SEA',
            'source_name' =>
                $validated['tr_source_name'],
            'source_version' =>
                null,
            'source_date' =>
                $validated['tr_source_date'] ?? null,
            'source_reference' =>
                !empty($validated['kkdik_registration_no'])
                    ? 'KKDIK:' . $validated['kkdik_registration_no']
                    : 'KKDIK:MANUAL:' .
                        $component->id .
                        ':' .
                        now()->format('YmdHis'),
            'source_url' =>
                null,
            'legal_status' =>
                'review_required',
            'hazard_classifications' =>
                $hazardClassifications,
            'signal_word' =>
                null,
            'pictograms' =>
                null,
            'hazard_statements' =>
                $hStatements,
            'supplemental_hazard_statements' =>
                null,
            'specific_concentration_limits' =>
                null,
            'ate_data' =>
                null,
            'm_factor_acute' =>
                null,
            'm_factor_chronic' =>
                null,
            'reach_registered' =>
                false,
            'svhc' =>
                false,
            'annex_vi' =>
                false,
            'annex_xiv' =>
                false,
            'annex_xvii' =>
                false,
            'annex_vi_entry' =>
                null,
            'annex_xiv_entry' =>
                null,
            'annex_xvii_entry' =>
                null,
            'regulatory_notes' =>
                $notes,
            'effective_from' =>
                $validated['tr_source_date'] ?? null,
            'effective_until' =>
                null,
            'checked_at' =>
                now(),
            'is_current' =>
                true,
            'is_verified' =>
                false,
        ]);

        return back()->with(
            'success',
            'KKDİK / SEA verisi eklendi. Kayıt kontrol bekliyor durumundadır.'
        );
    }

    private function validateComponentOwnership(
        RawMaterial $rawMaterial,
        RawMaterialSdsProfile $profile,
        RawMaterialSdsComponent $component
    ): void {
        if ($profile->raw_material_id != $rawMaterial->id) {
            abort(404);
        }

        if (
            $component->raw_material_sds_profile_id
            != $profile->id
        ) {
            abort(404);
        }
    }

    private function splitTextList(?string $value): ?array
    {
        if (!$value) {
            return null;
        }

        $items = array_values(
            array_filter(
                array_map(
                    'trim',
                    preg_split('/[,;]+/', $value)
                )
            )
        );

        return count($items)
            ? $items
            : null;
    }
}
