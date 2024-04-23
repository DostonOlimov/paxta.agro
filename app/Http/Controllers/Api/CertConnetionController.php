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

    public function __construct()
    {
        $this->middleware('auth')->except('login');
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        if (!auth()->attempt($request->only('email', 'password'))) {
            return response()->json('User not found', 401);
        }

        $user = auth()->user();

        if (!$user->api_token) {
            $user->api_token = $user->createToken('authToken')->accessToken;
            $user->save();
        }

        return response()->json(['user' => $user->api_token]);
    }

    public function crop_name()
    {
        $cropData = CropsName::get();
        // dd(request()->getHost());
        return response()->successJson($cropData);
    }
    public function crop_type(Request $request)
    {
        $name_id = $request->input('id');

        if ($name_id) {
            $cropData = CropsType::where('crop_id', $name_id)->get();

            return response()->successJson($cropData);
        }
        return response()->errorJson(null, 404, 'Crop Type not found');
    }

    public function organization_company(Request $request)
    {
        // unset($data['id']);
        $data = $request->all()['data'];

        if (isset($data['inn'])) {
            $model = OrganizationCompanies::where('inn', $data['inn'])->first();

            if ($model) {
                return response()->json($model->id);
            } else {
                $data = OrganizationCompanies::create($data);
                return response()->json($data->id);
            }
        } else {
            return response()->json(null);
        }
    }

    public function org_compy_edit(Request $request)
    {
        try {
            $rules = [
                'id' => 'required|numeric',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->errorJson($validator->errors(), 422, 'Validation error');
            }

            $org_compy = OrganizationCompanies::findOrFail($request->input('id'));
            $org_compy->update([
                'name'  => $request->input('name'),
                'city_id'  => $request->input('city'),
                'address'  => $request->input('address'),
                'owner_name'  => $request->input('owner_name'),
                'phone_number'  => $request->input('mobile'),
                'inn'  => $request->input('inn'),
            ]);
            if (!$org_compy) {
                return response()->errorJson(null, 404, 'Organization Companies ID Not found');
            }

            return response()->successJson($org_compy, 200);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1452) {
                return response()->errorJson(null, 422, 'Foreign key constraint violation: The organization ID provided does not exist.');
            }

            return response()->errorJson(null, 500, 'Database error: ' . $e->getMessage());
        }
    }

    public function org_compy_view(Request $request)
    {
        // unset($data['id']);
        $id = $request->input('id');

        $model = OrganizationCompanies::with(['city.region'])->find($id);

        if ($model) {
            return response()->json($model);
        } else {
            return response()->json(false);
        }
    }
    public function prepared_company(Request $request)
    {
        $name = $request->input('name');
        $country_id = $request->input('country_id');
        $state_id = $request->input('state_id');
        $kod = $request->input('kod');
        $tara = $request->input('tara');
        if ($name !== null) {
            $model = PreparedCompanies::where('name', 'like', $name)
                // ->where('country_id', $country_id)
                ->where('state_id', $state_id)
                ->where('kod', $kod)
                ->where('tara', $tara)
                ->first();

            if ($model) {
                return response()->json($model->id);
            } else {
                $newModel = PreparedCompanies::create([
                    'name' => $name,
                    // 'country_id' => $country_id,
                    'state_id' => $state_id,
                    'kod' => $kod,
                    'tara' => $tara,
                ]);
                return response()->json($newModel->id);
            }
        } else {
            return response()->json(null);
        }
    }
}
