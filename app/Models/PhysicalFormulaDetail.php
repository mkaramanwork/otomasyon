<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PhysicalFormulaDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'physical_formula_id',
        'physical_parameter_id',
        'selection_label',
        'quantity',
    ];

    public function formula()
    {
        return $this->belongsTo(PhysicalFormula::class, 'physical_formula_id');
    }

    public function parameter()
    {
        return $this->belongsTo(PhysicalParameter::class, 'physical_parameter_id');
    }
}