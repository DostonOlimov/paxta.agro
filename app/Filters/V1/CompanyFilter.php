<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;
use Illuminate\Database\Eloquent\Builder;

class CompanyFilter extends ApiFilter
{
    public array $safeParams = [
        'id' => ['eq'],
        'name' => ['lk'],
        'cityId' => ['eq'],
        'stateId' => ['eq'],
        'address' => ['lk'],
        'ownerName' => ['lk'],
        'phoneNumber' => ['lk'],
        'inn' => ['eq', 'lk'],
        'cityName' => ['lk'],
        'cityAddress' => ['lk'],
        'stateName' => ['lk'],
    ];

    protected array $operatorMap = [
        'eq' => '=',
        'lk' => 'like',
    ];

    protected array $columnMap = [
        'cityId' => 'city_id',
        'ownerName' => 'owner_name',
        'phoneNumber' => 'phone_number',
    ];

    /**
     * Determine if a filter requires joining another table.
     */
    protected function requiresJoin(string $key): bool
    {
        return in_array($key, ['cityName', 'stateId', 'stateName']);
    }

    /**
     * Apply necessary joins based on the filter key.
     */
    protected function applyJoin(Builder $query, string $key): void
    {
        if ($key === 'cityName' || $key === 'stateId') {
            $query->join('tbl_cities', 'organization_companies.city_id', '=', 'tbl_cities.id');
        } elseif ($key === 'stateName') {
            $query->join('tbl_cities', 'organization_companies.city_id', '=', 'tbl_cities.id')
                ->join('tbl_states', 'tbl_cities.state_id', '=', 'tbl_states.id');
        }
    }

    /**
     * Get the column name to be used in the join condition.
     */
    protected function getJoinColumn(string $key): string
    {
        $joinColumnMap = [
            'cityName' => 'tbl_cities.name',
            'stateId' => 'tbl_cities.state_id',
            'stateName' => 'tbl_states.name',
        ];

        return $joinColumnMap[$key] ?? $this->getColumn($key);
    }
}
