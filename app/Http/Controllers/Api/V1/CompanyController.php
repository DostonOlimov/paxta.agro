<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\CompanyFilter;
use App\Http\Resources\V1\CompanyCollection;
use App\Http\Resources\V1\CompanyResource;
use App\Models\OrganizationCompanies;
use Illuminate\Http\Request;


class CompanyController extends Controller
{
    public function index(Request $request, CompanyFilter $filter)
    {
        $query = OrganizationCompanies::query();

        // Extract filters from request
        $filters = $this->getFilters($request, $filter);

        // Apply filters to the query
        $filteredQuery = $filter->apply($query, $filters);

        // Get the results
        $data = $filteredQuery->get();

        return new CompanyCollection($data);
    }

    public function show(int $id)
    {
        // find data with given id
        $data = OrganizationCompanies::findOrFail($id);

        return new CompanyResource($data);
    }
    public function store(Request $request)
    {

        // Validate the request data
        $validatedData = $request->validate([
            'inn' => 'required|int|digits:9',
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:tbl_cities,id',
            'address' => 'nullable|string',
            'owner_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:255',
        ],);
        
        // Check if a company with the same INN already exists
        $existingCompany = OrganizationCompanies::where('inn', $validatedData['inn'])->first();

        if ($existingCompany) {
            return new CompanyResource($existingCompany); // company exist
        }

        // Create a new company record
        $company = OrganizationCompanies::create($validatedData);

        // Return the created company as a resource
        return new CompanyResource($company);
    }
    private function getFilters(Request $request, CompanyFilter $filter): array
    {
        return $request->only(array_keys($filter->safeParams));
    }

}
