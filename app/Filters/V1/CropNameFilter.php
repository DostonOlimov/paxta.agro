<?php
namespace App\Filters\V1;

use App\Filters\ApiFilter;

class CropNameFilter extends ApiFilter{

    public array $safeParams = [
        'id' => ['eq'],
        'name' => ['lk'],
        'kodtnved' => ['eq']
    ];

    protected array $operatorMap = [
        'eq' => '=',
        'lk' => 'like',
    ];

}
