<?php

namespace Classes\Models;

use Classes\Models\Region;
use Illuminate\Database\Eloquent\Model;

class YommReportsMothershipArea extends Model
{
    protected $casts = [
        'created_at',
        'updated_at',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
