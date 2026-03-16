<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAssignment extends Model
{
    protected $fillable = ['exam_id','user_id','assigned_at','completed'];
    public function exam() { return $this->belongsTo(Exam::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function answers() { return $this->hasMany(Answer::class,'assignment_id'); }

}
