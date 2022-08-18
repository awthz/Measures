<?php

namespace Classes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class YommCalendarExtraDetail
 * @package Classes\Models
 * @property int $id
 * @property int $calendar_id
 * @property int $region_field_id
 * @property int $field_value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property YommCalendar $calendar
 * @property RegionFieldValue $value
 * @property RegionField $field
 */
class YommCalendarExtraDetail extends Model
{
    /**
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * @return BelongsTo
     */
    public function calendar(): BelongsTo
    {
        return $this->belongsTo(YommCalendar::class, 'calendar_id', 'calendar_id');
    }

    /**
     * @return BelongsTo
     */
    public function value(): BelongsTo
    {
        return $this->belongsTo(RegionFieldValue::class, 'field_value');
    }

    /**
     * @return BelongsTo
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(RegionField::class, 'region_field_id');
    }
}
