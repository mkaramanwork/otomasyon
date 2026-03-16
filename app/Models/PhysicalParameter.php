<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhysicalParameter extends Model
{
        protected $fillable = [
        'name',
        'group',
        'description',
    ];

    public function details()
    {
        return $this->hasMany(PhysicalFormulaDetail::class);
    }


}