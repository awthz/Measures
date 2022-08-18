<?php

namespace Classes\Models;

use Classes\Models\User;
use Illuminate\Database\Eloquent\Model;

class YommNarrativeName extends Model
{
    protected $casts = [
        'response' => 'bool',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'user_id',
        'name',
        'response',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function search(string $name)
    {
        return YommNarrativeName::where('name', $name)
            ->where('user_id', get_current_user_id())
            ->get(['name', 'response']);
    }

    public static function shouldExcludeName(string $name): bool
    {
        return self::search($name)->first()->response ?? false;
    }

    public static function getNotNames()
    {
        return YommNarrativeName::where('user_id', get_current_user_id())
            ->where('response', 0)
            ->where('counter', '>', 1)
            ->get(['name'])
            ->pluck('name');
    }

    public static function add(string $name, bool $response): YommNarrativeName
    {
        $user_id = get_current_user_id();

        $nn = YommNarrativeName::where('user_id', $user_id)
            ->where('name', $name)
            ->first();

        if (!$nn) {
            $nn = YommNarrativeName::create([
                'user_id' => $user_id,
                'name' => $name,
                'response' => $response,
            ]);
        } else {
            // Reset the counter before changing response.
            if ($nn->response !== $response) {
                $nn->counter = 0;
            }

            $nn->counter++;
            $nn->response = $response;
            $nn->save();
        }

        return $nn;
    }
}
