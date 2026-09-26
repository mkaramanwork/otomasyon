<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferSample extends Model
{
    protected $fillable = [
        'offer_id','sample_id','sequence'
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function sample()
    {
        return $this->belongsTo(SampleAcceptance::class,'sample_id');
    }
}
