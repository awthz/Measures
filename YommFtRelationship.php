<?php

namespace Classes\Models;

use Carbon\Carbon;
use Classes\Models\YommFtMember;
use Illuminate\Database\Eloquent\Model;
use Classes\Models\YommFtRelationshipType;

/**
 * Class YommFtRelationship
 * @package Classes\Models
 * @property integer $id
 * @property integer $type_id
 * @property integer $member_1_id
 * @property integer $member_2_id
 * @property boolean $biological
 * @property string|null $martial_status
 * @property boolean $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property YommFtMember $member1
 * @property YommFtMember $member2
 * @property YommFtRelationshipType $type
 */
class YommFtRelationship extends Model
{
    protected $guarded = ['id'];

    public function member1()
    {
        return $this->belongsTo(YommFtMember::class, 'member_1_id');
    }

    public function member2()
    {
        return $this->belongsTo(YommFtMember::class, 'member_2_id');
    }

    public function type()
    {
        return $this->belongsTo(YommFtRelationshipType::class);
    }
}
