<?php


namespace Classes\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class YommReferralPortalPersonalStatusType
 * @package Classes\Models
 * @property int $id
 * @property string $type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Collection $statuses
 */
class YommReferralPortalPersonalStatusType extends Model
{
    protected $guarded = ['id'];

    /**
     * @return HasMany
     */
    public function statuses(): HasMany
    {
        return $this->hasMany(YommReferralPortalPersonalStatus::class, 'type_id');
    }
}
