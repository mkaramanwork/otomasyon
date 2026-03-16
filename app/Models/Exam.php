<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = ['title','category_id','question_count','created_by'];
    public function category() { return $this->belongsTo(Category::class); }
    public function questions() { return $this->belongsToMany(Question::class,'exam_questions'); }
    public function assignments() { return $this->hasMany(ExamAssignment::class); }


}
