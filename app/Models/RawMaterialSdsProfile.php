<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterialSdsProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'raw_material_id',
        'regulatory_substance_id',
        'supplier_name',
        'supplier_product_name',
        'sds_language',
        'regulation_profile',
        'supplier_sds_date',
        'supplier_sds_revision',
        'supplier_sds_version',
        'supplier_sds_file',
        'substance_or_mixture',
        'cas_no',
        'ec_no',
        'index_no',
        'reach_registration_no',
        'kkdik_registration_no',
        'hazard_classifications',
        'signal_word',
        'pictograms',
        'hazard_statements',
        'supplemental_hazard_statements',
        'precautionary_statements',
        'physical_state',
        'colour',
        'odour',
        'density',
        'density_unit',
        'flash_point',
        'flash_point_unit',
        'kinematic_viscosity',
        'kinematic_viscosity_temperature',
        'kinematic_viscosity_unit',
        'dnel_data',
        'pnec_data',
        'toxicology_data',
        'ecotoxicology_data',
        'transport_data',
        'regulatory_data',
        'source_name',
        'source_date',
        'is_current',
        'is_verified',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'supplier_sds_date' => 'date',
        'source_date' => 'date',

        'hazard_classifications' => 'array',
        'pictograms' => 'array',
        'hazard_statements' => 'array',
        'supplemental_hazard_statements' => 'array',
        'precautionary_statements' => 'array',

        'dnel_data' => 'array',
        'pnec_data' => 'array',
        'toxicology_data' => 'array',
        'ecotoxicology_data' => 'array',
        'transport_data' => 'array',
        'regulatory_data' => 'array',

        'is_current' => 'boolean',
        'is_verified' => 'boolean',
    ];

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function regulatorySubstance()
    {
        return $this->belongsTo(RegulatorySubstance::class);
    }

    public function components()
    {
        return $this->hasMany(RawMaterialSdsComponent::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}