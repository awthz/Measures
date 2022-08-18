<?php

namespace Classes\Models;

use Classes\Models\Region;
use Classes\Models\YommRegionMeta;
use Classes\Models\YommRegionMetaValue;
use Illuminate\Database\Eloquent\Model;

class YommRegionMeta extends Model
{
    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'yomm_region_meta';

    /**
     * The guarded columns for mass assignment.
     *
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * All of the meta which relates to this key.
     *
     * @return HasMany
     */
    public function meta()
    {
        return $this->hasMany(YommRegionMetaValue::class, 'meta_id');
    }

    /**
     * Returns true if they can access the given technology.
     *
     * @param string $meta The technologies name.
     *
     * @return bool
     */
    public static function canAccess(string $meta): bool
    {
        $field = \Classes\Models\YommRegionMeta::where('meta_name', $meta)
            ->with(['meta' => function ($query) {
                $query->where('region_id', Region::current()->getKey())
                    ->where('value', true)
                    ->where('status', true);
            }])
            ->first();

        return $field->meta ? $field->meta->count() > 0 : false;
    }
}
