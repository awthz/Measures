<?php

namespace Classes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class YommOtherService
 * @package Classes\Models
 * @property int $id
 * @property int $yp_id
 * @property int $service_id
 * @property string $service_name
 * @property int $assessor_id
 * @property Carbon $start_date
 * @property Carbon $created_date
 * @property Carbon $exit_date
 * @property char $type
 * @property text $note
 * @property text $file
 * @property text $filename
 * @property int $statuses
 * @property int $region_id
 * @property varchar $programme
 * @property YP $visitor
 */
class YommOtherService extends Model
{
    /**
     * @var string[]|bool
     */
    protected $guarded = ['id'];

    protected $table = 'yomm_other_service';
    public $timestamps = false;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'start_date',
        'created_date',
        'exit_date'
    ];

    public function visitor(): belongsTo
    {
        return $this->belongsTo(YP::class, 'yp_id');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(Assessor::class, 'assessor_id', 'assessor_id');
    }
}
