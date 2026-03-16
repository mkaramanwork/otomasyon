<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model {
    protected $fillable = [
        'name','contact','address','accreditation','status','performance_score','notes'
    ];

    public function evaluations()
    {
        return $this->hasMany(SupplierEvaluation::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }


}
