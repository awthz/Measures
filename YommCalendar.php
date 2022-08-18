<?php

namespace Classes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class YommCalendar
 * @package Classes\Models
 * @property int $calendar_id
 * @property Carbon $date
 * @property Carbon $start_time
 * @property Carbon $end_time
 * @property int $programme_id
 * @property int $task_id
 * @property int|null $yp_id
 * @property int $assessor_id
 * @property int $region_id
 * @property Carbon $datetime
 * @property Carbon $date_updated
 * @property string $attendance
 * @property int|null $note_id
 * @property string $note_type
 * @property string|null $note
 * @property Collection $details
 */
class YommCalendar extends Model
{
    const CREATED_AT = 'datetime';
    const UPDATED_AT = 'date_updated';

    protected $table = 'yomm_calendar';

    protected $primaryKey = 'calendar_id';

    protected $dates = ['date', 'start_time', 'end_time', 'datetime', 'date_updated'];

    public function noteEntry(): BelongsTo
    {
        return $this->belongsTo(YommNote::class, 'note_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(YommCalendarExtraDetail::class, 'calendar_id', 'calendar_id');
    }
}
