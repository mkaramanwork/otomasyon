<?php
namespace App\Models;   

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrientationPlan extends Model
{
    protected $fillable = ['personnel_id','department','purpose','evaluation_date','approval_date','is_completed'];

    public function personnel() {
        return $this->belongsTo(Personnel::class);
    }

    public function activities() {
        return $this->hasMany(OrientationActivity::class);
    }
}



