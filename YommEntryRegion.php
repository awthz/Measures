<?php

namespace Classes\Models;

use Classes\Models\YP;
use Classes\Models\Region;
use Classes\Models\YommEntryRegion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class YommEntryRegion extends Model
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'yomm_entry_region';

    /**
     * The columns which cannot be mass assigned.
     *
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * Relationship for the YP.
     *
     * @return BelongsTo
     */
    public function yp(): BelongsTo
    {
        return $this->belongsTo(YP::class, 'yp_id');
    }

    /**
     * The region for this entry.
     *
     * @return BelongsTo
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    /**
     * Check if this region can view a given type for a given YP ID.
     *
     * @param string $type
     * @param integer $yp
     * @return boolean
     */
    public static function canView(string $type, int $yp): bool
    {
        $region_id = Region::current()->getKey();

        return YommEntryRegion::where('region_id', $region_id)
            ->where('yp_id', $yp)
            ->where("meta->$type", true)
            ->limit(1)
            ->count() === 1;
    }

    /**
     * Check if this region can view a type from the YPs entry UID.
     *
     * @param string $type
     * @param string $entryuid
     * @return boolean
     */
    public static function canViewByEntryUid(string $type, string $entryuid): bool
    {
        $yp = YP::where('entryuid', $entryuid)->limit(1)->first();

        return self::canView($type, $yp->getKey());
    }
}
