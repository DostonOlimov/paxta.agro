<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\CropNameFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CropNameCollection;
use App\Http\Resources\V1\CropNameResource;
use App\Models\CropsName;
use App\Models\Region;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;


class CropNameController extends Controller
{

    public function index(Request $request, CropNameFilter $filter)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'includeTypes' => 'boolean',
            'includeGenerations' => 'boolean',
            // You can add more validation rules here as needed
        ]);

        // If validation fails, return a validation error response
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
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

        // Return as a resource collection
        return new CropNameCollection($data);
    }

    public function show(Request $request, $id): CropNameResource
    {
        // Validate the 'includeCities' parameter
        $includeTypes = filter_var($request->query('includeTypes'), FILTER_VALIDATE_BOOLEAN);
        $includeGenerations = filter_var($request->query('includeGeneration'), FILTER_VALIDATE_BOOLEAN);

        // Determine the query
        $query = CropsName::query();

        // get with types
        if ($includeTypes) {
            $query->with('types');
        }

        // get with generations
        if ($includeGenerations) {
            $query->with('generations');
        }

        // Find the region by ID
        $data = $query->findOrFail($id);

        return new CropNameResource($data);
    }

}
