<?php

namespace Classes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class YommNoteType
 * @package Classes\Models
 * @property int $id
 * @property string $type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Collection $notes
 */
class YommNoteType extends Model
{
    /**
     * All notes of this type.
     * @return HasMany
     */
    public function notes(): HasMany
    {
        return $this->hasMany(YommNote::class, 'type_id');
    }

    /**
     * Get the type ID from the string type.
     * @param string $type
     * @return int|null
     */
    public static function typeId(string $type): ?int
    {
        return YommNoteType::where('type', $type)->value('id');
    }
}
