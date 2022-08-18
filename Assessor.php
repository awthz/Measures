<?php

namespace Classes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Assessor
 * @package Classes\Models
 * @property int $assessor_id
 * @property int $user_id
 * @property string $assessor_firstname
 * @property string $assessor_lastname
 * @property string $assessor_title
 * @property Region $region
 * @property string $programmes
 * @property string|null $contracts
 * @property int $profession_id
 * @property int $admin
 * @property string $status
 * @property string $full_name
 * @property string $first_name
 * @property string $last_name
 * @property string $title
 * @property User $user
 * @property Profession $profession
 * @property string|null $phone
 * @property string|null $email_signature
 */
class Assessor extends Model
{
    protected $table = 'yomm_assessor';

    protected $primaryKey = 'assessor_id';

    public function getFullNameAttribute(): string
    {
        return sprintf('%s %s', $this->first_name, $this->last_name);
    }

    public function getFirstNameAttribute(): string
    {
        return $this->assessor_firstname;
    }

    public function getLastNameAttribute(): string
    {
        return $this->assessor_lastname;
    }

    public function getTitleAttribute(): string
    {
        return $this->assessor_title;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getUser()
    {
        return $this->hasOne(User::class, 'id')->get();
    }

    public function profession(): BelongsTo
    {
        return $this->belongsTo(Profession::class, 'profession_id');
    }

    public function getAssessments()
    {
        return $this->hasMany(Assessment::class)->get();
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region');
    }

    public function assessorsRegion()
    {
        return $this->region();
    }

    public function getRegion()
    {
        return $this->region()->first();
    }

    public static function current(): Assessor
    {
        $utility = new \utility;
        $assessor_id = $utility->getAssessorID(get_current_user_id());

        return Assessor::find($assessor_id);
    }

    public static function dropdown(): array
    {
        return Assessor::where('region', Region::current()->getKey())
            ->get()
            ->map(function ($item) {
                return (object) ['value' => $item->assessor_id, 'text' => $item->full_name];
            })
            ->toArray();
    }
}
