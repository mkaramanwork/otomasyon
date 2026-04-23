<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabProduct extends Model
{
    protected $fillable = ['product_code','product_name'];

    public function parameters()
    {
        return $this->hasMany(LabProductParameter::class, 'lab_product_id');
    }
}

