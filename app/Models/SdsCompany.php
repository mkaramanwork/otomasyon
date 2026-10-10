<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SdsCompany extends Model {
 protected $table = 'sds_companies';
 protected $fillable = ['name','address','phone','fax','email','emergency_phone','logo_path','is_active'];
 protected $casts = ['is_active' => 'boolean'];
}
