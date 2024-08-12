<?php
namespace App\Filters\V1;

use App\Filters\ApiFilter;

class CityFilter extends ApiFilter{

    public $safeParams = [
        'id' => ['eq'],
        'name' => ['lk'],
    ];

    protected $operatorMap = [
        'eq' => '=',
        'lk' => 'like',
    ];

}
