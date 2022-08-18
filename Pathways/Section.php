<?php

namespace Classes\Models\Pathways;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $table = 'pathways_section';

    protected $group_cache = [];

    public function getPresetAttribute()
    {
        return $this->belongsTo(Preset::class, 'preset_id')->first();
    }

    public function group()
    {
        return $this->hasOne(Group::class, 'section_id')
            ->orderBy('order', 'ASC');
    }

    public function getGroupsAttribute()
    {
        if (empty($this->group_cache)) {
            $this->group_cache = $this->hasOne(Group::class, 'section_id')
                ->orderBy('order', 'ASC')
                ->get();
        }

        return $this->group_cache;
    }

    /**
     * Add a new section to the database.
     * 
     * @param string     $title  The title of the section.
     * @param int|Preset $preset The preset which this section belongs to.
     * @param int        $order  The order of the section inside of the preset. If not
     *                           defined the next available preset will be used.
     * 
     * @return int The newly created section.
     */
    public static function create(string $title, $preset, int $order = null): Section
    {
        // Get the id of the preset.
        if ($preset instanceof Preset) {
            $preset = $preset->id;
        } elseif (!is_int($preset)) {
            throw new \InvalidArgumentException('The preset must implement Preset or be an integer.');
        }

        // Get the order if it isn't defined.
        if ($order === null) {
            $order = Section::where('preset_id', $preset)
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

        $section = new Section;
        $section->title = $title;
        $section->preset_id = $preset;
        $section->order = $order;
        $section->save();

        return $section;
    }
}
