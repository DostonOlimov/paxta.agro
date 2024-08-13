<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\CompanyFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CompanyCollection;
use App\Http\Resources\V1\CompanyResource;
use App\Models\OrganizationCompanies;
use Illuminate\Http\Request;


class CompanyController extends Controller
{
    public function index(Request $request, CompanyFilter $filter)
    {
        $query = OrganizationCompanies::query();

        // Extract filters from request
        $filters = $this->getFilters($request, $filter);

        // Apply filters to the query
        $filteredQuery = $filter->apply($query, $filters);

        // Get the results
        $data = $filteredQuery->get();

        return new CompanyCollection($data);
    }

    public function show(int $id)
    {
        $data = OrganizationCompanies::findOrFail($id);

        return new CompanyResource($data);
    }

    private function getFilters(Request $request, CompanyFilter $filter): array
    {
        return $request->only(array_keys($filter->safeParams));
    }

}
