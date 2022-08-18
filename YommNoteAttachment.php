<?php

namespace Classes\Models;

use Carbon\Carbon;
use Classes\FileAttachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class YommNoteAttachment
 * @package Classes\Models
 * @property int $id
 * @property int $note_id
 * @property string $file
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property YommNote $note
 */
class YommNoteAttachment extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    /**
     * Relationship to the note that this is attached to.
     * @return BelongsTo
     */
    public function note(): BelongsTo
    {
        return $this->belongsTo(YommNote::class, 'note_id')->withTrashed();
    }

    /**
     * Convert the model to the container class.
     * @return FileAttachment
     */
    public function toContainerClass(): FileAttachment
    {
        return new FileAttachment($this->file, $this->name, $this->getKey());
    }
}
