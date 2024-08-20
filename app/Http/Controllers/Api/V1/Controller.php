<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use ApiResponse, AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        // Apply Sanctum middleware or any API-specific middleware here if needed
//        $this->middleware('auth:sanctum');
    }
}
