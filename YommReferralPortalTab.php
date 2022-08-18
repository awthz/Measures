<?php

namespace Classes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class YommReferralPortalTab extends Model
{
    protected $guarded = ['id'];

    /**
     * The region which this belongs to.
     * @return BelongsTo
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }
}
