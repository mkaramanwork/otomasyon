<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhysicalFormula extends Model
{
    protected $fillable = [
        'product_id',
        'product_code',
        'product_name',
        'unit',
        'description',
    ];


    public function details()
    {
        return $this->hasMany(PhysicalFormulaDetail::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_code', 'code');
    }
    

}