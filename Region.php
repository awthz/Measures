<?php

namespace Classes\Models;

use Classes\Models\Assessor;
use Classes\Models\YommAddressCountry;
use Illuminate\Database\Eloquent\Model;
use Classes\Models\YommAddressCountryRegion;

class Region extends Model
{
    protected $table = 'yomm_region';

    public $timestamps = false;

    protected $casts = [
        'id'                    => 'int',
        'aged_out'              => 'bool',
        'nhi_compulsory'		=> 'bool',
        'goals_edit'			=> 'bool',
        'backdate'				=> 'int',
        'feedback'				=> 'bool',
        '2fa_force'				=> 'bool',
        '2fa_optin'				=> 'bool',
        'assessment_reports'	=> 'bool',
        'pathways'				=> 'bool',
        'timesheet_notes'       => 'bool',
    ];

    public static function getCurrent()
    {
        return self::current();
    }

    public static function current()
    {
        return once(function () {
            $user_id = get_current_user_id();

            $assessor = Assessor::with('region')
                ->where('user_id', $user_id)
                ->first();

            if (isset($assessor->relations['region'])) {
                return $assessor->relations['region'];
            }
        });
    }

    public function assessors()
    {
        return $this->hasMany(Assessor::class, 'region');
    }

    public function countries()
    {
        return $this->belongsToMany(
            YommAddressCountry::class,
            'yomm_address_country_regions',
            'region_id',
            'country_id'
        );
    }
}
