<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\ApplicationFilter;
use App\Http\Resources\V1\ApplicationCollection;
use App\Http\Resources\V1\ApplicationResource;
use App\Http\Resources\V1\CropDataResource;
use App\Models\Application;
use App\Models\CropData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;


class ApplicationController extends Controller
{
    const PAGINATE = 10;

    public function index(Request $request, ApplicationFilter $filter)
    {
        try {
            // Validate the 'includeCities' parameter
            $page = filter_var($request->query('page'), FILTER_VALIDATE_INT) ?? 1;

            $query = Application::query();

            // Extract filters from request
            $filters = $this->getFilters($request, $filter);

            // Apply filters to the query
            $filteredQuery = $filter->apply($query, $filters);

            // Get the results
            $data = $filteredQuery->with('crops')
                ->with('organization')
                ->with('prepared')
                ->latest('id')
                ->paginate(self::PAGINATE, ['*'], 'page', $page);

//            if ($data->isEmpty()) {
//                return $this->notFoundResponse('No applications found');
//            }

            return $this->successResponse(
                new ApplicationCollection($data),
                'Applications retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id)
    {
        try {
            $data = Application::with('crops')
                ->with('organization')
                ->with('prepared')
                ->find($id);

            if (!$data) {
                return $this->notFoundResponse('Application not found');
            }

            return $this->successResponse(
                new ApplicationResource($data),
                'Application retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        // Define validation rules with camelCase attribute names
        $rules = [
            'dataId' => 'required|exists:crop_data,id',
            'companyId' => 'required|exists:organization_companies,id',
            'factoryId' => 'required|exists:prepared_companies,id',
            'type' => 'required|int',
            'createdBy' => 'required|int',
        ];

        // Define custom validation messages
        $messages = [
            'required' => 'The :attribute field is mandatory and cannot be left empty.',
            'exists' => 'The selected :attribute is invalid.',
            'int' => 'The :attribute must be integer.',
            // Add other custom messages here
        ];

        // Validate the request data
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validatedData = $validator->validated();

        // Create a new company record
        $application = Application::create([
            'crop_data_id' => $validatedData['dataId'],
            'date' =>  date('Y-m-d'),
            'organization_id' => $validatedData['companyId'],
            'type' => $validatedData['type'],  // Map camelCase to snake_case
            'prepared_id' => $validatedData['factoryId'],
            'created_by' => $validatedData['createdBy'],
            'status' => Application::STATUS_NEW,
        ]);

        // Return the created company as a resource
        return $this->successResponse(
            new ApplicationResource($application),
            'Application created successfully',
            Response::HTTP_CREATED
        );
    }

    public function update(Request $request, $id)
    {
        try {
            // Define validation rules with camelCase attribute names
            $rules = [
                'dataId' => 'required|exists:crop_data,id',
                'companyId' => 'required|exists:organization_companies,id',
                'factoryId' => 'required|exists:prepared_companies,id',
                'type' => 'required|int',
                'createdBy' => 'required|int',
            ];

            // Define custom validation messages
            $messages = [
                'required' => 'The :attribute field is mandatory and cannot be left empty.',
                'exists' => 'The selected :attribute is invalid.',
                'int' => 'The :attribute must be integer.',
                // Add other custom messages here
            ];

            // Validate the request data
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $validatedData = $validator->validated();

            // Find the existing company record
            $company = Application::findOrFail($id);

            // Update the company record
            $company->update([
                'crop_data_id' => $validatedData['dataId'],
                'organization_id' => $validatedData['companyId'],
                'type' => $validatedData['type'],  // Map camelCase to snake_case
                'prepared_id' => $validatedData['factoryId'],
                'created_by' => $validatedData['createdBy'],
            ]);

            // Return the updated company as a resource
            return $this->successResponse(
                new ApplicationResource($company),
                'Application updated successfully'
            );

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e);
        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    private function getFilters(Request $request, ApplicationFilter $filter): array
    {
        return $request->only(array_keys($filter->safeParams));
    }
}

