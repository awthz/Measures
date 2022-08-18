<?php

namespace Classes\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Class NoteCompletedScope
 * @package Classes\Models\Scopes
 * @method static static|Builder|\Illuminate\Database\Query\Builder withIncomplete()
 */
class NoteCompletedScope implements Scope
{
    /**
     * All of the extensions to be added to the builder.
     * @var string[]
     */
    protected $extensions = ['WithIncomplete'];

    /**
     * @var bool Should we include incomplete results.
     */
    protected $with_incomplete = false;

    /**
     * @inheritDoc
     */
    public function apply(Builder $builder, Model $model)
    {
        if ($this->with_incomplete === false) {
            $builder->where('completed', true);
        }
    }

    /**
     * Extend the query builder with the needed functions.
     * @param Builder $builder
     */
    public function extend(Builder $builder)
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }
    }

    /**
     * Add the with incomplete builder extension.
     * @param Builder $builder
     */
    protected function addWithIncomplete(Builder $builder)
    {
        $builder->macro('withIncomplete', function (Builder $builder) {
            $this->with_incomplete = true;

            return $builder;
        });
    }
}
