<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TetkikSoru extends Model
{
    protected $table = 'tetkik_sorulari';
    protected $fillable = [
        'tetkik_plan_id','standart_maddesi','soru',
        'delil','uygunsuzluk_var','uygunsuzluk_aciklama'
    ];

    public function plan()
    {
        return $this->belongsTo(TetkikPlan::class,'tetkik_plan_id');
    }
}