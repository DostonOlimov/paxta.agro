<?php

namespace App\Http\Controllers;
use App\Models\OrganizationCompanies;
use App\Services\LocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyAjaxController extends Controller
{
    //get company
    public function getCompanyById(Request $request)
    {
        $id = $request->input('id');
        $company = OrganizationCompanies::with('city')->findOrFail($id);
        if ($company) {
            return response()->json([
                'name' => $company->name,
                'inn' => $company->inn,
                'ownerName' => $company->owner_name,
                'phoneNumber' => $company->phone_number,
                'address' => $company->address,
                'cityName' => optional($company->city)->name,
                'stateName' => optional(optional($company->city)->region)->name

            ]);
        }
    }

}
