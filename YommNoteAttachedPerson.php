<?php

namespace Classes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class YommNoteAttachedPerson
 * @package Classes\Models
 * @property int $id
 * @property int $note_id
 * @property int $visitor_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property YommNote $note
 * @property YP $visitor
 */
class YommNoteAttachedPerson extends Model
{
    /**
     * The note which this person is attached to.
     * @return BelongsTo
     */
    public function note(): BelongsTo
    {
        return $this->belongsTo(YommNote::class, 'note_id');
    }

    /**
     * The person who this note is also attached to.
     * @return BelongsTo
     */
    public function visitor(): BelongsTo
    {
        return $this->belongsTo(YP::class, 'visitor_id');
    }
}
