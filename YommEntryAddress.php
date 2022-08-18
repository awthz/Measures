<?php

namespace Classes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $yp_id
 * @property int $assessor_id
 * @property string $address_1
 * @property string $address_2
 * @property string $suburb
 * @property string $postalcode
 * @property string $city
 * @property int $type
 * @property Carbon $datetime
 * @property Carbon $date_updated
 * @property bool $status
 * @property string $full_address
 * @property YP $client
 * @property Assessor|null $assessor
 */
class YommEntryAddress extends Model
{
    const CREATED_AT = 'datetime';
    const UPDATED_AT = 'date_updated';

    protected $table = 'yomm_entry_address';

    protected $dates = [
        'datetime',
        'date_updated',
    ];

    protected $casts = [
        'status' => 'bool',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(YP::class, 'yp_id');
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(Assessor::class, 'assessor_id');
    }

    public function getFullAddressAttribute(): string
    {
        $items = [];

        if ($this->address_1) $items[] = $this->address_1;
        if ($this->address_2) $items[] = $this->address_2;
        if ($this->suburb) $items[] = $this->suburb;
        if ($this->city) $items[] = $this->city;
        if ($this->postalcode) $items[] = $this->postalcode;

        return implode(', ', $items);
    }
}
