<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['question_text','option_a','option_b','option_c','option_d','correct_option','difficulty','category_id'];
    public function category() { return $this->belongsTo(Category::class); }

}
