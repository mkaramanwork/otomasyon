<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TetkikPlan extends Model
{
    protected $table = 'tetkik_planlari';
    protected $fillable = [
        'tetkik_no','tetkik_tarih','denetci','bolum','amac','kapsam','durum'
    ];

    public function sorular()
    {
        return $this->hasMany(TetkikSoru::class,'tetkik_plan_id');
    }
}