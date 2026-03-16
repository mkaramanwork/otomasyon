<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessLog extends Model
{
    protected $fillable = ['action','description','user_id','sample_acceptance_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function sample() {
        return $this->belongsTo(SampleAcceptance::class,'sample_acceptance_id');
    }
}