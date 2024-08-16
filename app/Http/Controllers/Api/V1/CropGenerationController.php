<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\CropGenerationFilter;
use App\Http\Resources\V1\CropGenerationCollection;
use App\Http\Resources\V1\CropGenerationResource;
use App\Models\CropsGeneration;
use Illuminate\Http\Request;


class CropGenerationController extends Controller
{
    public function index(Request $request, CropGenerationFilter $filter): CropGenerationCollection
    {
        // Initialize query
        $query = CropsGeneration::query();

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

        return new CropGenerationCollection($data);
    }

    public function show(Request $request, $id): CropGenerationResource
    {
        $data = CropsGeneration::findOrFail($id);

        return new CropGenerationResource($data);
    }

}
