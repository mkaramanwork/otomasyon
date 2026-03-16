<?php
namespace App\Models;   

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;
class IqcRecord extends Model {
    use HasFactory;


    protected $fillable = ['equipment_id','date','material','value','rule_status','note'];
    public function equipment() { return $this->belongsTo(Equipment::class); }
}


