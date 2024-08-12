<?php
namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ApiFilter {

    protected $safeParams = [];

    protected $columnMap = [];

    protected $operatorMap = [];

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
            if (array_key_exists($key, $this->safeParams)) {

                $operators = $this->safeParams[$key];
                $column = $this->columnMap[$key] ?? $key;


                foreach ($operators as $operator) {

                    if (isset($value[$operator])) {

                        $operatorSymbol = $this->operatorMap[$operator] ?? $operator;

                        if ($operator === 'lk') {

                            $query->where($column, $operatorSymbol, '%' . $value[$operator] . '%');

                        } else {

                            $query->where($column, $operatorSymbol, $value[$operator]);

                        }
                    }
                }
            }
        }

        return $query;
    }
}
