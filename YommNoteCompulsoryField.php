<?php

namespace Classes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * YommNoteCompulsoryField model.
 * @property int $id
 * @property string $field_name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Collection $regions
 */
class YommNoteCompulsoryField extends Model
{
    protected $guarded = ['id'];

    public function regions(): BelongsToMany
    {
        return $this->belongsToMany(
            Region::class,
            'yomm_note_compulsory_field_regions',
            'field_id',
            'region_id'
        );
    }

    /**
     * Is this field compulsory for this organisation.
     * @param string $field
     * @return bool
     */
    public static function isCompulsory(string $field): bool
    {
        return DB::table('yomm_note_compulsory_fields AS yncf')
            ->join('yomm_note_compulsory_field_regions AS yncfr', 'yncfr.field_id', '=', 'yncf.id')
            ->where('yncfr.region_id', Region::current()->getKey())
            ->where('yncf.field_name', $field)
            ->exists();
    }
}
