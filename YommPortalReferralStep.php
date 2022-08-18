<?php

namespace Classes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class YommPortalReferralStep
 * @package Classes\Models
 * @property int $id
 * @property int $region_id
 * @property int $order
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Region $region
 */
class YommPortalReferralStep extends Model
{
    protected $guarded = ['id'];

    /**
     * The regions step of referrals.
     *
     * @return BelongsTo
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }
}
