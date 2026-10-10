<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegulatoryImportLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_type',
        'source_name',
        'regulation_profile',
        'jurisdiction',
        'source_version',
        'source_date',
        'file_name',
        'file_path',
        'file_type',
        'file_hash',
        'status',
        'total_rows',
        'new_substances',
        'updated_substances',
        'new_classifications',
        'updated_classifications',
        'unchanged_rows',
        'skipped_rows',
        'failed_rows',
        'summary_data',
        'error_data',
        'notes',
        'imported_by',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'source_date' => 'date',
        'summary_data' => 'array',
        'error_data' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function importedBy()
    {
        return $this->belongsTo(User::class, 'imported_by');
    }
}