<?php
namespace App\Models;   

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competency extends Model
{
    protected $fillable = ['method','description'];

    public function personnel() {
        return $this->belongsToMany(Personnel::class, 'personnel_competencies')
                    ->withPivot('granted_at','level')
                    ->withTimestamps();
    }
}



