<?php

namespace Classes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class YommReferralPortalConsentDocument extends Model
{
    protected $guarded = ['id'];

    public function referral(): BelongsTo
    {
        return $this->belongsTo(YommPortalReferral::class, 'referral_id');
    }
}
