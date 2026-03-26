<?php

namespace App\Domain\Criteria;

use Illuminate\Database\Eloquent\Builder;

interface FilterCriteriaInterface
{
    /**
     * Apply criteria to the query builder
     */
    public function apply(Builder $query): Builder;
}
