<?php

namespace Classes\Models;

use Carbon\Carbon;
use Classes\Api\Hashing\WpHasher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ApiToken
 * @package Classes\Models
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property string $host
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property User $user
 */
class ApiToken extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate an API token.
     * @param int $user_id
     * @return string
     */
    public static function generateToken(int $user_id): string
    {
        $hasher = new WpHasher();
        $token = self::genTokenStr();
        ApiToken::where('user_id', $user_id)->delete();
        ApiToken::create(['user_id' => $user_id, 'token' => $hasher->make($token), 'host' => $_SERVER['REMOTE_ADDR']]);

        return $token;
    }

    /**
     * Generate a random string for the token.
     * @return string
     */
    private static function genTokenStr(): string
    {
        $len = 32;
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $clen = strlen($chars);
        $str = '';

        for ($i = 0; $i < $len; $i++) {
            $str .= $chars[rand(0, $clen - 1)];
        }

        return $str;
    }
}
