<?php

namespace Classes\Models\GR;

use Illuminate\Database\Eloquent\Model;

class Filter extends Model
{
    protected $connection = 'default';
    
    protected $table = 'gr_filter';

    protected $guarded = ['id'];

    public $timestamps = false;

    public function getUser()
    {
        return $this->belongsTo(User::class, 'user_id')->get();
    }

    public static function init(): Filter
    {
        return Filter::create(['user_id' => get_current_user_id()]);
    }

    public function addMeta(string $key, $value)
    {
        if (is_object($value) || is_array($value)) {
            $value = json_encode($value);
        } elseif (!is_string($value) && !is_numeric($value)) {
            throw new \InvalidArgumentException('Only objects, arrays and strings can be set as the value.');
        }

        $meta = new FilterMeta;
        $meta->filter_id = $this->id;
        $meta->key = $key;
        $meta->value = $value;
        $meta->save();

        return $this;
    }

    public function getMeta()
    {
        $meta = $this->hasMany(FilterMeta::class, 'filter_id')
            ->orderBy('id', 'asc')
            ->get();
        $tmp = [];

        foreach ($meta as $m) {
            $tmp[$m->key] = $m;
        }

        return $tmp;
    }
}