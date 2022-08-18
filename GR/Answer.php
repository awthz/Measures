<?php

namespace Classes\Models\GR;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $connection = 'default';
    
    protected $table = 'gr_answer';

    public $timestamps = false;

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function getQuestion()
    {
        return $this->question()->first();
    }

    /**
     * Get the answer, always return a blank string
     * if the answer is NULL to avoid warnings and errors.
     *
     * @return string|object|array
     */
    public function getAnswer()
    {
        if (!$this->answer) {
            return '';
        }

        $answer = $this->answer;
        $decode = json_decode($answer);
        if (json_last_error() === JSON_ERROR_NONE) {
            $answer = $decode;
        }

        return $answer;
    }

    /**
     * Get the answer, always return a blank string
     * if the answer is NULL to avoid warnings and errors.
     * Strip slashes to resolve backwards compatibility
     *
     * @return string|object|array
     */
    public function getAnswerStripped()
    {
        if (!$this->answer) {
            return '';
        }

        $answer = $this->answer;
        $decode = json_decode(stripslashes($answer));
        if (json_last_error() === JSON_ERROR_NONE) {
            $answer = $decode;
        }

        return $answer;
    }    
}
