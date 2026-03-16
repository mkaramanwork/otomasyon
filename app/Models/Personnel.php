<?php
namespace App\Models;   

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personnel extends Model
{
    protected $table = 'personnel';
    protected $fillable = ['name','title','duty','start_date','deputy'];

    public function orientationPlans() {
        return $this->hasMany(OrientationPlan::class);
    }

    public function competencies() {
        return $this->belongsToMany(Competency::class, 'personnel_competencies')
                    ->withPivot('granted_at','level')
                    ->withTimestamps();
    }

    
}



