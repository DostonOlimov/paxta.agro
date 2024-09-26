<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\StateFilter;
use App\Http\Resources\V1\StateCollection;
use App\Http\Resources\V1\StateResource;
use App\Models\Region;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;


class StateController extends Controller
{
    public function index(Request $request, StateFilter $filter)
    {
        try {
            // Define validation rules
            $validator = Validator::make($request->all(), [
                'includeCities' => 'boolean',
            ]);

            // If validation fails, return a validation error response
            if ($validator->fails()) {
                return $this->validationErrorResponse(new \Illuminate\Validation\ValidationException($validator));
            }

            // Initialize query
            $query = Region::query();

            // Retrieve filters and apply them
            $filters = $request->only(array_keys($filter->safeParams));
            $query = $filter->apply($query, $filters);

            // Conditionally include related data
            $query->when($request->input('includeCities'), function ($query) {
                $query->with('areas');
            });

            // Retrieve the data
            $states = $query->get();

            if ($states->isEmpty()) {
                return $this->notFoundResponse('No states found');
            }

            // Return as a resource collection
            return $this->successResponse(
                new StateCollection($states),
                'States retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            // Validate the 'includeCities' parameter
            $includeCities = filter_var($request->query('includeCities'), FILTER_VALIDATE_BOOLEAN);

            // Determine the query
            $query = Region::query();

            if ($includeCities) {
                $query->with('areas');
            }

            // Find the region by ID
            $state = $query->find($id);

            if (!$state) {
                return $this->notFoundResponse('State not found');
            }

            return $this->successResponse(
                new StateResource($state),
                'State retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

