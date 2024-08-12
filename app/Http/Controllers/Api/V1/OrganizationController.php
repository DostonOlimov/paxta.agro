<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\ApplicationFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ApplicationCollection;
use App\Http\Resources\V1\ApplicationResource;
use App\Models\Application;
use Illuminate\Http\Request;


class OrganizationController extends Controller
{
    public function index(Request $request, ApplicationFilter $filter)
    {
        $query = Application::query();
        $filters = $request->only(array_keys($filter->safeParams));
        $query = $filter->apply($query, $filters);

        $applications = $query->paginate(10);

        return new ApplicationCollection($applications);
    }

    public function show($id)
    {
        $application = Application::findOrFail($id);

        return new ApplicationResource($application);
    }

}
