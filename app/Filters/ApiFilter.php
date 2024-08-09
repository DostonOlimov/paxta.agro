<?php
namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ApiFilter {

    protected $safeParams = [];

    protected $columnMap = [];

    protected $operatorMap = [];

    public function apply(Builder $query, $filters)
    {
        foreach ($filters as $key => $value) {
            if (array_key_exists($key, $this->safeParams)) {
                $operators = $this->safeParams[$key];
                $column = $this->columnMap[$key] ?? $key;

                foreach ($operators as $operator) {
                    if (isset($value[$operator])) {
                        $query->where($column, $this->operatorMap[$operator], $value[$operator]);
                    }
                }
            }
        }
        return $query;
    }

}
