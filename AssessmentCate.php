<?php

namespace Classes\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentCate extends Model
{
    protected $table = 'yomm_assessment_cate';

    protected $primaryKey = 'cate_id';

    public $timestamps = false;

    protected $casts = [
        'cate_meta' => 'object',
        'colour' => 'array',
    ];
}
