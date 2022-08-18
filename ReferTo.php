<?php
namespace Classes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;

class ReferTo extends Model
{
    public $primaryKey = 'ID';

    protected $table = 'yomm_refer_to';

    protected $casts = [
        'ID'        => 'int',
        'type_id'   => 'int',
    ];

    public $timestamps = false;

    public static function getDropDown()
    {
        $utility = new \utility;
        $region_id = $utility->getRegionID();

        $programmes = ReferTo::whereRaw('FIND_IN_SET(region, ' . $region_id . ')')
            ->get([
                'ID AS value',
                'display_name AS text',
                DB::raw('0 AS "selected"'),
                DB::raw('0 AS "disabled"'),
            ]);

        if ($programmes) {
            $programmes->prepend((object) [
                'value'     => 0,
                'text'      => 'Select an organisation',
                'selected'  => false,
                'disabled'  => false,
            ]);
            return $programmes;
        } else {
            return [];
        }
    }
}
