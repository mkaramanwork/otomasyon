<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabProductParameter extends Model
{
    protected $fillable = ['lab_product_id','parameter_id','min_value','max_value','net_value'];

    public function product()
    {
        return $this->belongsTo(LabProduct::class);
    }

    public function parameter()
    {
        return $this->belongsTo(LabParameter::class);
    }
}
