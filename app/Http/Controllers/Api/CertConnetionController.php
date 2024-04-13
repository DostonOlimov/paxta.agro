<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\CropsName;
use App\Models\CropsType;
use App\Models\OrganizationCompanies;
use App\Models\PreparedCompanies;
use Illuminate\Http\Request;


class CertConnetionController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except('login');
    }
    public function login(Request $request)
    {
        $params = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        if (!auth()->attempt($params)) {
            return response()->json('bizda bunday user yoq');
        }

        $token = auth()->user()->createToken('authToken')->accessToken;
        return response()->json(['user' => $token]);
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
