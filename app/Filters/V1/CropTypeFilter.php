<?php
namespace App\Filters\V1;

use App\Filters\ApiFilter;

class CropTypeFilter extends ApiFilter{

    public array $safeParams = [
        'id' => ['eq'],
        'name' => ['lk'],
        'kod' => ['eq']
    ];

    protected array $operatorMap = [
        'eq' => '=',
        'lk' => 'like',
    ];

}
