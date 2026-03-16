<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Uygunsuzluk extends Model
{
    protected $table = 'uygunsuzluk';

    protected $fillable = [
        'bolum','tanim','bildirim_tarihi','uy_sinif','durum','kaynak',
        'uy_aciklama','yapilacak_faaliyet','kaplam_kok','duzeltici_faaliyet',
        'tespit_eden','falliyet_sorumlu','planlanan_tarih',
        'faaliyet_degerlendirme','faaliyet_aciklama','faaliyet_kapatan','faaliyet_tarih',
        'etkinlik_degerlendirme','etkinlik_aciklama','etkinlik_kapatan','etkinlik_tarih',
        'file_name','uploaded_on'
    ];
}
