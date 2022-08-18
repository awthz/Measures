<?php

namespace Classes\Models;

use Classes\Models\YommAddressList;
use Illuminate\Database\Eloquent\Model;

class YommAddressCountry extends Model
{
    protected $guarded = ['id'];

    public function addresses()
    {
        return $this->hasMany(YommAddressList::class, 'country_id');
    }
}
