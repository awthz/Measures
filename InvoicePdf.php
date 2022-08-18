<?php

namespace Classes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $path
 * @property Carbon|null $start_at
 * @property Carbon|null $end_at
 * @property int|null $assessor_id
 * @property int|null $plan_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property User $user
 * @property Assessor|null $assessor
 * @property RegionFieldValue|null $plan
 * @method static InvoicePdf|null create(array $array)
 */
class InvoicePdf extends Model
{
    protected $guarded = ['id'];

    protected $dates = ['start_at', 'end_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(Assessor::class, 'assessor_id', 'assessor_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(RegionFieldValue::class, 'plan_id');
    }
}
