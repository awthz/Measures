<?php

namespace Classes\Models\GR;

use Illuminate\Database\Eloquent\Model;

class QuestionState extends Model
{
    protected $connection = 'default';
    
    protected $table = 'gr_question_state';

    public $timestamps = false;
}
