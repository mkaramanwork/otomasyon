<?php
namespace App\Models;   

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model {
    use HasFactory;


    protected $fillable = ['tag','name','model','serial_no','purchase_date','calibration_due_at','status'];
    public function calibrations() { return $this->hasMany(Calibration::class); }
    public function maintenances() { return $this->hasMany(Maintenance::class); }
    public function iqcRecords() { return $this->hasMany(IqcRecord::class); }
    public function eqcRecords() { return $this->hasMany(EqcRecord::class); }
}


