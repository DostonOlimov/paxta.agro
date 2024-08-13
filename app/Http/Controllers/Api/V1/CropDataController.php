<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\CropDataFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CropDataCollection;
use App\Http\Resources\V1\CropDataResource;
use App\Models\CropData;
use App\Models\CropsName;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;


class CropDataController extends Controller
{

    public function index(Request $request, CropDataFilter $filter)
    {
        // Initialize query
        $query = CropData::query();

        // Retrieve filters and apply them
        $filters = $request->only(array_keys($filter->safeParams));
        $query = $filter->apply($query, $filters);

        // Retrieve the data
        $data = $query->get();

        // Return as a resource collection
        return new CropDataCollection($data);
    }

    public function show(Request $request, $id): CropDataResource
    {
        // Determine the query
        $query = CropData::query();

        // Find the region by ID
        $data = $query->findOrFail($id);

        return new CropDataResource($data);
    }

}
