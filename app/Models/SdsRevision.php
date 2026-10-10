<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SdsRevision extends Model
{
    use HasFactory;

    protected $fillable = [
        'sds_document_id',
        'revision_no',
        'revision_code',
        'change_type',
        'change_description',
        'affected_sections',
        'old_value',
        'new_value',
        'formula_snapshot_before',
        'formula_snapshot_after',
        'classification_snapshot_before',
        'classification_snapshot_after',
        'regulatory_snapshot_before',
        'regulatory_snapshot_after',
        'reason',
        'requires_new_issue',
        'changed_by',
        'changed_at',
    ];

    protected $casts = [
        'affected_sections' => 'array',
        'old_value' => 'array',
        'new_value' => 'array',

        'formula_snapshot_before' => 'array',
        'formula_snapshot_after' => 'array',

        'classification_snapshot_before' => 'array',
        'classification_snapshot_after' => 'array',

        'regulatory_snapshot_before' => 'array',
        'regulatory_snapshot_after' => 'array',

        'requires_new_issue' => 'boolean',

        'changed_at' => 'datetime',
    ];

    public function document()
    {
        return $this->belongsTo(
            SdsDocument::class,
            'sds_document_id'
        );
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}