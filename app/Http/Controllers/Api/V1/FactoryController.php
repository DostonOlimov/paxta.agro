<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\FactoryFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CityCollection;
use App\Http\Resources\V1\CityResource;
use App\Http\Resources\V1\FactoryCollection;
use App\Http\Resources\V1\FactoryResource;
use App\Models\Area;
use App\Models\PreparedCompanies;
use Illuminate\Http\Request;


class FactoryController extends Controller
{
    public function index(Request $request, FactoryFilter $filter)
    {
        $query = PreparedCompanies::query();

        // Extract filters from request
        $filters = $this->getFilters($request, $filter);

        // Apply filters to the query
        $filteredQuery = $filter->apply($query, $filters);

        // Get the results
        $states = $filteredQuery->get();

        return new FactoryCollection($states);
    }

    public function show(int $id)
    {
        $factory = PreparedCompanies::findOrFail($id);

        return new FactoryResource($factory);
    }

    private function getFilters(Request $request, FactoryFilter $filter): array
    {
        return $request->only(array_keys($filter->safeParams));
    }

}
