<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabParameter extends Model
{
    protected $fillable = [
        'sample_type',
        'parameter',
        'method_tr',
        'method_en',
        'method_ru',
        'unit',
        'scope',
        'uncertainty',
    ];

    // ✅ Pivot ilişki: parametre → numuneler
    public function sampleAcceptances()
    {
        return $this->belongsToMany(SampleAcceptance::class, 'sample_acceptance_parameter')
                    ->withTimestamps();
    }
}