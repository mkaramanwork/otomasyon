<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductionFormulaDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_order_id',
        'formula_detail_id',
        'calculated_amount',
        'is_completed',
    ];

    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class);
    }

    public function formulaDetail()
    {
        return $this->belongsTo(FormulaDetail::class);
    }
}