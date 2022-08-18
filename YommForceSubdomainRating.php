<?php

namespace Classes\Models;

use Classes\Models\Region;
use Classes\Models\Programme;
use Classes\Models\AssessmentCate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class YommForceSubdomainRating extends Model
{
    protected $fillable = ['subdomain_id', 'programme_id', 'region_id'];

    public function subdomain(): BelongsTo
    {
        return $this->belongsTo(AssessmentCate::class, 'subdomain_id', 'cate_id');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class, 'programme_id', 'programme_id');
    }
}
