<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TetkikSoruListesi extends Model
{
    protected $table = 'tetkik_soru_listesi';
    protected $fillable = ['standart_maddesi','soru'];
}