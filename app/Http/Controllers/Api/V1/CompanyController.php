<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\CompanyFilter;
use App\Http\Resources\V1\CompanyCollection;
use App\Http\Resources\V1\CompanyResource;
use App\Models\OrganizationCompanies;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    public function index(Request $request, CompanyFilter $filter)
    {
        try {
            $query = OrganizationCompanies::query();

            // Extract filters from request
            $filters = $this->getFilters($request, $filter);

            // Apply filters to the query
            $filteredQuery = $filter->apply($query, $filters);

            // Get the results
            $data = $filteredQuery->get();

            // Check if data is found
            if ($data->isEmpty()) {
                return $this->notFoundResponse('No companies found');
            }

            // Return the results in a collection
            return $this->successResponse(
                new CompanyCollection($data),
                'Companies retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function show(int $id)
    {
        try {
            // Find data with the given id
            $data = OrganizationCompanies::find($id);

            // Check if data is found
            if (!$data) {
                return $this->notFoundResponse('Company not found');
            }

            // Return the found data as a resource
            return $this->successResponse(
                new CompanyResource($data),
                'Company retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function store(Request $request)
    {
        // Define validation rules with camelCase attribute names
        $rules = [
            'inn' => 'required|int|digits:9',
            'name' => 'required|string|max:255',
            'cityId' => 'required|exists:tbl_cities,id',
            'address' => 'nullable|string',
            'ownerName' => 'nullable|string|max:255',
            'phoneNumber' => 'nullable|string|max:255',
        ];

        // Define custom validation messages
        $messages = [
            'required' => 'The :attribute field is mandatory and cannot be left empty.',
            'exists' => 'The selected :attribute is invalid.',
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

        // Check if a company with the same INN already exists
        $existingCompany = OrganizationCompanies::where('inn', $validatedData['inn'])->first();

        if ($existingCompany) {
            return $this->successResponse(
                new CompanyResource($existingCompany),
                'Company already exists'
            );
        }

        // Create a new company record
        $company = OrganizationCompanies::create([
            'inn' => $validatedData['inn'],
            'name' => $validatedData['name'],
            'city_id' => $validatedData['cityId'],  // Map camelCase to snake_case
            'address' => $validatedData['address'],
            'owner_name' => $validatedData['ownerName'],
            'phone_number' => $validatedData['phoneNumber'],
        ]);

        // Return the created company as a resource
        return $this->successResponse(
            new CompanyResource($company),
            'Company created successfully',
            Response::HTTP_CREATED
        );
    }

    public function update(Request $request, $id)
    {
        try {
            // Define validation rules with camelCase attribute names
            $rules = [
                'inn' => 'required|int|digits:9',
                'name' => 'required|string|max:255',
                'cityId' => 'required|exists:tbl_cities,id',
                'address' => 'nullable|string',
                'ownerName' => 'nullable|string|max:255',
                'phoneNumber' => 'nullable|string|max:255',
            ];

            // Define custom validation messages
            $messages = [
                'required' => 'The :attribute field is mandatory and cannot be left empty.',
                'exists' => 'The selected :attribute is invalid.',
                'int' => 'The :attribute must be integer.',
                'digits' => 'The :attribute must be 9 digits.',
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
            $company = OrganizationCompanies::findOrFail($id);

            // Update the company record
            $company->update([
                'inn' => $validatedData['inn'],
                'name' => $validatedData['name'],
                'city_id' => $validatedData['cityId'],  // Map camelCase to snake_case
                'address' => $validatedData['address'],
                'owner_name' => $validatedData['ownerName'],
                'phone_number' => $validatedData['phoneNumber'],
            ]);

            // Return the updated company as a resource
            return $this->successResponse(
                new CompanyResource($company),
                'Company updated successfully'
            );

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e);
        } catch (\Exception $e) {
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function getFilters(Request $request, CompanyFilter $filter): array
    {
        return $request->only(array_keys($filter->safeParams));
    }

}
