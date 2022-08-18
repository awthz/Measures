<?php


namespace Classes\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class YommReferralPortalSupportPerson
 * @package Classes\Models
 * @property int $id
 * @property int $referral_id
 * @property int $region_id
 * @property int $relationship_id
 * @property string $first_name
 * @property string $last_name
 * @property string $phone_number
 * @property string $comment
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property YommPortalReferral $referral
 * @property Region $region
 * @property RegionFieldValue $relationship
 */
class YommReferralPortalSupportPerson extends Model
{
    /**
     * @var string[]|bool
     */
    protected $guarded = ['id'];

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
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    /**
     * @return BelongsTo
     */
    public function relationship(): BelongsTo
    {
        return $this->belongsTo(RegionFieldValue::class, 'relationship_id');
    }
}
