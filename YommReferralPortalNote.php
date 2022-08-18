<?php

namespace Classes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * The referral portal notes tab.
 *
 * @property int $id
 * @property int $referral_id
 * @property string|null $note
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property YommPortalReferral $referral
 */
class YommReferralPortalNote extends Model
{
    protected $guarded = ['id'];

    public function referral(): BelongsTo
    {
        return $this->belongsTo(YommPortalReferral::class, 'referral_id');
    }
}
