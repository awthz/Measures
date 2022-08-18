<?php

namespace Classes\Models\GR;

use Illuminate\Database\Eloquent\Model;

class TitleMeta extends Model
{
    protected $connection = 'default';

    protected $table = 'gr_title_meta';

    public $timestamps = false;

    protected $guarded = ['id'];

    public function getTitle()
    {
        return $this->belongsTo(Title::class, 'title_id')->get();
    }
}
