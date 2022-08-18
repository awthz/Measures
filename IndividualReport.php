<?php
namespace Classes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;

class IndividualReport extends Model
{
    protected $table = 'yomm_individual_report';

    protected $casts = [
        'id'        => 'int',
        'visible'   => 'bool',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function regions()
    {
        $this->hasMany(Region::class);
    }

    public function getRegions()
    {
        return $this->regions()->get();
    }

    public static function canCreate(string $report): bool
    {
        return DB::table('yomm_individual_report AS yir')
            ->join('yomm_region_individual_report AS yrir', 'yrir.report_id', '=', 'yir.id')
            ->where('yir.text_id', $report)
            ->where('yrir.region_id', Region::current()->id)
            ->limit(1)
            ->count() === 1;
    }
}
