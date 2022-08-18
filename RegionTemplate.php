<?php
namespace Classes\Models;

use Illuminate\Database\Eloquent\Model;

class RegionTemplate extends Model
{
    protected $table = 'yomm_region_template';

    protected $casts = [
        'id'        => 'int',
        'region_id' => 'int',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function getRegion()
    {
        return $this->region()->first();
    }

    public static function for(string $report): ?RegionTemplate
    {
        $utility = new \utility;

        return RegionTemplate::where('report', $report)
            ->where('region_id', $utility->getRegionID())
            ->first();
    }
}
