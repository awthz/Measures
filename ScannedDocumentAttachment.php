<?php
namespace Classes\Models;

use Illuminate\Database\Eloquent\Model;
use Classes\Models\GR\Filter;

class ScannedDocumentAttachment extends Model
{
    protected $table = 'yomm_scanned_document_attachment';

    protected $casts = [
        'id'            => 'integer',
        'document_id'   => 'integer',
        'filter_id'     => 'integer',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function document()
    {
        return $this->hasOne(ScannedDocument::class, 'id', 'document_id');
    }

    public function getDocument()
    {
        return $this->document()->first();
    }

    public function filter()
    {
        return $this->hasOne(Filter::class, 'id', 'filter_id');
    }

    public function getFilter()
    {
        return $this->filter()->first();
    }
}
