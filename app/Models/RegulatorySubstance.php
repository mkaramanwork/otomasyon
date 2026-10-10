<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegulatorySubstance extends Model
{
    use HasFactory;

    protected $fillable = [
        'preferred_name',
        'cas_no',
        'ec_no',
        'index_no',
        'molecular_formula',
        'substance_type',
        'echa_uuid',
        'echa_substance_url',
        'created_from',
        'verification_status',
        'last_echa_check_at',
        'is_active',
    ];

    protected $casts = [
        'last_echa_check_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function classifications()
    {
        return $this->hasMany(RegulatoryClassification::class);
    }

    public function sdsComponents()
    {
        return $this->hasMany(RawMaterialSdsComponent::class);
    }

    public function rawMaterialSdsProfiles()
    {
        return $this->hasMany(RawMaterialSdsProfile::class);
    }
}