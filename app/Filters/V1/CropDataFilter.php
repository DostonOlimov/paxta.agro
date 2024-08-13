<?php
namespace App\Filters\V1;

use App\Filters\ApiFilter;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class CropDataFilter extends ApiFilter{

    public array $safeParams = [
        'id' => ['eq'],
        'kodtnved' => ['eq', 'ne'],
        'partyNumber' => ['eq','lk'],
        'amount' => ['gt', 'lt','gte','lte'],
        'toyCount' => ['eq']
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
