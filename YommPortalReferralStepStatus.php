<?php

namespace Classes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class YommPortalReferralStepStatus
 * @package Classes\Models
 * @property int $id
 * @property int $referral_id
 * @property int $step_id
 * @property int $status_id
 * @property bool $viewed
 * @property string $note
 * @property YommPortalReferral $referral
 * @property YommPortalReferralStep $step
 * @property RegionFieldValue $status
 */
class YommPortalReferralStepStatus extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'viewed' => 'bool',
    ];

    public function referral(): BelongsTo
    {
        return $this->belongsTo(YommPortalReferral::class, 'referral_id');
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(YommPortalReferralStep::class, 'step_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(RegionFieldValue::class, 'status_id');
    }
}
