<?php

namespace Classes\Models;

use Carbon\Carbon;
use Classes\Models\YP;
use Classes\Models\Region;
use Classes\Models\RegionFieldValue;
use Classes\Models\YommFtRelationship;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class YommFtMember
 * @package Classes\Models
 * @property integer $id
 * @property integer $region_id
 * @property integer $visitor_id
 * @property string $last_name
 * @property string $first_name
 * @property string $middle_name
 * @property string $preferred_name
 * @property string $NHI
 * @property string $dob
 * @property string|null $deceased_at
 * @property integer|null $sex_id
 * @property integer $gender_id
 * @property integer|null $sexuality_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property YP $visitor
 * @property Region $region
 * @property RegionFieldValue|null $sex
 * @property RegionFieldValue $gender
 * @property RegionFieldValue|null $sexuality
 * @property Collection $relationships
 */
class YommFtMember extends Model
{
    protected $guarded = ['id'];

    public function visitor()
    {
        return $this->belongsTo(YP::class, 'visitor_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function sex()
    {
        return $this->belongsTo(RegionFieldValue::class);
    }

    public function gender()
    {
        return $this->belongsTo(RegionFieldValue::class);
    }

    public function sexuality()
    {
        return $this->belongsTo(RegionFieldValue::class);
    }

    public function relationships()
    {
        return $this->hasMany(YommFtRelationship::class, 'member_1_id');
    }
}
