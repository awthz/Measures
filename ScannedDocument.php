<?php

namespace Classes\Models;

use Illuminate\Database\Eloquent\Model;

class ScannedDocument extends Model
{
    protected $table = 'yomm_scanned_documents';

    protected static $_table = 'yomm_scanned_documents';

    protected $casts = [
        'id'            => 'integer',
        'document_type' => 'integer',
        'user_id'       => 'integer',
        'region_id'     => 'integer',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function type()
    {
        return $this->hasOne(RegionFieldValue::class, 'id', 'document_type');
    }

    public function getDocumentType()
    {
        return $this->type()->first();
    }

    /**
     * This is used for legacy queries which were using region rather
     * than region_id.
     *
     * @return int
     */
    public function getRegionAttribute(): int
    {
        return $this->region_id;
    }

    /**
     * Get the query builder for all viewable documents for
     * the currently logged in user. This includes all upload
     * documents for their region.
     *
     * @param Builder|null The query builder.
     *
     * @return Builder The query builder.
     */
    public static function &viewable($qb = null)
    {
        if (!$qb) {
            $qb = ScannedDocument::query();
        }

        // Get the region ID of the current assessor.
        $region_id = Region::join('yomm_assessor AS ya', 'ya.region', '=', 'yomm_region.id')
            ->where('ya.user_id', get_current_user_id())
            ->first()
            ->region;

        // Get all documents from that region.
        // Joins.
        $qb->leftJoin('yomm_assessor AS ya', 'ya.user_id', '=', self::$_table . '.user_id')
            ->leftJoin('yomm_region AS yr', 'yr.id', '=', 'ya.region');

        // Conditions.
        $qb->where(function ($q1) use ($region_id) {
            $q1->where(function ($q) use ($region_id) {
                // When user_id is null check if the current user is in the region.
                $q->where(self::$_table . '.user_id', null)
                    ->where(self::$_table . '.region_id', $region_id);
            })->orWhere(function ($q) use ($region_id) {
                // When user_id isn't null, check that the user_id is the current user.
                // Also make sure that they are in the same region.
                $q->where(self::$_table . '.user_id', '<>', null)
                    ->where(self::$_table . '.user_id', get_current_user_id())
                    ->where(self::$_table . '.region_id', $region_id);
            });
        });

        return $qb;
    }

    public static function &deletable($qb = null)
    {
        if (!$qb) {
            $qb = ScannedDocument::query();
        }

        return $qb->where('user_id', get_current_user_id());
    }

    public static function getViewableDocuments()
    {
        $collection = self::viewable()->get([self::$_table . '.id']);
        $ids = collect($collection->toArray())->flatten()->filter();

        return ScannedDocument::whereIn('id', $ids)->with(['type'])->get();
    }

    public function canView(): bool
    {
        $doc_region = (int) Assessor::join('yomm_region AS yr', 'yr.id', '=', 'yomm_assessor.region')
            ->where('user_id', $this->user_id)
            ->select(['yr.id'])
            ->first()
            ->id;

        $user_region = (int) Assessor::join('yomm_region AS yr', 'yr.id', '=', 'yomm_assessor.region')
            ->where('user_id', get_current_user_id())
            ->select(['yr.id'])
            ->first()
            ->id;

        // When the user_id is null allow everyone in their region to see it.
        $allowed_region = $this->user_id === null && $user_region === $this->region;

        // When it isn't then only the user can see it.
        // NOTE: Also check that they are in the same region.
        $allowed_user = $this->user_id === get_current_user_id() && (int) $this->region === $user_region;

        return (($doc_region === $user_region) && $this->user_id === null) || ($user_region === $this->region);
    }

    /**
     * Can the currently logged in user delete the upload. Only
     * the user who uploaded the file can delete it.
     *
     * @return bool
     */
    public function canDelete(): bool
    {
        return $this->user_id === get_current_user_id();
    }
}
