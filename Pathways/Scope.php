<?php

namespace Classes\Models\Pathways;

use Illuminate\Database\Eloquent\Model;

class Scope extends Model
{
    protected $table = 'pathways_scope';

    const ONLY_USER = 1;
    const ONLY_REGION = 2;
    const ONLY_PROGRAMME = 4;
    const EVERYONE = 8;

    public static function getConstant(int $vis)
    {
        $qb = Scope::where('status', 1);

        switch ($vis) {
            case self::ONLY_USER:
                $qb->where('visibility', 'user');
                break;
            case self::ONLY_REGION:
                $qb->where('visibility', 'region');
                break;
            case self::ONLY_PROGRAMME:
                $qb->where('visibility', 'programme');
                break;
            case self::EVERYONE:
                $qb->where('visibility', 'everyone');
                break;
            default:
                throw new \InvalidArgumentException('Unknown constant.');
                break;
        }

        return $qb->first();
    }

    public function onlyFor(int $scope)
    {
        $vis = strtolower($this->visibility);

        switch ($scope) {
            case self::ONLY_USER:
                if ($vis === 'user') {
                    return true;
                }
                break;
            case self::ONLY_REGION:
                if ($vis === 'region') {
                    return true;
                }
                break;
            case self::ONLY_PROGRAMME:
                if ($vis === 'programme') {
                    return true;
                }
                break;
            case self::EVERYONE:
                if ($vis === 'everyone') {
                    return true;
                }
                break;
            default:
                return false;
                break;
        }
    }

    public function getVisibilityConstant()
    {
        $vis = strtolower($this->visibility);

        if ($vis === 'user') {
            return self::ONLY_USER;
        } elseif ($vis === 'region') {
            return self::ONLY_REGION;
        } elseif ($vis === 'programme') {
            return self::ONLY_PROGRAMME;
        } elseif ($vis === 'everyone') {
            return self::EVERYONE;
        }

        return false;
    }

    public function getPresetAttribute()
    {
        return $this->hasOne(Preset::class, 'scope_id')->get();
    }

    /**
     * Create a new scope variable, this should only be used in migrations.
     *
     * @param int|string The visibility of the scope, e.g. user, region, programme, everyone, ...
     *
     * @return Scope
     */
    public static function create($vis): Scope
    {
        // Get the visibility string.
        if (is_int($vis)) {
            switch ($vis) {
                case self::ONLY_USER:
                    $vis = 'user';
                    break;
                case self::ONLY_REGION:
                    $vis = 'region';
                    break;
                case self::ONLY_PROGRAMME:
                    $vis = 'programme';
                    break;
                case self::EVERYONE:
                    $vis = 'everyone';
                    break;
                default:
                    throw new \InvalidArgumentException('Unknown visibility constant supplied.');
                    break;
            }
        } elseif (!is_string($vis)) {
            throw new \InvalidArgumentException('The visibility must be an int or string.');
        }

        $scope = new Scope;
        $scope->visibility = $vis;
        $scope->save();

        return $scope;
    }
}
