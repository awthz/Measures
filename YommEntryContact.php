<?php

namespace Classes\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class YommEntryContact
 * @package Classes\Models
 * @property integer $id
 * @property integer $yp_id
 * @property integer $assessor_id
 * @property string $contact
 * @property string|null $contact_2
 * @property string|null $firstname
 * @property string|null $lastname
 * @property string|null $relationship
 * @property string $contact_owner
 * @property string|null $type
 * @property integer|null $support_type
 * @property boolean $preferred
 * @property boolean $safe_to_leave_msg
 * @property boolean $safe_to_call
 * @property boolean $safe_to_text
 * @property string $notes
 * @property Carbon $datetime
 * @property Carbon|null $date_updated
 * @property boolean $status
 * @property YP $yp
 * @property Assessor $assessor
 * @property RegionFieldValue $supportType
 */
class YommEntryContact extends Model
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'yomm_entry_contact';

    public $timestamps = false;

    /**
     * The columns guarded from mass assignment.
     *
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * The casts for this model.
     *
     * @var string[]
     */
    protected $casts = [
        'preferred' => 'bool',
        'safe_to_leave_msg' => 'bool',
        'safe_to_call' => 'bool',
        'safe_to_text' => 'bool',
        'datetime' => 'datetime',
        'date_updated' => 'datetime',
        'status' => 'bool',
    ];

    /**
     * The YP which this contact method belongs to.
     *
     * @return BelongsTo
     */
    public function yp(): BelongsTo
    {
        return $this->belongsTo(YP::class, 'yp_id');
    }

    /**
     * The assessor who added this contact method.
     *
     * @return BelongsTo
     */
    public function assessor(): BelongsTo
    {
        return $this->belongsTo(Assessor::class, 'assessor_id');
    }

    /**
     * The type of support.
     *
     * @return BelongsTo
     */
    public function supportType(): BelongsTo
    {
        return $this->belongsTo(RegionFieldValue::class, 'support_type');
    }
}
