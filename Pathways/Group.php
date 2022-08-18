<?php

namespace Classes\Models\Pathways;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'pathways_group';

    protected $activities_cache = [];

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function getSectionAttribute()
    {
        return $this->section()->first();
    }

    public function activity()
    {
        return $this->hasOne(Activity::class, 'group_id')
            ->orderBy('order', 'ASC');
    }

    public function getActivitiesAttribute()
    {
        if (empty($this->activities_cache)) {
            $activities = $this->activity()->get();
            $this->activities_cache = $activities;
        } else {
            $activities = $this->activities_cache;
        }

        return $activities;
    }

    public function isCompleted(int $filter_id): bool
    {
        foreach ($this->activities as $activity) {
            if ($activity->getRating($filter_id) === null) {
                return false;
            }
        }

        return true;
    }

    public function isCurrent(int $filter_id)
    {
        foreach ($this->activities as $activity) {
            if ($activity->getRating($filter_id) !== null) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add a new group to the database.
     *
     * @param string      $title   The title of the group.
     * @param int|Section $section The section which this group belongs to.
     * @param int         $order   The order of the group inside of the section. If not
     *                             defined the next available group will be used.
     *
     * @return int The newly created group.
     */
    public static function create(string $title, $section, int $order = null): Group
    {
        // Get the id of the section.
        if ($section instanceof Section) {
            $section = $section->id;
        } elseif (!is_int($section)) {
            throw new \InvalidArgumentException('The section must implement Section or be an integer.');
        }

        // Get the order if it isn't defined.
        if ($order === null) {
            $order = Group::where('section_id', $section)
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

        $group = new Group;
        $group->title = $title;
        $group->section_id = $section;
        $group->order = $order;
        $group->save();

        return $group;
    }
}
