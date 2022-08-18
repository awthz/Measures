<?php

namespace Classes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $full_path
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property User $user
 * @method static YommDownloadLinkAudit create(array $array)
 */
class YommDownloadLinkAudit extends Model
{
    /**
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * The user model.
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'ID');
    }
}
