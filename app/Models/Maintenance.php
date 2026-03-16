<?php
namespace App\Models;   

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;
class Maintenance extends Model {
    use HasFactory;


    protected $fillable = ['equipment_id','type','performed_at','checklist','result'];
    public function equipment() { return $this->belongsTo(Equipment::class); }
}

