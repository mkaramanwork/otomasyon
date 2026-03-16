<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalDocumentRevision extends Model
{
    protected $fillable = [
        'external_document_id','revision_no','publish_date','file_path'
    ];

    public function document()
    {
        return $this->belongsTo(ExternalDocument::class);
    }
}
