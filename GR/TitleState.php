<?php

namespace Classes\Models\GR;

use Illuminate\Database\Eloquent\Model;

class TitleState extends Model
{
    protected $connection = 'default';
    
    protected $table = 'gr_question_state';

    protected $casts = [
        'hidden'        => 'bool',
    ];

    public function getTitle()
    {
        return $this->belongsTo(Title::class, 'title_id')->get();
    }

    public function getFilter()
    {
        return $this->belongsTo(Filter::class, 'rfid')->get();
    }

    public function toggle()
    {
        $this->hidden = !$this->hidden;
        $this->save();
    }
}
