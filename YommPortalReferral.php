<?php

namespace Classes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class YommPortalReferral
 * @package Classes\Models
 * @property int $id
 * @property int $region_id
 * @property int $sent_to_id
 * @property int $visitor_id
 * @property string|null $note
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property boolean $saved
 * @property boolean $saved_by_ngo
 * @property int $assign_to
 * @property Region $region
 * @property Region $sentTo
 * @property YP $visitor
 * @property Assessor $assessor
 * @property Collection $statuses
 * @property Collection $supportPeople
 * @property Collection $tests
 * @property Collection $personalPlans
 * @property Collection $personalStatuses
 * @property Collection $pdfs
 * @property Collection $screeningAnswers
 * @property YommReferralPortalPersonalPathway $plan
 * @property YommReferralPortalNote $referralNote
 * @property Collection $noteDocuments
 * @method static YommPortalReferral find(int $referral_id)
 */
class YommPortalReferral extends Model
{
    /**
     * @var string[]|bool
     */
    protected $guarded = ['id'];

    protected $dates = [
        'cancelled_at',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function sentTo(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'sent_to_id');
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(YP::class, 'visitor_id');
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(Assessor::class, 'assessor_id');
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(YommPortalReferralStepStatus::class, 'referral_id');
    }

    public function supportPeople(): HasMany
    {
        return $this->hasMany(YommEntryContact::class, 'yp_id', 'visitor_id');
    }

    public function tests(): HasMany
    {
        return $this->hasMany(YommReferralPortalScreeningTest::class, 'referral_id');
    }

    public function personalPlans(): HasMany
    {
        return $this->hasMany(YommReferralPortalPersonalPlan::class, 'referral_id');
    }

    public function personalStatuses(): HasMany
    {
        return $this->hasMany(YommReferralPortalPersonalStatus::class, 'referral_id');
    }

    public function pdfs(): HasMany
    {
        return $this->hasMany(YommPortalReferralPdf::class, 'referral_id');
    }

    public function screeningAnswers(): HasMany
    {
        return $this->hasMany(YommReferralPortalScreeningAnswer::class, 'referral_id');
    }

    public function plan(): HasOne
    {
        return $this->hasOne(YommReferralPortalPersonalPathway::class, 'referral_id');
    }

    public function noteDocuments(): HasMany
    {
        return $this->hasMany(YommReferralPortalNotesDocument::class, 'referral_id');
    }

    public function referralNote(): HasOne
    {
        return $this->hasOne(YommReferralPortalNote::class, 'referral_id');
    }
}
