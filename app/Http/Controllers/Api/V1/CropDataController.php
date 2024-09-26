<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\CropDataFilter;
use App\Http\Resources\V1\CompanyResource;
use App\Http\Resources\V1\CropDataCollection;
use App\Http\Resources\V1\CropDataResource;
use App\Models\CropData;
use App\Models\OrganizationCompanies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;


class CropDataController extends Controller
{
    public function index(Request $request, CropDataFilter $filter)
    {
        try {
            // Initialize query
            $query = CropData::query();

            // Retrieve filters and apply them
            $filters = $request->only(array_keys($filter->safeParams));
            $query = $filter->apply($query, $filters);

            // Retrieve the data
            $data = $query->get();

            if ($data->isEmpty()) {
                return $this->notFoundResponse('No crop data found');
            }

            // Return as a resource collection
            return $this->successResponse(
                new CropDataCollection($data),
                'Crop data retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            // Determine the query
            $query = CropData::query();

            // Find the crop data by ID
            $data = $query->find($id);

            if (!$data) {
                return $this->notFoundResponse('Crop data not found');
            }

            return $this->successResponse(
                new CropDataResource($data),
                'Crop data retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        // Define validation rules with camelCase attribute names
        $rules = [
            'nameId' => 'required|exists:crops_name,id',
            'kodtnved' => 'required|digits:10',
            'partyNumber' => 'required|string|max:255',
            'measureType' => 'required|integer',
            'amount' => 'required|numeric',
            'year' => 'required|integer',
            'toyCount' => 'required|integer',
            'countryId' => 'required|integer',
        ];

        // Define custom validation messages
        $messages = [
            'required' => 'The :attribute field is mandatory and cannot be left empty.',
            'exists' => 'The selected :attribute is invalid.',
            'digits' => 'The :attribute must be exactly 10 digits.',
            'integer' => 'The :attribute must be an integer.',
            'numeric' => 'The :attribute must be a numeric value.',
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

        // Create a new CropData record
        $cropData = CropData::create([
            'name_id' => $validatedData['nameId'],
            'kodtnved' => $validatedData['kodtnved'],
            'party_number' => $validatedData['partyNumber'],
            'measure_type' => $validatedData['measureType'],
            'amount' => $validatedData['amount'],
            'year' => $validatedData['year'],
            'toy_count' => $validatedData['toyCount'],
            'country_id' => $validatedData['countryId'],
        ]);

        // Return the created crop data as a resource
        return $this->successResponse(
            new CropDataResource($cropData),
            'Crop data created successfully',
            Response::HTTP_CREATED
        );
    }


    public function update(Request $request, $id)
    {
        try {
            // Define validation rules with camelCase attribute names
            $rules = [
                'nameId' => 'required|exists:crops_name,id',
                'kodtnved' => 'required|int|digits:10',
                'partyNumber' => 'required|string|max:255',
                'measureType' => 'required|int',
                'amount' => 'required',
                'year' => 'required|int',
                'toyCount' => 'required|int',
                'countryId' => 'required|int',
            ];

            // Define custom validation messages
            $messages = [
                'required' => 'The :attribute field is mandatory and cannot be left empty.',
                'exists' => 'The selected :attribute is invalid.',
                'digits' => 'The :attribute must be 10 digits.',
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
            $company = CropData::findOrFail($id);

            // Update the company record
            $company->update([
                'name_id' => $validatedData['nameId'],
                'kodtnved' => $validatedData['kodtnved'],
                'party_number' => $validatedData['partyNumber'],  // Map camelCase to snake_case
                'measure_type' => $validatedData['measureType'],
                'amount' => $validatedData['amount'],
                'year' => $validatedData['year'],
                'toy_count' => $validatedData['toyCount'],
                'country_id' => $validatedData['countryId']
            ]);

            // Return the updated company as a resource
            return $this->successResponse(
                new CropDataResource($company),
                'Crop Data updated successfully'
            );

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e);
        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

