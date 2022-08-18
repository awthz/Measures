<?php


namespace Classes\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class YommReferralPortalScreeningQuestion extends \Illuminate\Database\Eloquent\Model
{
    protected $guarded = ['id'];

    public function answers(): HasMany
    {
        return $this->hasMany(YommReferralPortalScreeningAnswer::class, 'question_id');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }
}
