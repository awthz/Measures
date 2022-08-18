<?php

namespace Classes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class YommEntryPronoun
 * @package Classes\Models
 * @property int $id
 * @property int $yp_id
 * @property int $pronoun_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property YP $yp
 * @property RegionFieldValue $pronoun
 */
class YommEntryPronoun extends Model
{
    protected $guarded = ['id'];

    /**
     * The YP this entry is attached to.
     * @return BelongsTo
     */
    public function yp(): BelongsTo
    {
        return $this->belongsTo(YP::class, 'yp_id');
    }

    /**
     * The pronoun which this YP is.
     * @return BelongsTo
     */
    public function pronoun(): BelongsTo
    {
        return $this->belongsTo(RegionFieldValue::class, 'pronoun_id');
    }
}
