<?php

namespace Classes\Models;

use DateTime;
use Classes\Models\Region;
use Classes\Models\ReferTo;
use Classes\Models\YommReport;
use Classes\Models\RegionField;
use Classes\Models\RegionFieldValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;

class YommReturnReport extends Model
{
    protected $guarded = ['id'];

    public function report()
    {
        return $this->belongsTo(YommReport::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function status()
    {
        return $this->belongsTo(RegionFieldValue::class, 'ot_status_id');
    }

    public function ethnicity()
    {
        return $this->belongsTo(RegionFieldValue::class, 'ethnicity_id');
    }

    public function gender()
    {
        return $this->belongsTo(RegionFieldValue::class, 'gender_id');
    }

    public function referral()
    {
        return $this->belongsTo(RegionFieldValue::class, 'referral_id');
    }

    public function exitReason()
    {
        return $this->belongsTo(RegionFieldValue::class, 'exit_reason_id');
    }

    public function neetStatus()
    {
        return $this->belongsTo(RegionFieldValue::class, 'neet_status_id');
    }

    public static function getSelectOptions()
    {
        $options['otStatus'] = self::getStatus('ot_status')->toArray();
        $options['neetStatus'] = self::getStatus('ot_neet_status')->toArray();
        $options['exitReason'] = self::getStatus('ot_exit_reason')->toArray();
        $options['ethnicity'] = self::getStatus('ot_ethnicity')->toArray();
        $options['gender'] = self::getStatus('ot_gender')->toArray();
        $options['referral'] = self::getStatus('ot_referral')->toArray();
        return $options;
    }

    public static function getStatus($field_name)
    {
        $field_id = RegionField::where('field_name', $field_name)->first()->id;
        return RegionFieldValue::where('field_id', $field_id)->get(['field_value','id'])->pluck('field_value','id');
    }

    public function scopeWithAll($query)
    {
        $query->with(
            'report',
            'region',
            'status',
            'ethnicity',
            'gender',
            'referral',
            'exitReason',
            'neetStatus'
        );
    }

    public static function getReferralSources(): array
    {
        return [
            'Other referral source',
            'Auckland City YJ',
            'Christchurch East YJ',
            'Christchurch West YJ',
            'Hawkes Bay YJ',
            'Manurewa YJ',
            'North Harbour YJ',
            'Otahuhu YJ',
            'Otara YJ',
            'Palmerston North YJ',
            'Papakura YJ',
            'Rotorua YJ',
            'South Canterbury/Otago YJ',
            'Southland YJ',
            'Tairawhiti YJ',
            'Taranaki YJ',
            'Tauranga YJ',
            'Te Tai Tokerau YJ',
            'Upper South YJ',
            'Waikato/Hauraki YJ',
            'Waitakere YJ',
            'Wellington YJ',
            'Whanganui YJ',
        ];
    }

    public function toObject()
    {
        $referrals = self::getReferralSources();
        $referral = $this->referral;
        $referral = $referral ? $referral->field_text : null;

        $other_referral = 'Other referral source';
        if ($referral && substr($referral, 0, 13) === 'OT YJ Site - ') {
            $referral = sprintf('%s YJ', substr($referral, 13));

            if (!in_array($referral, $referrals)) {
                $referral = $other_referral;
            }
        } elseif (substr($referral, -strlen(' YJ')) === ' YJ') {
            // New format.
            $referral = $referral;
        } elseif ($referral) {
            // Put it in the other category.
            $referral = $other_referral;
        } else {
            $referral = '';
        }

        return (object) [
            'id' => $this->getKey(),
            'visitor_id' => $this->visitor_id,
            'cyras_id' => $this->cyras_id,
            'report_id' => $this->report_id,
            'report' => $this->report->report_desc,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'ethnicity' => $this->ethnicity->field_text,
            'gender' => $this->gender->field_text,
            'dob' => $this->dob,
            'age' => DateTime::createFromFormat('Y-m-d', $this->dob)->diff(new DateTime())->y,
            'ot_status' => $this->status->field_text,
            'ot_status_id' => $this->status->id,
            'service' => $this->getOtProgrammeNames()[$this->report_id] ?? '',
            'service_code' => $this->getOtServiceCodes()[$this->report_id] ?? '',
            'referral' => $referral,
            'referral_id' => $this->referral ? $this->referral->id : null,
            'date_entered' => $this->date_entered,
            'date_exited' => $this->date_exited,
            'exit_reason' => $this->exitReason ? $this->exitReason->field_text : null,
            'completed' => $this->date_exited && ($this->exitReason === null || $this->exitReason->field_text === 'Completed programme'),
            'neet_status' => $this->neetStatus ? $this->neetStatus->field_text : null,
            'neet_status_id' => $this->neetStatus ? $this->neetStatus->id : null,
            'f2f_hours' => $this->f2f_hours,
            'f2f_hours_whanau' => $this->f2f_hours_whanau,
            'offended_reoffended' => $this->offended_count,
            'breached_sb' => $this->breached_count,
        ];
    }

    private function getOtProgrammeNames()
    {
        static $names = null;

        if (is_null($names)) {
            $names = DB::connection('default')
                ->table('yomm_reports_meta')
                ->where('meta_name', 'ot_name')
                ->get(['report_id', 'meta_value'])
                ->mapWithKeys(function ($item) {
                    return [$item->report_id => $item->meta_value];
                })
                ->toArray();
        }

        return $names;
    }

    private function getOtServiceCodes()
    {
        static $codes = null;

        if (is_null($codes)) {
            $codes = DB::connection('default')
                ->table('yomm_reports_meta')
                ->where('meta_name', 'ot_code')
                ->get(['report_id', 'meta_value'])
                ->mapWithKeys(function ($item) {
                    return [$item->report_id => $item->meta_value];
                });
        }

        return $codes;
    }
}
