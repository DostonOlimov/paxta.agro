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
        'partyNumber' => ['eq','lk'],
        'stateId' => ['eq'],
        'cityId' => ['eq'],
        'createdBy' => ['eq'],
        'year' => ['eq'],
    ];

    protected array $operatorMap = [
        'eq' => '=',
        'ne' => '!=',
        'lk' => 'like',
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

    protected array $sortColumnMap  = [
        'organization' => [
            'table' => 'organization_companies',
            'column' => 'organization_companies.name',
            'foreign_key' => 'applications.organization_id',
            'local_key' => 'organization_companies.id'
        ],
        'amount' => [
            'table' => 'crop_data',
            'column' => 'crop_data.amount',
            'foreign_key' => 'applications.crop_data_id',
            'local_key' => 'crop_data.id'
        ],
        'party_number' => [
            'table' => 'crop_data',
            'column' => 'crop_data.party_number',
            'foreign_key' => 'applications.crop_data_id',
            'local_key' => 'crop_data.id'
        ]
    ];
    /**
     * Determine if a filter requires joining another table.
     */
    protected function requiresJoin(string $key): bool
    {
        return in_array($key, ['nameId','partyNumber', 'cityId', 'stateId','year']);
    }

    /**
     * Apply necessary joins based on the filter key.
     */
    protected function applyJoin(Builder $query, string $key): void
    {
        if ($key === 'cityId') {
            $query->join('organization_companies', 'applications.organization_id', '=', 'organization_companies.id');
        } elseif($key === 'nameId' or $key === 'year' or $key === 'partyNumber'){
            $query->join('crop_data', 'applications.crop_data_id', '=', 'crop_data.id');
        }
        elseif ($key === 'stateId') {
            $query->join('organization_companies', 'applications.organization_id', '=', 'organization_companies.id')
                ->join('tbl_cities as cities', 'organization_companies.city_id', '=', 'cities.id');
        }
    }

    /**
     * Get the column name to be used in the join condition.
     */
    protected function getJoinColumn(string $key): string
    {
        $joinColumnMap = [
            'nameId' => 'crop_data.name_id',
            'partyNumber' => 'crop_data.party_number',
            'stateId' => 'cities.state_id',
            'cityId' => 'organization_companies.city_id',
            'year' => 'crop_data.year'
        ];

        return $joinColumnMap[$key] ?? $this->getColumn($key);
    }


}
