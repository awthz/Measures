<?php

namespace Classes\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $table = 'yomm_assessment';

    protected $primaryKey = 'assessment_id';

    public function yp()
    {
        return $this->belongsTo(YP::class, 'visitor_id');
    }

    public function getYP()
    {
        return $this->yp()->first();
    }

    public function getAssessor1()
    {
        return $this->belongsTo(Assessor::class, 'assessor1')->first();
    }

    public function getAssessor2()
    {
        return $this->belongsTo(Assessor::class, 'assessor2')->first();
    }

    public function getProgramme()
    {
        return $this->belongsTo(Programme::class, 'programme_id')->first();
    }

    public function getProgress()
    {
        return $this->belongsTo(Progress::class, 'progress_id')->first();
    }

    public function getRegion()
    {
        return $this->belongsTo(Region::class, 'region_id')->first();
    }

    public function getTitleAttribute()
    {
        return $this->assessment_title;
    }

    public function getRiskFullname(): ?string
    {
        switch ((int) $this->over_all_risk) {
            case 0:
                return 'Missed assessment';
                break;
            case 1:
                return 'Seriously at risk';
                break;
            case 2:
                return 'At risk';
                break;
            case 3:
                return 'Ok';
                break;
            case 4:
                return 'Good';
                break;
            case 5:
                return 'Thriving';
                break;
            default:
                return null;
                break;
        }
    }

    public function getRiskId(): ?string
    {
        switch ((int) $this->over_all_risk) {
            case 0:
                return 'missed';
                break;
            case 1:
                return 'satrisk';
                break;
            case 2:
                return 'atrisk';
                break;
            case 3:
                return 'ok';
                break;
            case 4:
                return 'good';
                break;
            case 5:
                return 'thriving';
                break;
            default:
                return null;
                break;
        }
    }
}
