<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegulatoryClassification extends Model
{
    use HasFactory;

    protected $fillable = [
        'regulatory_substance_id',
        'regulation_profile',
        'jurisdiction',
        'source_type',
        'source_name',
        'source_version',
        'source_date',
        'source_reference',
        'source_url',
        'legal_status',
        'hazard_classifications',
        'signal_word',
        'pictograms',
        'hazard_statements',
        'supplemental_hazard_statements',
        'specific_concentration_limits',
        'ate_data',
        'm_factor_acute',
        'm_factor_chronic',
        'reach_registered',
        'svhc',
        'annex_vi',
        'annex_xiv',
        'annex_xvii',
        'annex_vi_entry',
        'annex_xiv_entry',
        'annex_xvii_entry',
        'latest_application_date',
        'sunset_date',
        'authorisation_conditions',
        'restriction_conditions',
        'dnel_data',
        'pnec_data',
        'toxicology_data',
        'ecotoxicology_data',
        'pbt',
        'vpvb',
        'endocrine_disruptor',
        'pbt_vpvb_notes',
        'endocrine_notes',
        'regulatory_notes',
        'effective_from',
        'effective_until',
        'checked_at',
        'is_current',
        'is_verified',
    ];

    protected $casts = [
        'source_date' => 'date',
        'latest_application_date' => 'date',
        'sunset_date' => 'date',
        'effective_from' => 'date',
        'effective_until' => 'date',
        'checked_at' => 'datetime',

        'hazard_classifications' => 'array',
        'pictograms' => 'array',
        'hazard_statements' => 'array',
        'supplemental_hazard_statements' => 'array',
        'specific_concentration_limits' => 'array',
        'ate_data' => 'array',
        'dnel_data' => 'array',
        'pnec_data' => 'array',
        'toxicology_data' => 'array',
        'ecotoxicology_data' => 'array',
        'regulatory_notes' => 'array',

        'reach_registered' => 'boolean',
        'svhc' => 'boolean',
        'annex_vi' => 'boolean',
        'annex_xiv' => 'boolean',
        'annex_xvii' => 'boolean',
        'pbt' => 'boolean',
        'vpvb' => 'boolean',
        'endocrine_disruptor' => 'boolean',
        'is_current' => 'boolean',
        'is_verified' => 'boolean',
    ];

    public function regulatorySubstance()
    {
        return $this->belongsTo(RegulatorySubstance::class);
    }
}