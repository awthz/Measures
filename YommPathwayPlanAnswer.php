<?php

namespace Classes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Class YommPathwayPlanAnswers
 * @package Classes\Models
 * @property int $id
 * @property int $activity_id
 * @property int $assessor_id
 * @property int $personal_pathway_id
 * @property int $calendar_id
 * @property JSON $answer
 * @property int $increment
 */

class YommPathwayPlanAnswer extends Model
{
  protected $guarded = ['id'];

  public static function getAnswersByPathwayId(int $pathway_id): object 
  {
    $answers = YommPathwayPlanAnswer::where('personal_pathway_id', $pathway_id)->get();
    return $answers;
  }

  public static function getActivityNameByAnswerId(int $answer_id): ?string
  {
    $activity_id = YommPathwayPlanAnswer::where('id', $answer_id)->value('activity_id');
    $activity_name = DB::table('yomm_pathway_plan_activities')->where('id', $activity_id)->value('name');
    return $activity_name; 
  }
}