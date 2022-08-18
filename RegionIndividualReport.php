<?php
namespace Classes\Models;

use Illuminate\Database\Eloquent\Model;

class RegionIndividualReport extends Model
{
    protected $table = 'yomm_region_individual_report';

    protected $casts = [
        'id'        => 'int',
        'region_id' => 'int',
        'report_id' => 'int',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function getDropDown()
    {
        $region = Region::getCurrent();
        if (!$region) {
            throw new \Exception('No region was found.');
        }

        $reports = [];
        $results = RegionIndividualReport::where('region_id', $region->id)
            ->where('visible', true)
            ->orderBy('order', 'ASC')
            ->with(['report', 'region']);

        foreach ($results as $res) {
            if (!$res->report->visible) {
                continue;
            }

            $reports[] = (object) [
                'value'     => $res->report->text_id,
                'text'      => $res->report->name,
                'selected'  => false,
                'disabled'  => false,
            ];
        }
    }

    public function report()
    {
        return $this->belongsTo(IndividualReport::class);
    }

    public function getReport()
    {
        return $this->report()->first();
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function getRegion()
    {
        return $this->region()->first();
    }
}
