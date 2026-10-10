<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SdsSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'sds_document_id',
        'section_no',
        'subsection_no',
        'section_code',
        'title',
        'generated_content',
        'final_content',
        'structured_data',
        'source_data',
        'generation_type',
        'is_manually_edited',
        'is_complete',
        'requires_review',
        'last_edited_by',
        'last_edited_at',
        'review_note',
    ];

    protected $casts = [
        'structured_data' => 'array',
        'source_data' => 'array',

        'is_manually_edited' => 'boolean',
        'is_complete' => 'boolean',
        'requires_review' => 'boolean',

        'last_edited_at' => 'datetime',
    ];

    public function document()
    {
        return $this->belongsTo(
            SdsDocument::class,
            'sds_document_id'
        );
    }

    public function lastEditedBy()
    {
        return $this->belongsTo(User::class, 'last_edited_by');
    }
}