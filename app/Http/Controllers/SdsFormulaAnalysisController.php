<?php

namespace App\Http\Controllers;

use App\Models\Formula;
use App\Models\SdsFormulaProfile;
use App\Services\Sds\MixtureClassificationService;
use App\Services\Sds\MixtureHazardEngineService;
use App\Services\Sds\Sds16SectionBuilderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SdsFormulaAnalysisController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('q'));

        $formulas = Formula::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where(
                        'name',
                        'like',
                        '%' . $search . '%'
                    );

                    if (ctype_digit($search)) {
                        $q->orWhere(
                            'id',
                            (int) $search
                        );
                    }
                });
            })
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view(
            'sds.formula-analysis.index',
            compact(
                'formulas',
                'search'
            )
        );
    }

    public function show(
        Request $request,
        Formula $formula,
        MixtureClassificationService $mixtureClassificationService,
        MixtureHazardEngineService $mixtureHazardEngineService,
        Sds16SectionBuilderService $sds16SectionBuilderService
    ) {
        $regulationProfile = $this->regulationProfile(
            $request->get('regulation_profile')
        );

        $profile = SdsFormulaProfile::where(
            'formula_id',
            $formula->id
        )->first();

        $analysis = $mixtureClassificationService->collect(
            $formula,
            $regulationProfile
        );

        $physicalProperties =
            $profile?->physical_properties
            ?? [];

        $classification = $mixtureHazardEngineService->classify(
            $analysis,
            $physicalProperties
        );

        $sdsDraft = $sds16SectionBuilderService->build(
            $formula,
            $analysis,
            $classification,
            $regulationProfile,
            $profile
        );

        return view(
            'sds.formula-analysis.show',
            compact(
                'formula',
                'profile',
                'analysis',
                'classification',
                'sdsDraft',
                'regulationProfile'
            )
        );
    }

    public function profile(
        Request $request,
        Formula $formula
    ) {
        $regulationProfile = $this->regulationProfile(
            $request->get('regulation_profile')
        );

        $profile = SdsFormulaProfile::firstOrNew([
            'formula_id' => $formula->id,
        ]);

        if (!$profile->exists) {
            $profile->product_type =
                config(
                    'sds.defaults.product_type',
                    'lubricant'
                );

            $profile->identified_use =
                config(
                    'sds.defaults.identified_use'
                );

            $profile->uses_advised_against =
                config(
                    'sds.defaults.uses_advised_against'
                );
        }

        $companies = \App\Models\SdsCompany::where('is_active',true)->orderBy('name')->get();
        $families = \App\Models\SdsProductFamily::where('is_active',true)->orderBy('name')->get();
        $templates = \App\Models\SdsTemplate::where('is_active',true)->orderBy('name')->get();
        $preparers = \App\Models\SdsPreparer::where('is_active',true)->orderBy('name')->get();
        return view('sds.formula-analysis.profile', compact('formula','profile','regulationProfile','companies','families','templates','preparers'));
    }

    public function saveProfile(
        Request $request,
        Formula $formula
    ) {
        $validated = $request->validate([
            'regulation_profile' =>
                'nullable|in:TR_KKDIK_SEA,EU_REACH_CLP',
            'sds_company_id' => 'required|exists:sds_companies,id',
            'sds_product_family_id' => 'required|exists:sds_product_families,id',
            'sds_template_id' => 'required|exists:sds_templates,id',
            'sds_preparer_id' => 'nullable|exists:sds_preparers,id',

            'physical_properties.physical_state' =>
                'required|string|max:100',

            'physical_properties.colour' =>
                'required|string|max:255',

            'physical_properties.density' =>
                'required|string|max:255',

            'physical_properties.flash_point_c' =>
                'required|numeric',

            'physical_properties.kinematic_viscosity_40_cst' =>
                'required|numeric|min:0',

            'physical_properties.kinematic_viscosity_100_cst' =>
                'required|numeric|min:0',

            'physical_properties.pour_point_c' =>
                'required|numeric',

            'override_enabled' =>
                'nullable|boolean',

            'override.first_aid.*' =>
                'nullable|string|max:4000',

            'override.fire_fighting.*' =>
                'nullable|string|max:4000',

            'override.accidental_release.*' =>
                'nullable|string|max:4000',

            'override.handling_storage.*' =>
                'nullable|string|max:4000',

            'override.exposure_controls.*' =>
                'nullable|string|max:8000',

            'override.stability_reactivity.*' =>
                'nullable|string|max:4000',

            'override.toxicology.*' =>
                'nullable|string|max:8000',

            'override.ecology.*' =>
                'nullable|string|max:8000',

            'override.disposal.*' =>
                'nullable|string|max:4000',

            'override.transport.*' =>
                'nullable|string|max:4000',

            'override.regulatory.*' =>
                'nullable|string|max:4000',

            'override.revision.*' =>
                'nullable|string|max:2000',
        ]);

        $profile = SdsFormulaProfile::firstOrNew([
            'formula_id' => $formula->id,
        ]);

        $templateValid = \App\Models\SdsTemplate::whereKey($validated['sds_template_id'])
            ->where('company_id',$validated['sds_company_id'])->where('is_active',true)->exists();
        if (!$templateValid) return back()->withErrors(['sds_template_id'=>'Şablon seçilen firmaya ait değil.'])->withInput();
        $profile->formula_id = $formula->id;
        foreach (['sds_company_id','sds_product_family_id','sds_template_id','sds_preparer_id'] as $key) {
            $profile->{$key} = $validated[$key] ?? null;
        }

        $profile->product_type =
            $profile->product_type
            ?: config(
                'sds.defaults.product_type',
                'lubricant'
            );

        $profile->identified_use =
            $profile->identified_use
            ?: config(
                'sds.defaults.identified_use'
            );

        $profile->uses_advised_against =
            $profile->uses_advised_against
            ?: config(
                'sds.defaults.uses_advised_against'
            );

        $profile->physical_properties =
            $this->cleanArray(
                $validated[
                    'physical_properties'
                ]
            );

        $override =
            $request->boolean(
                'override_enabled'
            )
                ? $this->cleanArray(
                    $request->input(
                        'override',
                        []
                    )
                )
                : [];

        foreach (
            [
                'first_aid',
                'fire_fighting',
                'accidental_release',
                'handling_storage',
                'exposure_controls',
                'stability_reactivity',
                'toxicology',
                'ecology',
                'disposal',
                'transport',
                'regulatory',
                'revision',
            ]
            as $jsonField
        ) {
            $profile->{$jsonField} =
                $override[$jsonField]
                ?? [];
        }

        $profile->updated_by =
            Auth::id();

        $profile->save();

        return redirect()
            ->route(
                'sds.formula-analysis.show',
                [
                    'formula' =>
                        $formula->id,

                    'regulation_profile' =>
                        $this->regulationProfile(
                            $request->input(
                                'regulation_profile'
                            )
                        ),
                ]
            )
            ->with(
                'success',
                '7 fiziksel değer kaydedildi. Diğer SDS alanları otomatik üretildi.'
            );
    }


    public function print(
        Request $request,
        Formula $formula,
        MixtureClassificationService $mixtureClassificationService,
        MixtureHazardEngineService $mixtureHazardEngineService,
        Sds16SectionBuilderService $sds16SectionBuilderService
    ) {
        app()->setLocale($request->query('lang') === 'tr' ? 'tr' : 'en');

        $regulationProfile = $this->regulationProfile(
            $request->get('regulation_profile')
        );

        $profile = SdsFormulaProfile::where(
            'formula_id',
            $formula->id
        )->first();

        $analysis = $mixtureClassificationService->collect(
            $formula,
            $regulationProfile
        );

        $physicalProperties =
            $profile?->physical_properties
            ?? [];

        $classification = $mixtureHazardEngineService->classify(
            $analysis,
            $physicalProperties
        );

        $sdsDraft = $sds16SectionBuilderService->build(
            $formula,
            $analysis,
            $classification,
            $regulationProfile,
            $profile
        );

        if (
            empty(
                $sdsDraft['summary']['publish_ready']
            )
        ) {
            $notReady = collect(
                $sdsDraft['sections']
                ?? []
            )
                ->filter(
                    fn ($section) =>
                        ($section['status'] ?? null)
                        !== 'ready'
                )
                ->map(
                    fn ($section) =>
                        ($section['number'] ?? '?')
                        . ' - '
                        . ($section['title'] ?? '')
                )
                ->values()
                ->all();

            return redirect()
                ->route(
                    'sds.formula-analysis.show',
                    [
                        'formula' =>
                            $formula->id,

                        'regulation_profile' =>
                            $regulationProfile,
                    ]
                )
                ->withErrors([
                    'sds_print' =>
                        'SDS baskıya hazır değil. Eksik/bloke bölümler: '
                        . implode(
                            ', ',
                            $notReady
                        ),
                ]);
        }

        $company = $profile?->sds_company_id ? \App\Models\SdsCompany::find($profile->sds_company_id) : null;
        $template = $profile?->sds_template_id ? \App\Models\SdsTemplate::find($profile->sds_template_id) : null;
        $preparer = $profile?->sds_preparer_id ? \App\Models\SdsPreparer::find($profile->sds_preparer_id) : null;
        if (!$company || !$template || $template->company_id !== $company->id) {
            return back()->withErrors(['sds_print'=>'Firma/şablon seçimi eksik veya uyumsuz.']);
        }
        return view(
            'sds.formula-analysis.print',
            compact(
                'formula',
                'profile',
                'analysis',
                'classification',
                'sdsDraft',
                'regulationProfile', 'company', 'template', 'preparer'
            )
        );
    }

    private function regulationProfile(
        ?string $value
    ): string {
        return in_array(
            $value,
            [
                'TR_KKDIK_SEA',
                'EU_REACH_CLP',
            ],
            true
        )
            ? $value
            : 'TR_KKDIK_SEA';
    }

    private function cleanArray(
        array $data
    ): array {
        $clean = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $nested = $this->cleanArray(
                    $value
                );

                if (!empty($nested)) {
                    $clean[$key] = $nested;
                }

                continue;
            }

            if ($value === null) {
                continue;
            }

            if (is_string($value)) {
                $value = trim($value);
            }

            if ($value === '') {
                continue;
            }

            $clean[$key] = $value;
        }

        return $clean;
    }
}
