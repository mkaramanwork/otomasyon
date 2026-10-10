<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SdsPreparer extends Model {
 protected $table = 'sds_preparers';
 protected $fillable = ['name','title','certificate_number','issuer','issued_at','expires_at','email','is_active'];
 protected $casts = ['issued_at' => 'date','expires_at' => 'date','is_active' => 'boolean'];
}
