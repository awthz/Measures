<?php


namespace Classes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class YommLog
 * @package Classes\Models
 * @property int $ID
 * @property int $user_id
 * @property string $action
 * @property Carbon $date
 * @property string $note
 * @property string|null $type
 * @property string|null $value
 * @property string|null $value_prev
 * @property string $ip
 * @property User $user
 */
class YommLog extends Model
{
    /**
     * @var string[] Guarded columns.
     */
    protected $guarded = ['id'];

    /**
     * The user model.
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Add a log entry.
     * @param string $action
     * @param string $note
     * @param string|null $type
     * @param string|null $value
     * @param string|null $value_prev
     * @return YommLog|null
     */
    public static function log(
        string $action,
        string $note,
        string $type = null,
        string $value = null,
        string $value_prev = null
    ): ?YommLog {
        try {
            $user_id = User::current()->getKey();
            $date = new \DateTime('now', new \DateTimeZone('Auckland/Pacific'));
            $ip = $_SERVER['REMOTE_ADDR'];
        } catch (\Throwable $e) {
            return null;
        }

        return YommLog::create(compact('action', 'note', 'type', 'value', 'value_prev', 'ip', 'date', 'user_id'));
    }

    /**
     * Log an exception.
     * @param string $action
     * @param \Throwable $e
     * @return YommLog|null
     */
    public static function exception(string $action, \Throwable $e): ?YommLog
    {
        YommLog::log($action, $e->getMessage(), null, $e->getTraceAsString());
    }
}
