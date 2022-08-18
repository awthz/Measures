<?php


namespace Classes\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class YommReferralPortalScreeningAnswer
 * @package Classes\Models
 * @property int $id
 * @property int $question_id
 * @property int $referral_id
 * @property int $assessor_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property YommReferralPortalScreeningQuestion $question
 * @property Assessor $assessor
 * @property YommPortalReferral $referral
 */
class YommReferralPortalScreeningAnswer extends \Illuminate\Database\Eloquent\Model
{
    protected $guarded = ['id'];

    /**
     * The question that this answer is for.
     *
     * @return BelongsTo
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(YommReferralPortalScreeningQuestion::class, 'question_id');
    }

    /**
     * The assessor which this answer was created by.
     *
     * @return BelongsTo
     */
    public function assessor(): BelongsTo
    {
        return $this->belongsTo(Assessor::class, 'assessor_id');
    }

    /**
     * The referral that this answer belongs to.
     *
     * @return BelongsTo
     */
    public function referral(): BelongsTo
    {
        return $this->belongsTo(YommPortalReferral::class, 'referral_id');
    }
}
