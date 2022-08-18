<?php

namespace Classes\Models;

use Classes\Models\YommReport;
use Illuminate\Database\Eloquent\Model;

class YommReturnReportFte extends Model
{
    protected $guarded = ['id'];

    public function report()
    {
        return $this->belongsTo(YommReport::class);
    }
}
