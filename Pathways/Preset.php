<?php

namespace Classes\Models\Pathways;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;
use Classes\Models\Region as YommRegion;

class Preset extends Model
{
    protected $table = 'pathways_preset';

    public function scope()
    {
        return $this->belongsTo(Scope::class, 'scope_id');
    }

    public function getScopeAttribute()
    {
        return $this->scope()->first();
    }

    public function section()
    {
        return $this->hasOne(Section::class, 'preset_id')
            ->orderBy('order', 'ASC');
    }

    public function getSectionsAttribute()
    {
        return $this->section()->get();
    }

    public function updateRatings($filter_id)
    {
        $activity_ids = Section::join('pathways_group AS pg', 'pg.section_id', '=', 'pathways_section.id')
            ->join('pathways_activity AS pa', 'pa.group_id', '=', 'pg.id')
            ->where('preset_id', $this->id)
            ->get(['pa.id', 'pathways_section.id AS section_id']);

        $tmp = [];
        $info = [];
        foreach ($activity_ids as $id) {
            $tmp[$id->id] = $id->id;
            $info[$id->id] = $id;
        }

        $activities = Activity::whereIn('id', $tmp)->get();
        $now = new \DateTime;

        $previous = null;
        foreach ($activities as $activity) {
            $rating = $activity->getRating($filter_id);

            if (!$rating) {
                $section = Section::find($info[$activity->id]->section_id);
                $preset = $section->preset;

                $due = $activity->getDueDate($section, $preset, $filter_id);
                if (!$due) {
                    continue;
                }

                if ($due < $now) {
                    $rating = new Rating;
                    $rating->activity_id = $activity->id;

                    if ($previous) {
                        $rating->user_id = $previous->getRating($filter_id)->user_id;
                    } elseif ($this->user_id) {
                        $rating->user_id = $this->user_id;
                    } else {
                        $rating->user_id = get_current_user_id();
                    }

                    $rating->filter_id = $filter_id;
                    $rating->rating = 1;
                    $rating->save();

                    $rating->created_at = $due->format('Y-m-d H:i:s');
                    $rating->save();
                }
            } else {
                $previous = $activity;
            }
        }
    }

    public function canUserEdit(): bool
    {
        return ($this->user_id == get_current_user_id() && $this->isUserAllowed());
    }

    public function isUserAllowed(): bool
    {
        $vis = $this->scope->visibility;
        $current_region = YommRegion::current()->id;

        // Validate permission conditions.
        if ($vis === 'everyone') {
            // Restrict, this should only be done with migrations.
        } elseif ($vis === 'region') {
            // If the region is set an I am not in that region don't show it.
            if ($this->region_id && $current_region != $this->region_id) {
                return false;
            } elseif ($this->region_id && $this->region_id == $current_region) {
                // If the region is set an they are in this region allow them.
                return true;
            }

            // Check if the user is in this region.
            $query = DB::table('wp_users AS u')
                ->join('yomm_assessor AS ya', 'ya.user_id', '=', 'u.ID')
                ->join('yomm_region AS yr', 'yr.id', '=', 'ya.region');

            $region = $query;
            $thisuser = $query;
            $region = $region->where('u.ID', $this->user_id)
                ->select(['yr.id'])
                ->first()
                ->id;
            $thisuser->where('u.ID', get_current_user_id())
                ->where('yr.id', $region);

            // Check if the current user is in that programme.
            if ($thisuser->count() < 1) {
                return false;
            }
        } elseif ($vis === 'programme') {
            // They still must be in this region.
            if ($this->region_id && $current_region != $this->region_id) {
                return false;
            }

            // Check if the user is in this programme.
            // This doesn't include the text 'all'.
            $query = DB::table('wp_users AS u')
                ->join('yomm_assessor AS ya', 'ya.user_id', '=', 'u.ID')
                ->join('yomm_programmes AS yp', function ($q) {
                    $q->on(DB::raw('FIND_IN_SET(yp.programme_id, ya.programmes)'), '=', DB::raw(1));
                });

            // If the assessors programmes is = 'all'.
            $all = DB::table('yomm_assessor AS ya')
                ->join('yomm_region AS yr', 'yr.id', '=', 'ya.region')
                ->join('yomm_programmes AS yp', function ($q) {
                    $q->on(DB::raw('FIND_IN_SET(yr.id, yp.region)'), '=', DB::raw(1));
                })
                ->where('ya.programmes', 'all');

            $regionprogrammes = clone $all;
            $regionprogrammes = $regionprogrammes->where('yr.id', $this->region_id)
                ->get(['yp.programme_id']);

            $programmes = $query;
            $thisuser = $query;
            $programmes = $programmes->where('u.ID', $this->user_id)
                ->where('yp.programme_id', '<>', null)
                ->get(['yp.programme_id']);

            $tmp = [];
            foreach ($programmes as $programme) {
                $tmp[$programme->programme_id] = $programme->programme_id;
            }

            foreach ($regionprogrammes as $a) {
                $tmp[$a->programme_id] = $a->programme_id;
            }

            $thisuser->where('u.ID', get_current_user_id())
                ->whereIn('yp.programme_id', $tmp);

            $thisregion = $all->where('ya.user_id', get_current_user_id())
                ->whereIn('yp.programme_id', $tmp);

            // Check if the current user is in that programme.
            if ($thisuser->count() < 1 && $thisregion->count() < 1) {
                return false;
            }
        } elseif ($vis === 'user') {
            if ($this->region_id && $this->region_id != $current_region) {
                return false;
            }

            // Check if it is this user.
            if ($this->user_id != get_current_user_id()) {
                return false;
            }
        }

        return true;
    }

    public static function getAllowedPresetsDropDown()
    {
        $all = Preset::all();
        $dd = [];

        foreach ($all as $preset) {
            if ($preset->isUserAllowed()) {
                $dd[] = (object) [
                    'value'     => $preset->id,
                    'text'      => sprintf(
                        '‘%s‘ - created %s',
                        $preset->name,
                        \DateTime::createFromFormat('Y-m-d H:i:s', $preset->created_at)
                            ->format('d.m.y')
                    ),
                ];
            }
        }

        return $dd;
    }

    public static function &create(string $name, $scope, int $user_id = null, int $region_id = null): Preset
    {
        // Get the scopes id.
        if ($scope instanceof Scope) {
            $scope = $scope->id;
        } elseif (!is_int($scope)) {
            throw new \InvalidArgumentException('The scope must be an instance of Scope or an integer.');
        }

        try {
            $current_region = YommRegion::current();
            if ($current_region) {
                $region_id = $current_region->id;
            }            
        } catch (\Throwable $th) {

        }

        if (!$region_id){
            throw new \InvalidArgumentException('the region must be set.');
        }

        $preset = new Preset;
        $preset->name = $name;
        $preset->scope_id = $scope;
        $preset->user_id = $user_id;
        $preset->region_id = $region_id;
        $preset->save();

        return $preset;
    }

    /**
     * This creates a preset from an existing preset template.
     *
     * @param Preset|int $preset The preset to clone.
     *
     * @return Preset The newly created preset.
     */
    public static function &createFromExisting($preset): Preset
    {
        if (is_int($preset)) {
            $preset = Preset::find($preset);

            if (!$preset) {
                throw new \Exception('The preset could not be found.');
            }
        } elseif (!($preset instanceof Preset)) {
            throw new \InvalidArgumentException('The preset must implement Preset or be an integer.');
        }

        // Clone the preset.
        $npreset = Preset::create($preset->name, $preset->scope_id, get_current_user_id(), $preset->region_id);

        foreach ($preset->sections as $section) {
            $nsection = Section::create($section->title, $npreset, $section->order);

            foreach ($section->groups as $group) {
                $ngroup = Group::create($group->title, $nsection, $group->order);

                foreach ($group->activities as $activity) {
                    $nactivity = Activity::create($activity->name, $ngroup, $activity->order);
                }
            }
        }

        return $npreset;
    }
}
