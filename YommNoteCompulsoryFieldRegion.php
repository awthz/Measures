<?php

namespace Classes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * YommNoteCompulsoryFieldRegion model.
 * @property int $id
 * @property int $field_id
 * @property int $region_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property YommNoteCompulsoryField $field
 * @property Region $region
 */
class YommNoteCompulsoryFieldRegion extends Model
{
    protected $guarded = ['id'];

    public function field(): BelongsTo
    {
        return $this->belongsTo(YommNoteCompulsoryField::class, 'field_id');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }
}
