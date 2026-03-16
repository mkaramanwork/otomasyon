<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Denetim extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',        // Doküman adı
        'category',     // Başlık (örn: yasal_statü)
        'file_path',    // PDF dosya yolu
        'description',  // Tanım
        'date',         // Tarih
        'uploaded_by',  // Ekleyen kullanıcı
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}