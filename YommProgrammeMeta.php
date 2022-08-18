<?php

namespace Classes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class YommProgrammeMeta
 * @package Classes\Models
 * @property int $id
 * @property int $programme_id
 * @property int $region_id
 * @property string $meta_type
 * @property string $meta_title
 * @property string $meta_desc
 * @property bool $status
 * @property array $meta
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Programme $programme
 * @property Region $region
 * @property YommProgrammeMeta|null $parent
 */
class YommProgrammeMeta extends Model
{
    protected $table = 'yomm_programme_meta';

    protected $casts = [
        'status' => 'bool',
        'meta' => 'array',
    ];

    protected $guarded = ['id'];

    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class, 'programme_id', 'programme_id');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(YommProgrammeMeta::class, 'parent_id')->with('parent');
    }
}
