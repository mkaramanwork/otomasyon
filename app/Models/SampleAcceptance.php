<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SampleAcceptance extends Model
{
    protected $fillable = [
        'product_id',
        'company_name',
        'batch_no',
        'country',
        'uncertainty',
        'report_request',
        'report_no',
        'request_no',
        'sample_type',
        'description',
        'taken_place',
        'taken_by',
        'taken_at',
        'amount',
        'protection',
        'status',
        'accepted_by',

        // ✅ Rapor meta alanları
        'report_date',
        'report_description',
        'report_images',
    ];

        protected $casts = [
        'taken_at'     => 'datetime',
        'report_date'  => 'datetime',
        'report_images'=> 'array', 
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function acceptedUser()
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    public function parameters()
    {
        return $this->belongsToMany(\App\Models\LabParameter::class, 'sample_acceptance_parameter')
                    ->withPivot(['result','result_date','description'])
                    ->withTimestamps();
    }

    public function logs()
    {
        return $this->hasMany(BusinessLog::class,'sample_acceptance_id')->latest();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }


    public function additions()
    {
        return $this->hasMany(SampleAcceptanceAddition::class);
    }

        
    public function labProduct()
    {
        return $this->belongsTo(LabProduct::class, 'product_id');
    }
    public function labProductParameters()
    {
        return $this->labProduct
                    ? $this->labProduct->parameters()
                    : collect();
    }

}