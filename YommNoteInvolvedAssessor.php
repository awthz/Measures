<?php

namespace Classes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class YommNoteInvolvedAssessor
 * @package Classes\Models
 * @property int $id
 * @property int $note_id
 * @property int $assessor_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property YommNote $note
 * @property Assessor $assessor
 */
class YommNoteInvolvedAssessor extends Model
{
    /**
     * Get the note which this belongs to.
     * @return BelongsTo
     */
    public function note(): BelongsTo
    {
        return $this->belongsTo(YommNote::class, 'note_id');
    }

    /**
     * Get the assessor which this belongs to.
     * @return BelongsTo
     */
    public function assessor(): BelongsTo
    {
        return $this->belongsTo(Assessor::class, 'assessor_id', 'assessor_id');
    }
}
