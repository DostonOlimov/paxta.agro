<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

abstract class ApiFilter
{
    protected array $safeParams = [];
    protected array $columnMap = [];
    protected array $operatorMap = [];
    protected array $sortColumnMap = [];

    /**
     * Apply the filters to the query.
     *
     * @param Builder $query
     * @param array $filters
     * @return Builder
     */
    public function apply(Builder $query, array $filters): Builder
    {
        foreach ($filters as $key => $value) {
            if ($this->isSafeParam($key)) {
                $this->applyFilter($query, $key, $value);
            }
        }

        return $query;
    }
    /**
     * Apply sorting to the query dynamically.
     *
     * @param Builder $query
     * @param string $sort_by
     * @param string $sort_order
     * @return Builder
     */
    public function applySorting(Builder $query, string $sort_by, string $sort_order): Builder
    {
        // Check if sorting requires a join
        if (array_key_exists($sort_by, $this->sortColumnMap)) {
            $sortData = $this->sortColumnMap[$sort_by];
            // Apply the join and order by the related column
            $query->join($sortData['table'], $sortData['foreign_key'], '=', $sortData['local_key'])
                ->orderBy($sortData['column'], $sort_order);
        } else {
            // Ensure you're ordering by the correct `id` column
            $sort_by = $sort_by === 'id' ? 'applications.id' : $sort_by;
            $query->orderBy($sort_by, $sort_order);
        }

        return $query;
    }
    /**
     * Check if the parameter is safe for filtering.
     *
     * @param string $key
     * @return bool
     */
    protected function isSafeParam(string $key): bool
    {
        return array_key_exists($key, $this->safeParams);
    }

    /**
     * Apply individual filters.
     *
     * @param Builder $query
     * @param string $key
     * @param mixed $value
     */
    protected function applyFilter(Builder $query, string $key, $value): void
    {
        $operators = $this->safeParams[$key];
        $column = $this->getColumn($key);

        foreach ($operators as $operator) {
            if (isset($value[$operator])) {
                $this->applyCondition($query, $key, $column, $operator, $value[$operator]);
            }
        }
    }

    /**
     * Get the database column for the filter key.
     *
     * @param string $key
     * @return string
     */
    protected function getColumn(string $key): string
    {
        return $this->columnMap[$key] ?? $key;
    }

    /**
     * Apply the condition to the query.
     *
     * @param Builder $query
     * @param string $key
     * @param string $column
     * @param string $operator
     * @param mixed $value
     */
    protected function applyCondition(Builder $query, string $key, string $column, string $operator, $value): void
    {
        $operatorSymbol = $this->operatorMap[$operator] ?? $operator;

        if ($this->requiresJoin($key)) {
            $this->applyJoin($query, $key);
            $query->where($this->getJoinColumn($key), $operatorSymbol, $this->prepareValue($operator, $value));
        } else {
            $query->where($column, $operatorSymbol, $this->prepareValue($operator, $value));
        }
    }

    /**
     * Prepare value based on the operator.
     *
     * @param string $operator
     * @param mixed $value
     * @return mixed
     */
    protected function prepareValue(string $operator, $value)
    {
        return $operator === 'lk' ? "%$value%" : $value;
    }

    /**
     * Determine if a filter requires joining another table.
     */
    protected function requiresJoin(string $key): bool
    {
        return false; // Override in child classes if needed
    }

    /**
     * Apply necessary joins based on the filter key.
     */
    protected function applyJoin(Builder $query, string $key): void
    {
        // Override in child classes if needed
    }

    /**
     * Get the column name to be used in the join condition.
     */
    protected function getJoinColumn(string $key): string
    {
        return $this->getColumn($key); // Override in child classes if needed
    }



}
