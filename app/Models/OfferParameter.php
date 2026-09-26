<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferParameter extends Model
{
    protected $fillable = [
        'offer_id','parameter_id','method','price','scope'
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function parameter()
    {
        return $this->belongsTo(LabParameter::class,'parameter_id');
    }
}
