<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentRevision extends Model
{
    protected $fillable = [
        'document_id','revision_no','revision_date','review_date',
        'change_note','file_path','updated_by'
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}