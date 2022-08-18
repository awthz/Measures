<?php

namespace Classes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class YommFtRelationshipType
 * @package Classes\Models
 * @property integer $id
 * @property string $type
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property YommFtRelationship $relationships
 */
class YommFtRelationshipType extends Model
{
    protected $guarded = ['id'];

    public function relationships()
    {
        return $this->hasMany(YommFtRelationship::class);
    }
}
