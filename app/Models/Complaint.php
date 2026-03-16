<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model {
    protected $fillable = [
        'complaint_no','received_date','subject','description','owner','phone','email',
        'received_by','product_id','user_id','evaluation','evaluation_date','evaluated_by',
        'result','dif_opened','dif_description','feedback','status'
    ];
    public function files(){ return $this->hasMany(ComplaintFile::class); }
    public function actions(){ return $this->hasMany(ComplaintAction::class); }
    public function product(){ return $this->belongsTo(Product::class); }
    public function user(){ return $this->belongsTo(User::class); }
}

