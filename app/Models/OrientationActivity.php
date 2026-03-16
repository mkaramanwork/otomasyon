<?php
namespace App\Models;   

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrientationActivity extends Model
{
    protected $fillable = ['orientation_plan_id','activity','trainer','start_date','end_date','result'];

    public function plan() {
        return $this->belongsTo(OrientationPlan::class);
    }
}



