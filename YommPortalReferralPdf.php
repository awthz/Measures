<?php

namespace Classes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class YommPortalReferralPdf
 * @package Classes\Models
 * @property int $id
 * @property int $referral_id
 * @property string $file
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property YommPortalReferral $referral
 */
class YommPortalReferralPdf extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    /**
     * @return BelongsTo
     */
    public function referral(): BelongsTo
    {
        return $this->belongsTo(YommPortalReferral::class, 'referral_id');
    }

    /**
     * Can this user view this PDF?
     * @return bool
     */
    public function canView(): bool
    {
        $referral = $this->referral; 
        $region = Region::current()->getKey();

        return $region === $referral->region->getKey() || $region === $referral->sentTo->getKey();
    }
}
