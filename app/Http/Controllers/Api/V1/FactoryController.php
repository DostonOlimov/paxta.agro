<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\FactoryFilter;
use App\Http\Resources\V1\FactoryCollection;
use App\Http\Resources\V1\FactoryResource;
use App\Models\PreparedCompanies;
use Illuminate\Http\Request;


class FactoryController extends Controller
{
    public function index(Request $request, FactoryFilter $filter)
    {
        try {
            $query = PreparedCompanies::query();

            // Extract filters from request
            $filters = $this->getFilters($request, $filter);

            // Apply filters to the query
            $filteredQuery = $filter->apply($query, $filters);

            // Get the results
            $factories = $filteredQuery->get();

            return $this->successResponse(
                new FactoryCollection($factories),
                'Factories retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id)
    {
        try {
            $factory = PreparedCompanies::find($id);

            if (!$factory) {
                return $this->notFoundResponse('Factory not found');
            }

            return $this->successResponse(
                new FactoryResource($factory),
                'Factory retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function getFilters(Request $request, FactoryFilter $filter): array
    {
        return $request->only(array_keys($filter->safeParams));
    }
}

