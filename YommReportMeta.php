<?php

namespace Classes\Models;

use Classes\Models\YommReport;
use Illuminate\Database\Eloquent\Model;

class YommReportMeta extends Model
{
    protected $table = 'yomm_reports_meta';

    protected $guarded = ['id'];

    public function report()
    {
        return $this->belongsTo(YommReport::class, 'report_id');
    }
}
