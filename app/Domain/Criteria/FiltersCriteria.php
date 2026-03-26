<?php

namespace App\Domain\Criteria;

use Illuminate\Database\Eloquent\Builder;

/**
 * Modern Filter Criteria System
 *
 * Syntax: field|operator|value;field2|operator2|value2
 *
 * Operators:
 * - eq, = : equals
 * - ne, != : not equals
 * - gt, > : greater than
 * - gte, >= : greater than or equal
 * - lt, < : less than
 * - lte, <= : less than or equal
 * - like : contains (adds % around value)
 * - ilike : case-insensitive like
 * - in : value is comma-separated list
 * - notIn : value is comma-separated list
 * - null : field is null (value ignored)
 * - notNull : field is not null (value ignored)
 * - between : value is comma-separated (min,max)
 * - date : date comparison (value format: Y-m-d)
 * - dateBetween : date range (value format: Y-m-d,Y-m-d)
 *
 * Relations:
 * - relation.field|operator|value : filter by related model
 *
 * Examples:
 * - plate|like|ABC
 * - owner_name|like|Juan
 * - vehicle_type|in|car,motorcycle
 * - created_at|dateBetween|2024-01-01,2024-12-31
 * - vehicle.plate|eq|ABC123
 */
class FiltersCriteria implements FilterCriteriaInterface
{
    protected array $filters;

    protected array $allowedFields;

    protected array $allowedRelations;

    protected array $dateFields;

    public function __construct(
        string|array $filters,
        array $allowedFields = [],
        array $allowedRelations = [],
        array $dateFields = []
    ) {
        if (is_string($filters)) {
            $this->filters = $this->parseFilters($filters);
        } elseif (is_array($filters)) {
            // Si es un array asociativo (como ['date_from' => '2024-01-01']), convertirlo al formato correcto
            if ($this->isAssociativeArray($filters)) {
                $this->filters = $this->convertAssociativeToFilters($filters, $allowedFields, $dateFields);
            } else {
                // Ya está en el formato correcto [['field' => ..., 'operator' => ..., 'value' => ...]]
                $this->filters = $filters;
            }
        } else {
            $this->filters = [];
        }

        $this->allowedFields = $allowedFields;
        $this->allowedRelations = $allowedRelations;
        $this->dateFields = $dateFields;
    }

    /**
     * Apply filters to query builder
     */
    public function apply(Builder $query): Builder
    {
        foreach ($this->filters as $filter) {
            $this->applyFilter($query, $filter);
        }

        return $query;
    }

    /**
     * Parse filter string into array
     */
    protected function parseFilters(string $filters): array
    {
        if (empty(trim($filters))) {
            return [];
        }

        $parsed = [];
        $filterParts = explode(';', $filters);

        foreach ($filterParts as $filter) {
            $filter = trim($filter);
            if (empty($filter)) {
                continue;
            }

            $parts = explode('|', $filter);
            if (count($parts) < 2) {
                continue;
            }

            $field = trim($parts[0]);
            $operator = trim($parts[1] ?? 'eq');
            $value = trim($parts[2] ?? '');

            $parsed[] = [
                'field' => $field,
                'operator' => $operator,
                'value' => $value,
            ];
        }

        return $parsed;
    }

    /**
     * Apply a single filter
     */
    protected function applyFilter(Builder $query, array $filter): void
    {
        $field = $filter['field'];
        $operator = $filter['operator'];
        $value = $filter['value'];

        // Check if field is a relation
        if (str_contains($field, '.')) {
            $this->applyRelationFilter($query, $field, $operator, $value);

            return;
        }

        // Validate field is allowed
        if (! empty($this->allowedFields) && ! in_array($field, $this->allowedFields)) {
            return;
        }

        $this->applyFieldFilter($query, $field, $operator, $value);
    }

    /**
     * Apply filter on relation
     */
    protected function applyRelationFilter(Builder $query, string $field, string $operator, string $value): void
    {
        $parts = explode('.', $field);
        $relation = $parts[0];
        $relationField = $parts[1];

        // Validate relation is allowed
        if (! empty($this->allowedRelations) && ! in_array($relation, $this->allowedRelations)) {
            return;
        }

        $query->whereHas($relation, function (Builder $q) use ($relationField, $operator, $value) {
            $this->applyFieldFilter($q, $relationField, $operator, $value);
        });
    }

    /**
     * Apply filter on field
     */
    protected function applyFieldFilter(Builder $query, string $field, string $operator, string $value): void
    {
        $operator = strtolower($operator);

        match ($operator) {
            'null', 'is_null' => $query->whereNull($field),
            'notnull', 'not_null', 'is_not_null' => $query->whereNotNull($field),
            'in' => $this->applyInFilter($query, $field, $value),
            'notin', 'not_in' => $this->applyNotInFilter($query, $field, $value),
            'between' => $this->applyBetweenFilter($query, $field, $value),
            'datebetween', 'date_between' => $this->applyDateBetweenFilter($query, $field, $value),
            'date' => $this->applyDateFilter($query, $field, $value),
            'like' => $query->where($field, 'like', "%{$value}%"),
            'ilike' => $query->whereRaw("LOWER({$field}) LIKE ?", ['%'.strtolower($value).'%']),
            'eq', '=', '==' => $query->where($field, '=', $value),
            'ne', '!=', '<>' => $query->where($field, '!=', $value),
            'gt', '>' => $query->where($field, '>', $value),
            'gte', '>=' => $query->where($field, '>=', $value),
            'lt', '<' => $query->where($field, '<', $value),
            'lte', '<=' => $query->where($field, '<=', $value),
            default => $query->where($field, $operator, $value),
        };
    }

    /**
     * Apply IN filter
     */
    protected function applyInFilter(Builder $query, string $field, string $value): void
    {
        $values = array_map('trim', explode(',', $value));
        $values = array_filter($values);

        if (! empty($values)) {
            $query->whereIn($field, $values);
        }
    }

    /**
     * Apply NOT IN filter
     */
    protected function applyNotInFilter(Builder $query, string $field, string $value): void
    {
        $values = array_map('trim', explode(',', $value));
        $values = array_filter($values);

        if (! empty($values)) {
            $query->whereNotIn($field, $values);
        }
    }

    /**
     * Apply BETWEEN filter
     */
    protected function applyBetweenFilter(Builder $query, string $field, string $value): void
    {
        $values = array_map('trim', explode(',', $value));

        if (count($values) === 2) {
            $query->whereBetween($field, $values);
        }
    }

    /**
     * Apply DATE filter
     */
    protected function applyDateFilter(Builder $query, string $field, string $value): void
    {
        try {
            $date = \Carbon\Carbon::parse($value)->startOfDay();
            $query->whereDate($field, '=', $date);
        } catch (\Exception $e) {
            // Invalid date, skip filter
        }
    }

    /**
     * Apply DATE BETWEEN filter
     */
    protected function applyDateBetweenFilter(Builder $query, string $field, string $value): void
    {
        $values = array_map('trim', explode(',', $value));

        if (count($values) === 2) {
            try {
                $startDate = \Carbon\Carbon::parse($values[0])->startOfDay();
                $endDate = \Carbon\Carbon::parse($values[1])->endOfDay();
                $query->whereBetween($field, [$startDate, $endDate]);
            } catch (\Exception $e) {
                // Invalid dates, skip filter
            }
        }
    }

    /**
     * Check if array is associative
     */
    protected function isAssociativeArray(array $array): bool
    {
        if (empty($array)) {
            return false;
        }

        // Check if keys are numeric and sequential (indexed array)
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * Convert associative array to filters format
     */
    protected function convertAssociativeToFilters(array $filters, array $allowedFields = [], array $dateFields = []): array
    {
        $converted = [];

        foreach ($filters as $key => $value) {
            // Skip empty values
            if ($value === null || $value === '') {
                continue;
            }

            // If allowedFields is specified, only include allowed fields
            if (! empty($allowedFields) && ! in_array($key, $allowedFields)) {
                continue;
            }

            // Determine operator based on field type
            $operator = 'eq';

            if (in_array($key, $dateFields)) {
                $operator = 'date';
            } elseif (str_contains((string) $value, ',')) {
                $operator = 'in';
            } elseif (strlen((string) $value) > 3 && (str_contains((string) $value, '%') || preg_match('/[a-zA-Z]/', (string) $value))) {
                $operator = 'like';
            }

            $converted[] = [
                'field' => $key,
                'operator' => $operator,
                'value' => (string) $value,
            ];
        }

        return $converted;
    }

    /**
     * Create instance from request
     */
    public static function fromRequest(array $requestData, array $allowedFields = [], array $allowedRelations = [], array $dateFields = []): self
    {
        $filters = [];

        foreach ($requestData as $key => $value) {
            if (empty($value) || ! in_array($key, $allowedFields)) {
                continue;
            }

            // Determine operator based on field type
            $operator = 'eq';

            if (in_array($key, $dateFields)) {
                if (str_contains($value, ',')) {
                    $operator = 'dateBetween';
                } else {
                    $operator = 'date';
                }
            } elseif (str_contains($value, ',')) {
                $operator = 'in';
            } elseif (str_contains($value, '%') || strlen($value) > 3) {
                $operator = 'like';
            }

            $filters[] = [
                'field' => $key,
                'operator' => $operator,
                'value' => $value,
            ];
        }

        return new self($filters, $allowedFields, $allowedRelations, $dateFields);
    }
}
