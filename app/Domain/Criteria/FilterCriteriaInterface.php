<?php

namespace App\Domain\Criteria;

use Illuminate\Database\Eloquent\Builder;

interface FilterCriteriaInterface
{
    /**
     * Apply criteria to the query builder
     *
     * @param Builder $query
     * @return Builder
     */
    public function apply(Builder $query): Builder;
}



