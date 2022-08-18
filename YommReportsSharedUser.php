<?php

namespace Classes\Models;

use Classes\Models\User;
use Classes\Models\YommReport;
use Illuminate\Database\Eloquent\Model;

class YommReportsSharedUser extends Model
{
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function report()
    {
        return $this->belongsTo(YommReport::class);
    }
}
