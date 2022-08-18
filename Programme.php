<?php

namespace Classes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;

class Programme extends Model
{
    protected $table = 'yomm_programmes';

    protected $primaryKey = 'programme_id';

    public function getAssessments()
    {
        return $this->hasMany(Assessment::class, 'programme_id')->get();
    }

    public function getNameAttribute()
    {
        return $this->programme_name;
    }

    public static function inRegion(int $region_id)
    {
        return Programme::whereRaw('FIND_IN_SET(' . $region_id . ', region)');
    }

    public static function getDropDown()
    {
        $utility = new \utility;
        $region_id = $utility->getRegionID();

        $programmes = Programme::whereRaw('FIND_IN_SET(' . $region_id . ', region)')
            ->get([
                'programme_id AS value',
                'programme_name AS text',
                DB::raw('0 AS "selected"'),
                DB::raw('0 AS "disabled"'),
            ]);

        if ($programmes) {
            $programmes->prepend((object) [
                'value'     => 0,
                'text'      => 'Select a programme',
                'selected'  => false,
                'disabled'  => false,
            ]);
            return $programmes;
        } else {
            return [];
        }
    }

    public static function dropdown(): array
    {
        return Programme::whereRaw('FIND_IN_SET(?, region)', Region::current()->getKey())
            ->get()
            ->map(function ($item) {
                return (object) ['value' => $item->programme_id, 'text' => $item->programme_name];
            })
            ->toArray();
    }
}
