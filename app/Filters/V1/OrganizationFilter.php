<?php
namespace App\Filters\V1;

use App\Filters\ApiFilter;

class OrganizationFilter extends ApiFilter{

    public $safeParams = [
        'id' => ['eq'],
        'name' => ['lk'],
        'cityId' => ['eq'],
        'address' => ['lk'],
        'ownerName' => ['lk'],
        'phoneName' => ['lk'],
        'inn' => ['eq', 'lk']
    ];

    protected $columnMap = [
        'cityId' => 'city_id',
        'ownerName' => 'owner_name',
        'phoneName' => 'phone_number'
    ];

    protected $operatorMap = [
        'eq' => '=',
        'lk' => 'like',
    ];

}
