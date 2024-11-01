<?php
namespace App\Filters\V1;

use App\Filters\ApiFilter;
use Illuminate\Http\Request;

class SifatContractsFilter extends ApiFilter{

    public array $safeParams = [
        'inn' => ['eq'],
        'date' => ['eq'],
        'number' => ['eq'],
    ];

    protected array $operatorMap = [
        'eq' => '=',
        'lk' => 'like',
    ];
    //getting safe params for filter
    public function getFilters(Request $request): array
    {
        return $request->only(array_keys($this->safeParams));
    }

}
