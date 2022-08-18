<?php

namespace Classes\Models;

use Classes\Models\User;
use Illuminate\Database\Eloquent\Model;

class AlternativeAccount extends Model
{
    protected $fillable = [
        'user_id',
        'account_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'ID');
    }

    public function account()
    {
        return $this->belongsTo(User::class, 'account_id', 'ID');
    }
}
