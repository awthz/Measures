<?php


namespace Classes\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class YommReferralPortalPersonalStatus
 * @package Classes\Models
 * @property int $id
 * @property int $referral_id
 * @property int $type_id
 * @property int $status_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property YommPortalReferral $referral
 * @property YommReferralPortalPersonalStatusType $type
 * @property RegionFieldValue $status
 */
class YommReferralPortalPersonalStatus extends Model
{
    protected $guarded = ['id'];
//    protected $date = ['started_at'];
//    protected $time = ['started_at'];

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
    public function type(): BelongsTo
    {
        return $this->belongsTo(YommReferralPortalPersonalStatusType::class, 'type_id');
    }

    /**
     * @return BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(RegionFieldValue::class, 'status_id');
    }
}
