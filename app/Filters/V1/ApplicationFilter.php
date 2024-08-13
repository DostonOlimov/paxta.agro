<?php
namespace App\Filters\V1;

use App\Filters\ApiFilter;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class ApplicationFilter extends ApiFilter{

    public array $safeParams = [
        'id' => ['eq'],
        'status' => ['eq', 'ne'],
        'type' => ['eq'],
        'date' => ['gt', 'lt'],
        'acceptedDate' => ['gt', 'lt']
    ];

    protected array $columnMap = [
        'acceptedDate' => 'accepted_date'
    ];

    protected array $operatorMap = [
        'eq' => '=',
        'ne' => '!=',
        'gt' => '>',
        'gte' => '>=',
        'lt' => '<',
        'lte' => '<='
    ];

}
