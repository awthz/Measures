<?php

namespace Classes\Models;

use Illuminate\Database\Eloquent\Model;

class JsLog extends Model
{
    protected $table = 'yomm_js_log';

    protected $casts = [
        'line'          => 'int',
        'column'        => 'int',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
