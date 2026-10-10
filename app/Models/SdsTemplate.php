<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SdsTemplate extends Model {
 protected $table = 'sds_templates';
 protected $fillable = ['company_id','name','code','header_color','accent_color','footer_text','logo_path','is_active'];
 protected $casts = ['is_active' => 'boolean'];
}
