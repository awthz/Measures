<?php

namespace Classes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * YommNoteViewPreference model.
 * @property int $id
 * @property string $preference
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * */

class YommNoteViewPreference extends Model
{
  protected $guarded = ['id'];

  public static function getPreferenceIdByName(string $preference): int 
  {
    $preference_id = YommNoteViewPreference ::where('preference', $preference)->value('id');
    return $preference_id;
  }
}