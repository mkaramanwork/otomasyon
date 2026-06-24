<?php
namespace App\Models;   

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;

class Additive extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'unit', 'stock_kg', 'added_by'];

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
