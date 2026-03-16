<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintAction extends Model {
    protected $fillable = ['complaint_id','action_description','responsible_person_id','due_date','completed_at'];
    public function complaint(){ return $this->belongsTo(Complaint::class); }
}

