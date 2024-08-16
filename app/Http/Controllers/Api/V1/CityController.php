<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\CityFilter;
use App\Http\Resources\V1\CityCollection;
use App\Http\Resources\V1\CityResource;
use App\Models\Area;
use Illuminate\Http\Request;


class CityController extends Controller
{
    public function index(Request $request, CityFilter $filter): CityCollection
    {
        // Initialize query
        $query = Area::query();

        // Apply filters
        $filters = $request->only(array_keys($filter->safeParams));
        $query = $filter->apply($query, $filters);

        // Optionally add state filter
        $stateId = $request->input('stateId');
        if ($stateId) {
            $query->where('state_id', $stateId);
        }

        // Retrieve and return results
        $areas = $query->get();

        return new CityCollection($areas);
    }

    public function show(Request $request, $id): CityResource
    {
        // Retrieve the 'includeCities' parameter
        $includeCities = filter_var($request->query('includeCities'), FILTER_VALIDATE_BOOLEAN);

        // Find the Area by ID
        $query = Area::query();

        if ($includeCities) {
            $query->with('cities'); // Assuming 'cities' is the relation you want to include
        }

        $area = $query->findOrFail($id);

        return new CityResource($area);
    }

}
