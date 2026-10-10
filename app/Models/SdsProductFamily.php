<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SdsProductFamily extends Model {
 protected $table = 'sds_product_families';
 protected $fillable = ['name','code','defaults','is_active'];
 protected $casts = ['defaults' => 'array','is_active' => 'boolean'];
}
