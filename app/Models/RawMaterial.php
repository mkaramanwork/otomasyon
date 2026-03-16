<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RawMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit',
        'stock_kg',
        'unit_price_tl',
        'unit_price_usd',
        'unit_price_eur',
        'currency',
        'added_by',
    ];

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    // Formül detayları ile ilişki
    public function formulaDetails()
    {
        return $this->hasMany(FormulaDetail::class);
    }
        public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

}