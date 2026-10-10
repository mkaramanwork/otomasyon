<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterialSdsComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'raw_material_sds_profile_id',
        'regulatory_substance_id',
        'chemical_name',
        'cas_no',
        'ec_no',
        'index_no',
        'concentration_min',
        'concentration_max',
        'concentration_unit',
        'hazard_classifications',
        'h_statements',
        'specific_concentration_limits',
        'ate_data',
        'm_factor_acute',
        'm_factor_chronic',
        'regulatory_notes',
        'source_section',
        'source_page',
        'supplier_note',
        'is_disclosed',
        'is_verified',
    ];

    protected $casts = [
        'hazard_classifications' => 'array',
        'h_statements' => 'array',
        'specific_concentration_limits' => 'array',
        'ate_data' => 'array',
        'regulatory_notes' => 'array',

        'is_disclosed' => 'boolean',
        'is_verified' => 'boolean',
    ];

    public function sdsProfile()
    {
        return $this->belongsTo(
            RawMaterialSdsProfile::class,
            'raw_material_sds_profile_id'
        );
    }

    public function regulatorySubstance()
    {
        return $this->belongsTo(RegulatorySubstance::class);
    }
}