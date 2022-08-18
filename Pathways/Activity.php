<?php

namespace Classes\Models\Pathways;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;

class Activity extends Model
{
    protected $table = 'pathways_activity';

    protected $rating_cache = [];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function getGroupAttribute()
    {
        return $this->group()->first();
    }

    public function getRating(int $filter_id)
    {
        if (isset($this->rating_cache[$filter_id])) {
            $rating = $this->rating_cache[$filter_id];
            if ($rating === false) {
                $rating = null;
            }
        } else {
            $rating = $this->hasOne(Rating::class, 'activity_id')
                ->where('filter_id', $filter_id)
                ->first();

            $this->rating_cache[$filter_id] = !$rating ? false : $rating;
        }

        return $rating;
    }

    public function isCurrent(Section &$section, int $filter_id): bool
    {
        foreach ($section->groups as $group) {
            foreach ($group->activities as $activity) {
                if (!$activity->getRating($filter_id)) {
                    if ($activity->id === $this->id) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        }

        return false;
    }

    public function showAlert(Section &$section, Preset $preset, int $filter_id = null): bool
    {
        $due = $this->getDueDate($section, $preset, $filter_id);

        // If the assessment isn't due yet.
        if (!$due) {
            return false;
        }

        // x days warning.
        $now = new \DateTime;
        $due->modify("-{$this->reminder} days");

        if ($now > $due) {
            return true;
        }

        return false;
    }

    /**
     * This returns the due date of the next rating.
     *
     * @param Section $section   The section which this activity belongs to.
     * @param Group   $group     The group which this activity belongs to.
     * @param int     $filter_id The filter ID.
     * @param string  $format    The output format of the datetime.
     *
     * @return \DateTime|string|null
     */
    public function getDueDate(Section &$section, Preset $preset, int $filter_id = null, string $format = null)
    {
        $created = DB::table('gr_filter AS f')
            ->join('gr_filter_meta AS fm', 'f.id', '=', 'fm.filter_id')
            ->join('gr_filter_meta AS fm1', 'fm1.filter_id', '=', 'f.id')
            ->join('gr_filter_meta AS fm2', 'fm2.filter_id', '=', 'f.id')
            ->where('fm.key', 'start_date')
            ->where('fm1.key', 'preset_id')
            ->where('fm1.value', $preset->id)
            ->where('fm2.key', 'status')
            ->where('fm2.value', 1);

        if ($filter_id) {
            $created->where('f.id', $filter_id);
        }

        $created = $created->limit(1)
            ->select('fm.value')
            ->pluck('value')
            ->first();

        if (!$created && $filter_id) {
            $now = new \DateTime;
            DB::table('gr_filter_meta')
                ->insert([
                    [
                        'filter_id' => $filter_id,
                        'key'       => 'start_date',
                        'value'     => $now->format('Y-m-d H:i:s'),
                    ],
                ]);
            $created = $now->format('Y-m-d H:i:s');
        }

        if ($preset->block) {
            // Get the previous date.
            $previous = null;
            foreach ($section->groups as $group) {
                foreach ($group->activities as $activity) {
                    if ($activity->id === $this->id) {
                        break 2;
                    }

                    $previous = $activity;
                }
            }

            if (!$previous) {
                $previous = $this;
            }

            $date = $previous->getRating($filter_id);
            if (!$date) {
                $date = \DateTime::createFromFormat('Y-m-d H:i:s', $created);
            } else {
                $date = $date->created_at;
            }

            if ($date) {
                $date->modify("+{$this->period} days");
            } else {
                $date = false;
            }
        } else {
            $date = $created;
            $date = \DateTime::createFromFormat('Y-m-d H:i:s', $date);

            foreach ($section->groups as $group) {
                foreach ($group->activities as $idx => $activity) {
                    $date->modify("+{$activity->period} days");

                    if ($activity->id === $this->id) {
                        break 2;
                    }
                }
            }
        }

        if (!$date) {
        } elseif ($format) {
            $date = $date->format($format);
        }

        return $date;
    }

    /**
     * Create a new activity in the database.
     *
     * @param string    $name     The name of the activity.
     * @param Group|int $group    The ID or an instance of the group which this activity belongs to.
     * @param int       $period   The period of days that this activity can last.
     * @param int       $reminder The amount of days before due date that the user will be reminded.
     * @param int       $order    The order of the activity from within the group.
     *
     * @return Activity The newly created activity.
     */
    public static function create(string $name, $group, int $period, int $reminder = null, int $order = null): Activity
    {
        // Get the groups id.
        if ($group instanceof Group) {
            $group = $group->id;
        } elseif (!is_int($group)) {
            throw new \InvalidArgumentException('The group must implement Group or be an integer.');
        }

        // Get the order if it isn't defined.
        if ($order === null) {
            $order = Activity::where('group_id', $group)
                ->where('status', 1)
                ->orderBy('order', 'DESC')
                ->select(['order'])
                ->first();

            if ($order) {
                $order = $order->order + 1;
            } else {
                $order = 1;
            }
        }

        $activity = new Activity;
        $activity->name = $name;
        $activity->period = $period;

        if ($reminder) {
            $activity->reminder = $reminder;
        }

        $activity->group_id = $group;
        $activity->order = $order;
        $activity->save();

        return $activity;
    }
}
