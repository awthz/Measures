<?php

namespace Classes\Models;

use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    protected $table = 'yomm_progress';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'progress_id');
    }

    public function getAssessments()
    {
        return $this->assessments()->get();
    }

    public function getProgramme()
    {
        return $this->belongsTo(Programme::class, 'programme_id')->first();
    }

    public function getYP()
    {
        return $this->belongsTo(Entry::class, 'visitor_id')->first();
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function getRegion()
    {
        return $this->region()->first();
    }
}
