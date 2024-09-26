<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\CropTypeFilter;
use App\Http\Resources\V1\CropTypeCollection;
use App\Http\Resources\V1\CropTypeResource;
use App\Models\CropsType;
use Illuminate\Http\Request;


class CropTypeController extends Controller
{
    public function index(Request $request, CropTypeFilter $filter)
    {
        try {
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

            if ($data->isEmpty()) {
                return $this->notFoundResponse('No crop types found');
            }

            return $this->successResponse(
                new CropTypeCollection($data),
                'Crop types retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $data = CropsType::find($id);

            if (!$data) {
                return $this->notFoundResponse('Crop type not found');
            }

            return $this->successResponse(
                new CropTypeResource($data),
                'Crop type retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

