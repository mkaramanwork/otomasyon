<?php
namespace App\Models;   

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;
class EqcRecord extends Model {
    use HasFactory;


    protected $fillable = ['equipment_id','provider','round_no','result','z_score','action_plan'];
    public function equipment() { return $this->belongsTo(Equipment::class); }
}
