<?php
namespace App\Models;   

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calibration extends Model {
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'certificate_no',
        'provider',
        'performed_at',
        'valid_until',
        'result',
        'certificate_file' 
    ];

    public function equipment() {
        return $this->belongsTo(Equipment::class);
    }
}


