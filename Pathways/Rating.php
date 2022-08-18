<?php

namespace Classes\Models\Pathways;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $table = 'pathways_rating';

    protected $fillable = ['filter_id', 'activity_id', 'user_id', 'rating', 'created_at'];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function getActivityAttribute()
    {
        return $this->belongsTo(Activity::class, 'activity_id')->first();
    }
}
