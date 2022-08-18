<?php

namespace Classes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class YommNoteProgress
 * @package Classes\Models
 * @property int $id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Collection $notes
 * @property Collection $history
 */
class YommNoteProgress extends Model
{
    /**
     * Get all notes for this progress.
     * @return HasMany
     */
    public function notes(): HasMany
    {
        return $this->hasMany(YommNote::class, 'progress_id')->withTrashed();
    }

    /**
     * Get only the history for this note.
     * @return HasMany
     */
    public function history(): HasMany
    {
        return $this->notes()->whereNotNull('deleted_at');
    }
}
