<?php

namespace Classes\Models;

use Classes\Models\Region;
use Classes\Models\YommAddressCountry;
use Illuminate\Database\Eloquent\Model;

class YommAddressCountryRegion extends Model
{
    protected $guarded = ['id'];

    public function country()
    {
        return $this->belongsTo(YommAddressCountry::class, 'country_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }
}
