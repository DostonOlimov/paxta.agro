<?php
namespace App\Filters\V1;

use App\Filters\ApiFilter;

class FactoryFilter extends ApiFilter{

    public array $safeParams = [
        'id' => ['eq'],
        'name' => ['lk'],
        'stateId' => ['eq'],
        'tara' => ['eq','gt','ls'],
        'kod' => ['eq'],
    ];

    protected array $operatorMap = [
        'eq' => '=',
        'lk' => 'like',
        'gt' => '>',
        'gte' => '>=',
        'lt' => '<',
        'lte' => '<='
    ];

    protected array $columnMap = [
        'stateId' => 'state_id'
    ];

}
