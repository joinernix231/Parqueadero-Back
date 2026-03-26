<?php

namespace App\Infrastructure\Repositories\Traits;

use App\Domain\Criteria\FiltersCriteria;
use Illuminate\Database\Eloquent\Builder;

trait AppliesFilters
{
    /**
     * Apply filters to query builder
     */
    protected function applyFilters(Builder $query, array|string|null $filters): Builder
    {
        if (empty($filters)) {
            return $query;
        }

        $criteria = new FiltersCriteria(
            $filters,
            $this->getFilterableFields(),
            $this->getFilterableRelations(),
            $this->getDateFields()
        );

        return $criteria->apply($query);
    }

    /**
     * Get filterable fields for this repository
     * Override in repository implementation
     */
    protected function getFilterableFields(): array
    {
        return [];
    }

    /**
     * Get filterable relations for this repository
     * Override in repository implementation
     */
    protected function getFilterableRelations(): array
    {
        return [];
    }

    /**
     * Get date fields for this repository
     * Override in repository implementation
     */
    protected function getDateFields(): array
    {
        return ['created_at', 'updated_at'];
    }
}
