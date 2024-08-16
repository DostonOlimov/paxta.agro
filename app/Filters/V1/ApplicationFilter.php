<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;
use Illuminate\Database\Eloquent\Builder;

class ApplicationFilter extends ApiFilter
{
    public array $safeParams = [
        'id' => ['eq'],
        'type' => ['eq'],
        'date' => ['gt','lt'],
        'status' => ['eq','ne'],
        'companyId' => ['eq'],
        'factoryId' => ['eq'],
        'nameId' => ['eq'],
        'stateId' => ['eq'],
        'cityId' => ['eq'],
        'createdBy' => ['eq'],
        'year' => ['eq'],
    ];

    protected array $operatorMap = [
        'eq' => '=',
        'ne' => '!=',
        'gt' => '>',
        'gte' => '>=',
        'lt' => '<',
        'lte' => '<='
    ];

    protected array $columnMap = [
        'companyId' => 'organization_id',
        'factoryId' => 'prepared_id',
        'createdBy' => 'created_by'
    ];

    /**
     * Determine if a filter requires joining another table.
     */
    protected function requiresJoin(string $key): bool
    {
        return in_array($key, ['nameId', 'cityId', 'stateId','year']);
    }

    /**
     * Apply necessary joins based on the filter key.
     */
    protected function applyJoin(Builder $query, string $key): void
    {
        if ($key === 'cityId') {
            $query->join('organization_companies', 'applications.organization_id', '=', 'organization_companies.id');
        } elseif($key === 'nameId' or $key === 'year'){
            $query->join('crop_data', 'applications.crop_data_id', '=', 'crop_data.id');
        }
        elseif ($key === 'stateId') {
            $query->join('organization_companies', 'applications.organization_id', '=', 'organization_companies.id')
                ->join('tbl_cities', 'organization_companies.city_id', '=', 'tbl_cities.id');
        }
    }

    /**
     * Get the column name to be used in the join condition.
     */
    protected function getJoinColumn(string $key): string
    {
        $joinColumnMap = [
            'nameId' => 'crop_data.name_id',
            'stateId' => 'tbl_cities.state_id',
            'cityId' => 'organization_companies.city_id',
            'year' => 'crop_data.year'
        ];

        return $joinColumnMap[$key] ?? $this->getColumn($key);
    }
}
