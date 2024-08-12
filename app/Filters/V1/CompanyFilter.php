<?php
namespace App\Filters\V1;

use App\Filters\ApiFilter;

class CompanyFilter extends ApiFilter{

    public $safeParams = [
        'id' => ['eq'],
        'name' => ['lk'],
        'cityId' => ['eq'],
        'address' => ['lk'],
        'ownerName' => ['lk'],
        'phoneNumber' => ['lk'],
        'inn' => ['eq','lk'],
    ];

    protected $operatorMap = [
        'eq' => '=',
        'lk' => 'like',
    ];

    protected $columnMap = [
        'cityId' => 'city_id',
        'ownerName' => 'owner_name',
        'phoneNumber' => 'phone_number',
    ];

}
