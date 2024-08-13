<?php
namespace App\Filters\V1;

use App\Filters\ApiFilter;

class StateFilter extends ApiFilter{

    public array $safeParams = [
        'id' => ['eq'],
        'name' => ['lk'],
    ];

    protected array $operatorMap = [
        'eq' => '=',
        'lk' => 'like',
    ];

}
