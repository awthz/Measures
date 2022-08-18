<?php

namespace Classes\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RegionField
 * @package Classes\Models
 * @property int $id
 * @property string $field_name
 * @property string|null $meta
 * @property RegionFieldValue $fieldValue
 */
class RegionField extends Model
{
    protected $table = 'yomm_region_fields';

    public $timestamps = false;

    public function fieldValue()
    {
        return $this->belongsTo(RegionFieldValue::class, 'field_id');
    }

    public function getFieldValues()
    {
        return $this->fieldValue()->get();
    }

    public static function getByName(string $name): ?RegionField
    {
        return RegionField::where('field_name', $name)->first();
    }

    public static function getIdByName(string $name): ?int
    {
        $field = RegionField::getByName($name);

        return $field ? $field->getKey() : null;
    }
}
