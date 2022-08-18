<?php


namespace Classes\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo;

class YommReferralPortalRegion extends \Illuminate\Database\Eloquent\Model
{
    protected $guarded = ['id'];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function referTo(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'refer_to_id');
    }
}
