<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintFile extends Model {
    protected $fillable = ['complaint_id','file_path','file_type','stage'];
    public function complaint(){ return $this->belongsTo(Complaint::class); }
}
