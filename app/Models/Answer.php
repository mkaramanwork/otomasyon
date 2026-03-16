<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = ['assignment_id','question_id','selected_option','is_correct'];
    public function assignment() { return $this->belongsTo(ExamAssignment::class,'assignment_id'); }
    public function question() { return $this->belongsTo(Question::class); }

}
