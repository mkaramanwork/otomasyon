<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SdsFormulaProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'formula_id', 'sds_company_id', 'sds_product_family_id', 'sds_template_id', 'sds_preparer_id',
        'product_type',
        'identified_use',
        'uses_advised_against',
        'first_aid',
        'fire_fighting',
        'accidental_release',
        'handling_storage',
        'exposure_controls',
        'physical_properties',
        'stability_reactivity',
        'toxicology',
        'ecology',
        'disposal',
        'transport',
        'regulatory',
        'revision',
        'updated_by',
    ];

    protected $casts = [
        'first_aid' => 'array',
        'fire_fighting' => 'array',
        'accidental_release' => 'array',
        'handling_storage' => 'array',
        'exposure_controls' => 'array',
        'physical_properties' => 'array',
        'stability_reactivity' => 'array',
        'toxicology' => 'array',
        'ecology' => 'array',
        'disposal' => 'array',
        'transport' => 'array',
        'regulatory' => 'array',
        'revision' => 'array',
    ];

    public function formula()
    {
        return $this->belongsTo(Formula::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
