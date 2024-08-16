<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\CropTypeFilter;
use App\Http\Resources\V1\CropTypeCollection;
use App\Http\Resources\V1\CropTypeResource;
use App\Models\CropsType;
use Illuminate\Http\Request;


class CropTypeController extends Controller
{
    public function index(Request $request, CropTypeFilter $filter): CropTypeCollection
    {
        // Initialize query
        $query = CropsType::query();

        // Apply filters
        $filters = $request->only(array_keys($filter->safeParams));
        $query = $filter->apply($query, $filters);

        // Optionally add crop filter
        $cropId = $request->input('cropId');
        if ($cropId) {
            $query->where('crop_id', $cropId);
        }

        // Retrieve and return results
        $data = $query->get();

        return new CropTypeCollection($data);
    }

    public function show(Request $request, $id): CropTypeResource
    {
        $data = CropsType::findOrFail($id);

        return new CropTypeResource($data);
    }

}
