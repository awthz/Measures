<?php

namespace Classes\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RegionFieldValue
 * @package Classes\Models
 * @property int $id
 * @property int $field_id
 * @property int $region_id
 * @property string $field_value
 * @property string $field_text
 * @property string $AlternativeText
 * @property int $orderid
 * @property int $groupid
 * @property object|null $meta
 * @property RegionField $field
 * @property ScannedDocument $scannnedDocument
 * @method static RegionFieldValue find(int $id)
 */
class RegionFieldValue extends Model
{
    protected $table = 'yomm_region_fields_value';

    protected $casts = [
        'meta'      => 'object',
    ];

    public $timestamps = false;

    public function field()
    {
        return $this->hasOne(RegionField::class, 'field_id');
    }

    public function getRegionField()
    {
        return $this->field()->first();
    }

    public function scannedDocument()
    {
        return $this->belongsTo(ScannedDocument::class, 'document_type');
    }

    public function getScannedDocuments()
    {
        return $this->scannedDocument()->get();
    }

    public function getDropDown($field)
    {
        if ($field instanceof RegionField) {
            $field = $field->id;
        } elseif (!is_int($field)) {
            throw new \InvalidArgumentException('The field must be the RegionField model or an ID.');
        }

        $values = RegionFieldValue::where('field_id', $field)
            ->where(function ($where) {
                $utility = new \utility;
                $region_id = $utility->getRegionID();

                $where->whereRaw("FIND_IN_SET($region_id, region_id)")
                    ->orWhere('region_id', 0);
            })
            ->orderBy('groupid', 'ASC')
            ->orderBy('orderid', 'ASC')
            ->get(['id AS value', 'field_value', 'field_text AS text', 'meta']);

        return $values;
    }
}
