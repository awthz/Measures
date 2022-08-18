<?php

namespace Classes\Models;

use Carbon\Carbon;
use Classes\Models\Scopes\NoteCompletedScope;
use Classes\Notes\NoteProgress;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class YommNote
 * @package Classes\Models
 * @property int $id
 * @property int $progress_id
 * @property int $type_id
 * @property int $region_id
 * @property int|null $heading_id
 * @property int|null $subheading_id
 * @property string|null $note
 * @property bool $completed
 * @property Carbon $entered_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property YommNoteType $type
 * @property Region $region
 * @property Collection $attachments
 * @property Collection $restrictions
 * @property Collection $attachedTo
 * @property Collection $visitors
 * @property Collection $involvedAssessors
 * @property Collection $assessors
 * @property YommNoteProgress $progress
 * @property Assessor $primary_assessor
 * @property RegionFieldValue|null $heading
 * @property RegionFieldValue|null $subheading
 * @property Collection $actions
 * @property Collection $milestones
 * @method static Builder withRelationships()
 */
class YommNote extends Model
{
    use SoftDeletes;

    protected $casts = [
        'completed' => 'bool',
        'entered_at' => 'datetime',
    ];

    protected $guarded = ['id'];

    /**
     * Boot this model.
     */
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new NoteCompletedScope());
    }

    /**
     * The region which this note was created in.
     * @return BelongsTo
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    /**
     * Get the type of note.
     * @return BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(YommNoteType::class, 'type_id');
    }

    /**
     * All attachments attached to this note.
     * @return HasMany
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(YommNoteAttachment::class, 'note_id');
    }

    /**
     * Get a list of available be who can see this note.
     * @return HasMany
     */
    public function restrictions(): HasMany
    {
        return $this->hasMany(YommNoteRestriction::class, 'note_id');
    }

    /**
     * Get all YP who this note have been attached to.
     * @return HasMany
     */
    public function attachedTo(): HasMany
    {
        return $this->hasMany(YommNoteAttachedPerson::class, 'note_id');
    }

    /**
     * Get all YP who this note have been attached to.
     * @return BelongsToMany
     */
    public function visitors(): BelongsToMany
    {
        return $this->belongsToMany(
            YP::class,
            'yomm_note_attached_people',
            'note_id',
            'visitor_id'
        );
    }

    /**
     * Get the assessors who are involved in writing this note (pivot table).
     * @return HasMany
     */
    public function involvedAssessors(): HasMany
    {
        return $this->hasMany(YommNoteInvolvedAssessor::class, 'note_id');
    }

    /**
     * Get the assessors who are involved in writing this note.
     * @return BelongsToMany
     */
    public function assessors(): BelongsToMany
    {
        return $this->belongsToMany(
            Assessor::class,
            'yomm_note_involved_assessors',
            'note_id',
            'assessor_id',
            'id',
            'assessor_id'
        );
    }

    /**
     * Get the support actions.
     * @return BelongsToMany
     */
    public function actions(): BelongsToMany
    {
        return $this->belongsToMany(
            YommProgrammeMeta::class,
            'yomm_note_support_actions',
            'note_id',
            'action_id'
        );
    }

    /**
     * Get the milestone for this note.
     * @return BelongsToMany
     */
    public function milestones(): BelongsToMany
    {
        return $this->belongsToMany(
            RegionFieldValue::class,
            'yomm_note_milestones',
            'note_id',
            'milestone_id'
        );
    }

    /**
     * Get the progress which this note belongs to.
     * @return BelongsTo
     */
    public function progress(): BelongsTo
    {
        return $this->belongsTo(YommNoteProgress::class, 'progress_id');
    }

    /**
     * The heading for this note.
     * @return BelongsTo
     */
    public function heading(): BelongsTo
    {
        return $this->belongsTo(RegionFieldValue::class, 'heading_id');
    }

    /**
     * The sub heading for this note.
     * @return BelongsTo
     */
    public function subheading(): BelongsTo
    {
        return $this->belongsTo(RegionFieldValue::class, 'subheading_id');
    }

    /**
     * Get the assessor who actually entered the note.
     * @return Assessor
     */
    public function getPrimaryAssessorAttribute(): Assessor
    {
        return $this->assessors()->first();
    }

    /**
     * Load this note with all of the necessary relationships.
     * @param Builder $query
     */
    public function scopeWithRelationships(Builder $query)
    {
        $query->with('type', 'attachments', 'restrictions', 'assessors', 'visitors', 'progress.history');
    }

    /**
     * Create a basic note using the current login.
     * @param int $visitor_id
     * @param string $note
     * @param string $type
     * @return YommNote
     */
    public static function createBasicNote(int $visitor_id, string $note, string $type): YommNote
    {
        /** @var YommNote $n */
        $n = YommNote::create([
            'type_id' => YommNoteType::typeId('entry'),
            'region_id' => Region::current()->getKey(),
            'note' => $note,
            'completed' => true,
            'entered_at' => Carbon::now(),
            'progress_id' => YommNoteProgress::create()->getKey(),
        ]);

        $n->assessors()->attach(Assessor::current()->getKey());
        $n->visitors()->attach($visitor_id);

        return $n;
    }
}
