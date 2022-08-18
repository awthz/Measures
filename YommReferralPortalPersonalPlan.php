<?php

namespace Classes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class YommReferralPortalPersonalPlan
 * @package Classes\Models
 * @property int $id
 * @property int $referral_id
 * @property Carbon $appearance_datetime
 * @property string $appearance_comment
 * @property int $appearance_location_id
 * @property Carbon $scheduled_datetime
 * @property string $scheduled_comment
 * @property Carbon $koti_datetime
 * @property string $koti_comment
 * @property int $koti_location_id
 * @property int $plan_type_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property YommPortalReferral $referral
 * @property RegionFieldValue $appearanceLocation
 * @property RegionFieldValue $kotiLocation
 * @property RegionFieldValue $planType
 */
class YommReferralPortalPersonalPlan extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'appearance_datetime' => 'datetime',
        'scheduled_datetime' => 'datetime',
        'koti_datetime' => 'datetime',
    ];

    /**
     * @return BelongsTo
     */
    public function referral(): BelongsTo
    {
        return $this->belongsTo(YommPortalReferral::class, 'referral_id');
    }

    /**
     * @return BelongsTo
     */
    public function appearanceLocation(): BelongsTo
    {
        return $this->belongsTo(RegionFieldValue::class, 'appearance_location_id');
    }

    /**
     * @return BelongsTo
     */
    public function kotiLocation(): BelongsTo
    {
        return $this->belongsTo(RegionFieldValue::class, 'koti_location_id');
    }

    /**
     * @return BelongsTo
     */
    public function planType(): BelongsTo
    {
        return $this->belongsTo(RegionFieldValue::class, 'plan_type_id');
    }
}
