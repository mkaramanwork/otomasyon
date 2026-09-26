<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = [
        'offer_no','customer_id','title','text','offer_date',
        'sample_count','total_amount','discount_rate','net_amount',
        'status','approved_by','approved_pdf'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function parameters()
    {
        return $this->hasMany(OfferParameter::class);
    }

    public function samples()
    {
        return $this->hasMany(OfferSample::class);
    }

    public function acceptedSamples()
    {
        return $this->hasMany(SampleAcceptance::class,'offer_id');
    }
}

