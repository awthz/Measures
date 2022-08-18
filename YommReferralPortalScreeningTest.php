<?php


namespace Classes\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class YommReferralPortalScreeningTest
 * @package Classes\Models
 * @property int $id
 * @property int $referral_id
 * @property int $assessor_id
 * @property array $answer
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property YommPortalReferral $referral
 * @property Assessor $assessor
 */
class YommReferralPortalScreeningTest extends Model
{
    protected $casts = [
        'answer' => 'array',
    ];

    protected $guarded = ['id'];

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(Assessor::class, 'assessor_id');
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(YommPortalReferral::class, 'referral_id');
    }
}
