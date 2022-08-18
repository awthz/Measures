<?php

namespace Classes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $referral_id
 * @property int $plan_id
 * @property string|null $facilitator
 * @property Carbon|null $started_at
 * @property bool|null $exited
 * @property int|null $exit_reason
 * @property Carbon|null $exited_at
 * @property string|null $comment
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property bool|null $travel_consented
 * @property string|null $risk_rating
 * @property int|null $sessions
 * @property YommPortalReferral $referral
 * @property RegionFieldValue $plan
 * @property RegionFieldValue $reason
 * @method static YommReferralPortalPersonalPathway find(int $id)
 * @method static YommReferralPortalPersonalPathway|null updateOrCreate(array $compact, array $compact1)
 */
class YommReferralPortalPersonalPathway extends Model
{
    protected $guarded = ['id'];

    public function referral(): BelongsTo
    {
        return $this->belongsTo(YommPortalReferral::class, 'referral_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(RegionFieldValue::class, 'plan_id');
    }

    public function reason(): BelongsTo
    {
        return $this->belongsTo(RegionFieldValue::class, 'exit_reason');
    }
}
