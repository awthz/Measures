
<?php


namespace Classes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class YommEntryReligion
 * @package Classes\Models
 * @property int $id
 * @property int $yp_id
 * @property int $religion_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property YP $yp
 * @property RegionFieldValue $religion
 */
class YommEntryReligion extends Model
{
    public $table = 'yomm_entry_religions';

    protected $guarded = ['id'];

    /**
     * Relationship for YP.
     * @return BelongsTo
     */
    public function yp(): BelongsTo
    {
        return $this->belongsTo(YP::class, 'yp_id');
    }

    /**
     * Relationship for religion.
     * @return BelongsTo
     */
    public function religion(): BelongsTo
    {
        return $this->belongsTo(RegionFieldValue::class, 'religion_id');
    }
}
