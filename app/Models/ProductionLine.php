<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'capacity_litre',
        'description',
        'added_by'
    ];

    // Ekleyen kullanıcı
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    // Reçetelerle ilişki
    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }
}