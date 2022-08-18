<?php

namespace Classes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Class YommNoteRestriction
 * @package Classes\Models
 * @property int $id
 * @property int $note_id
 * @property string $accessible_type
 * @property int $accessible_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property YommNote $note
 * @property Profession|Assessor $accessible
 */
class YommNoteRestriction extends Model
{
    /**
     * Guarded columns.
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * The note which this belongs to.
     * @return BelongsTo
     */
    public function note(): BelongsTo
    {
        return $this->belongsTo(YommNote::class, 'note_id');
    }

    /**
     * The accessible group which this note can be viewed by: profession or assessor.
     * @return MorphOne
     */
    public function accessible(): MorphOne
    {
        return $this->morphOne('accessible');
    }
}
