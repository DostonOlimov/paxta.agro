<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\CityFilter;
use App\Http\Resources\V1\CityCollection;
use App\Http\Resources\V1\CityResource;
use App\Models\Area;
use Illuminate\Http\Request;


class CityController extends Controller
{
    public function index(Request $request, CityFilter $filter)
    {
        try {
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

            if ($areas->isEmpty()) {
                return $this->notFoundResponse('No cities found');
            }

            return $this->successResponse(
                new CityCollection($areas),
                'Cities retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            // Retrieve the 'includeCities' parameter
            $includeCities = filter_var($request->query('includeCities'), FILTER_VALIDATE_BOOLEAN);

            // Find the Area by ID
            $query = Area::query();

            if ($includeCities) {
                $query->with('cities'); // Assuming 'cities' is the relation you want to include
            }

            $area = $query->find($id);

            if (!$area) {
                return $this->notFoundResponse('City not found');
            }

            return $this->successResponse(
                new CityResource($area),
                'City retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

