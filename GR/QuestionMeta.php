<?php

namespace Classes\Models\GR;

use Illuminate\Database\Eloquent\Model;

class QuestionMeta extends Model
{
    protected $connection = 'default';

    protected $table = 'gr_question_meta';

    public $timestamps = false;

    protected $guarded = ['id'];

    public function getQuestion()
    {
        return $this->belongsTo(Question::class, 'question_id')->get();
    }
}
