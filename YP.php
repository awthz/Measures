<?php
namespace Classes\Models;

use Carbon\Carbon;
use Classes\Models\YP;
use Classes\Models\Assessment;
use Classes\Models\YommEntryRegion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Class YP
 * @package Classes\Models
 * @property Collection $assessments
 * @property Collection $entryRegions
 * @property Collection $notes
 * @property Collection $pronouns
 * @property Carbon $date_added
 * @method static YP|null find(int $id)
 */
class YP extends Model
{
    protected $table = 'yomm_entry';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    public $dates = [
        'dob',
        'date_added',
    ];

    protected $guarded = ['ID'];

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get their NHI or secondary ID.
     *
     * @return string
     */
    public function getIdentifierAttribute(): string
    {
        $yp_id = $this->getKey();

        return once(function () use ($yp_id) {
            $id = DB::table('yomm_entry_ids')->where('yp_id', $yp_id)->first(['secondary_id', 'secondary_id_type']);

            if ($id) {
                return sprintf('%s#: %s', $id->secondary_id_type, $id->secondary_id);
            }

            return sprintf('NHI#: %s', $this->NHI);
        });
    }

    public static function findByEntryUID(string $entryuid): ?YP
    {
        return YP::where('entryuid', $entryuid)->first();
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'visitor_id');
    }

    public function entryRegions()
    {
        return $this->hasMany(YommEntryRegion::class, 'yp_id');
    }

    /**
     * Get all notes that this YP has.
     * @return BelongsToMany
     */
    public function notes(): BelongsToMany
    {
        return $this->belongsToMany(
            YommNote::class,
            'yomm_note_attached_people',
            'visitor_id',
            'note_id'
        );
    }

    public function pronouns(): HasMany
    {
        return $this->hasMany(YommEntryPronoun::class, 'yp_id');
    }

    public function getEthnicities(): array
    {
        $ids = explode(',', $this->ethnicity);
        $ethnicities = [];

        // Get ethnicities in their correct order.
        foreach ($ids as $id) {
            if (is_numeric($id)) {
                $eth = DB::table('yomm_ethnicity_2')->where('id', $id)->value('ethnicity');

                if ($eth) {
                    $ethnicities[] = $eth;
                }
            }
        }

        return $ethnicities;
    }

    public function getAddressAttribute(): string
    {
        $yp_id = $this->getKey();

        return once(function () use ($yp_id) {
            $str = '';
            $address = DB::table('yomm_entry_address')
                ->where('yp_id', $yp_id)
                ->where('status', true)
                ->first();

            if ($address) {
                $str = $address->address_1;

                if ($address->address_2) {
                    $str .= sprintf(', %s', $address->address_2);
                }

                $str .= sprintf(', %s, %s', $address->suburb, $address->city);
            }

            return $str;
        });
    }
}
