<?php

namespace Classes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class YommOtherServicesInvolved
 * @package Classes\Models
 * @property int $id
 * @property int $referral_id
 * @property string|null $service_name
 * @property bool $yes
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $email
 * @property string|null $role
 * @property string|null $phone
 * @property string|null $note
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property YommPortalReferral $referral
 */
class YommReferralPortalService extends Model
{
    /**
     * @var string[]|bool
     */
    protected $guarded = ['id'];

    public function referral(): belongsTo
    {
        return $this->belongsTo(YommPortalReferral::class, 'referral_id');
    }
}
