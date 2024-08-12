<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\StateFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\StateCollection;
use App\Http\Resources\V1\StateResource;
use App\Models\Region;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;


class StateController extends Controller
{

    public function index(Request $request, StateFilter $filter): StateCollection
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'includeCities' => 'boolean',
            // You can add more validation rules here as needed
        ]);

        // If validation fails, return a validation error response
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
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

        // Return as a resource collection
        return new StateCollection($states);
    }

    public function show(Request $request, $id): StateResource
    {
        // Validate the 'includeCities' parameter
        $includeCities = filter_var($request->query('includeCities'), FILTER_VALIDATE_BOOLEAN);

        // Determine the query
        $query = Region::query();

        if ($includeCities) {
            $query->with('areas');
        }

        // Find the region by ID
        $application = $query->findOrFail($id);

        return new StateResource($application);
    }

}
