<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\CropNameFilter;
use App\Http\Resources\V1\CropNameCollection;
use App\Http\Resources\V1\CropNameResource;
use App\Models\CropsName;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class CropNameController extends Controller
{
    public function index(Request $request, CropNameFilter $filter)
    {
        try {
            // Define validation rules
            $validator = Validator::make($request->all(), [
                'includeTypes' => 'boolean',
                'includeGenerations' => 'boolean',
                // You can add more validation rules here as needed
            ]);

            // If validation fails, return a validation error response
            if ($validator->fails()) {
                return $this->validationErrorResponse(new \Illuminate\Validation\ValidationException($validator));
            }

            // Initialize query
            $query = CropsName::query();

            // Retrieve filters and apply them
            $filters = $request->only(array_keys($filter->safeParams));
            $query = $filter->apply($query, $filters);

            // Conditionally include related data
            $query->when($request->input('includeTypes'), function ($query) {
                $query->with('types');
            });

            // Conditionally include related data
            $query->when($request->input('includeGenerations'), function ($query) {
                $query->with('generations');
            });

            // Retrieve the data
            $data = $query->get();

            if ($data->isEmpty()) {
                return $this->notFoundResponse('No crop names found');
            }

            // Return as a resource collection
            return $this->successResponse(
                new CropNameCollection($data),
                'Crop names retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            // Validate the 'includeCities' parameter
            $includeTypes = filter_var($request->query('includeTypes'), FILTER_VALIDATE_BOOLEAN);
            $includeGenerations = filter_var($request->query('includeGenerations'), FILTER_VALIDATE_BOOLEAN);

            // Determine the query
            $query = CropsName::query();

            // Get with types
            if ($includeTypes) {
                $query->with('types');
            }

            // Get with generations
            if ($includeGenerations) {
                $query->with('generations');
            }

            // Find the crop name by ID
            $data = $query->find($id);

            if (!$data) {
                return $this->notFoundResponse('Crop name not found');
            }

            return $this->successResponse(
                new CropNameResource($data),
                'Crop name retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

