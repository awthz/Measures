<?php

namespace Classes\Models;

use Classes\Models\Region;
use Classes\Models\YommRegionMeta;
use Illuminate\Database\Eloquent\Model;

class YommRegionMetaValue extends Model
{
    /**
     * The name of this table.
     *
     * @var string
     */
    protected $table = 'yomm_region_meta_value';

    /**
     * The columns which are guarded for mass assignment.
     *
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * The type of meta.
     *
     * @return BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(YommRegionMeta::class, 'meta_id');
    }

    /**
     * The relationship for the region which this belongs to.
     *
     * @return BelongsTo
     */
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }
}
