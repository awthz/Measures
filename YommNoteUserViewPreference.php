<?php

namespace Classes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * YommNoteUserViewPreference model.
 * @property int $id
 * @property int $user_id
 * @property int $preference_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * */

class YommNoteUserViewPreference extends Model
{
  protected $guarded = ['id'];

  /**
   * Get the view preference from the user_id.
   * @param string $user_id
   * @return int|null
   */
  public static function getPreferenceByUserId(int $user_id): ?string
  {
      $preference_id = YommNoteUserViewPreference ::where('user_id', $user_id)->value('preference_id');
      return YommNoteViewPreference ::where('id', $preference_id)->value('preference');
  }
}