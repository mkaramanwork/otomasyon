<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormulaDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'formula_id',
        'raw_material_id',
        'amount',
        'percentage',
        'unit',
    ];

    public function formula()
    {
        return $this->belongsTo(Formula::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}   