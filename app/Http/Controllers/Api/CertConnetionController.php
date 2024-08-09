<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\CropsName;
use App\Models\CropsType;
use App\Models\OrganizationCompanies;
use App\Models\PreparedCompanies;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class CertConnetionController extends Controller
{
    public function login(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to authenticate the user with the provided credentials
        if (!auth()->attempt($request->only('email', 'password'))) {
            return response()->json('User not found', 401);
        }

        $user = auth()->user();

        // Check if the user already has an API token, if not, create one
        if (!$user->api_token) {
            $user->api_token = $user->createToken('authToken')->accessToken;
            $user->save();
        }

        return response()->json(['user' => $user->api_token]);
    }

    public function cropName()
    {
        // Fetch all crop names from the database
        $cropData = CropsName::all();
        return response()->json(['success' => true, 'data' => $cropData]);
    }

    public function cropType(Request $request)
    {
        // Get the 'id' parameter from the request
        $nameId = $request->input('id');

        if ($nameId) {
            // Fetch crop types that match the given crop name ID
            $cropData = CropsType::where('crop_id', $nameId)->get();
            return response()->json(['success' => true, 'data' => $cropData]);
        }

        return response()->json(['success' => false, 'message' => 'Crop Type not found'], 404);
    }

    public function organizationCompany(Request $request)
    {
        // Get the 'data' parameter from the request
        $data = $request->input('data');

        if (isset($data['inn'])) {
            // Find or create an organization company by 'inn'
            $company = OrganizationCompanies::firstOrCreate(['inn' => $data['inn']], $data);
            return response()->json(['id' => $company->id]);
        }

        return response()->json(null);
    }

    public function orgCompyEdit(Request $request)
    {
        try {
            // Validation rules
            $rules = [
                'id' => 'required|numeric',
                'name' => 'required|string',
                'city_id' => 'required|numeric',
                'address' => 'required|string',
                'owner_name' => 'required|string',
                'phone_number' => 'required|string',
                'inn' => 'required|string',
            ];

            // Validate the request data
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            // Find the organization company by ID and update it
            $orgCompy = OrganizationCompanies::findOrFail($request->input('id'));
            $orgCompy->update($request->only(['name', 'city_id', 'address', 'owner_name', 'phone_number', 'inn']));

            return response()->json(['success' => true, 'data' => $orgCompy], 200);
        } catch (QueryException $e) {
            $errorMessage = $e->errorInfo[1] == 1452 ?
                'Foreign key constraint violation: The organization ID provided does not exist.' :
                'Database error: ' . $e->getMessage();
            return response()->json(['success' => false, 'message' => $errorMessage], 500);
        }
    }

    public function orgCompyView(Request $request)
    {
        // Get the 'id' parameter from the request
        $id = $request->input('id');

        // Find the organization company by ID and include related city and region
        $company = OrganizationCompanies::with(['city.region'])->find($id);

        if ($company) {
            return response()->json($company);
        }

        return response()->json(false);
    }

    public function preparedCompany(Request $request)
    {
        // Get parameters from the request
        $name = $request->input('name');
        $countryId = $request->input('country_id');
        $stateId = $request->input('state_id');
        $kod = $request->input('kod');
        $tara = $request->input('tara');

        if ($name !== null) {
            // Find or create a prepared company by name, state ID, kod, and tara
            $company = PreparedCompanies::firstOrCreate(
                ['name' => $name, 'state_id' => $stateId, 'kod' => $kod, 'tara' => $tara],
                ['country_id' => $countryId]
            );

            return response()->json(['id' => $company->id]);
        }

        return response()->json(null);
    }
}
