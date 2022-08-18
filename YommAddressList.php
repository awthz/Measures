<?php

namespace Classes\Models;

use Classes\Models\YommAddressCountry;
use Illuminate\Database\Eloquent\Model;

class YommAddressList extends Model
{
    protected $table = 'yomm_address_list';

    protected $guarded = ['id'];

    public $timestamps = false;

    public function country()
    {
        return $this->hasMany(YommAddressCountry::class, 'country_id');
    }
}
