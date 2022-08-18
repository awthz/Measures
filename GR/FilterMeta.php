<?php

namespace Classes\Models\GR;

use Illuminate\Database\Eloquent\Model;

class FilterMeta extends Model
{
    protected $connection = 'default';
    
    protected $table = 'gr_filter_meta';
    
    public $timestamps = false;

    public function getFilter()
    {
        return $this->belongsTo(Filter::class, 'filter_id')->get();
    }
}
