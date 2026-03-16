<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechnicalSpecification extends Model
{
    protected $fillable = [
        'inventory_id','spec_title','spec_detail','standards','compliance','document_code'
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
