<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\ApplicationFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ApplicationCollection;
use App\Http\Resources\V1\ApplicationResource;
use App\Models\Application;
use App\Models\OrganizationCompanies;
use Illuminate\Http\Request;


class ApplicationController extends Controller
{
    public function index(Request $request, ApplicationFilter $filter)
    {
        $query = Application::query();

        // Extract filters from request
        $filters = $this->getFilters($request, $filter);

        // Apply filters to the query
        $filteredQuery = $filter->apply($query, $filters);

        // Get the results
        $data = $filteredQuery->with('crops')
            ->with('organization')
            ->with('prepared')
            ->paginate(10);

        return new ApplicationCollection($data);
    }

    public function show(int $id)
    {
        $data = Application::with('crops')
            ->with('organization')
            ->with('prepared')
            ->findOrFail($id);

        return new ApplicationResource($data);
    }

    private function getFilters(Request $request, ApplicationFilter $filter): array
    {
        return $request->only(array_keys($filter->safeParams));
    }

}
