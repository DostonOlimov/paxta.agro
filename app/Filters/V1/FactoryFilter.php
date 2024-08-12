<?php
namespace App\Filters\V1;

use App\Filters\ApiFilter;

class FactoryFilter extends ApiFilter{

    public $safeParams = [
        'id' => ['eq'],
        'name' => ['lk'],
        'stateId' => ['eq'],
        'tara' => ['eq','gt','ls'],
        'kod' => ['eq'],
    ];

    protected $operatorMap = [
        'eq' => '=',
        'lk' => 'like',
        'gt' => '>',
        'gte' => '>=',
        'lt' => '<',
        'lte' => '<='
    ];

    protected $columnMap = [
        'stateId' => 'state_id'
    ];

}
