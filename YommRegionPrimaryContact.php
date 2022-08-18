<?php


namespace Classes\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo;

class YommRegionPrimaryContact extends \Illuminate\Database\Eloquent\Model
{
    protected $guarded = ['id'];

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(Assessor::class, 'assessor_id');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }
}
