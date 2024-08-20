<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\CropGenerationFilter;
use App\Http\Resources\V1\CropGenerationCollection;
use App\Http\Resources\V1\CropGenerationResource;
use App\Models\CropsGeneration;
use Illuminate\Http\Request;


class CropGenerationController extends Controller
{
    public function index(Request $request, CropGenerationFilter $filter)
    {
        try {
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

            if ($data->isEmpty()) {
                return $this->notFoundResponse('No crop generations found');
            }

            return $this->successResponse(
                new CropGenerationCollection($data),
                'Crop generations retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $data = CropsGeneration::find($id);

            if (!$data) {
                return $this->notFoundResponse('Crop generation not found');
            }

            return $this->successResponse(
                new CropGenerationResource($data),
                'Crop generation retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

