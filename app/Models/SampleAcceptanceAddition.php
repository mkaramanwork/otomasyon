<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SampleAcceptanceAddition extends Model
{
    protected $fillable = [
        'sample_acceptance_id',
        'sample_name',
        'batch_no',
        'tank_no',              
        'reason',
        'user_id',
        'parameters',
        'nonconformity_result',
        'nonconformity_fix',
        'reason_date',
    ];

    protected $casts = [
        'parameters' => 'array',
        'reason_date' => 'datetime',
    ];

    // İlişkiler
    public function sample()
    {
        return $this->belongsTo(SampleAcceptance::class, 'sample_acceptance_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}