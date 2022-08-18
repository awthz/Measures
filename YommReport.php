<?php

namespace Classes\Models;

use Classes\Models\Region;
use Classes\Models\YommReportMeta;
use Illuminate\Database\Eloquent\Model;
use Classes\Models\YommReportsSharedUser;

class YommReport extends Model
{
    protected $dates = [
        'date',
        'end_date',
    ];

    protected $casts = [
        'status' => 'bool',
    ];

    public function owner()
    {
        return $this->belongsTo(Region::class, 'owner_regionid');
    }

    public function sharedUsers()
    {
        return $this->hasMany(YommReportsSharedUser::class);
    }

    public function meta()
    {
        return $this->hasMany(YommReportMeta::class, 'report_id');
    }
}
