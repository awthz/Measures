<?php

namespace Classes\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Profession
 * @package Classes\Models
 * @property int $profession_id
 * @property string|null $profession
 * @property string $description
 * @property string $profession_info
 * @property int $status
 * @property int|null $profession_type
 * @property Collection $assessors
 */
class Profession extends Model
{
    protected $table = 'yomm_profession';

    protected $primaryKey = 'profession_id';

    public function assessors()
    {
        return $this->hasMany(Assessor::class, 'profession_id', 'assessor_id');
    }
}
