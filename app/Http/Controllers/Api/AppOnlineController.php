<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppForeignFile;
use App\Models\Application;
use App\Models\CropData;
use App\Models\CropsName;
use App\Models\DefaultModels\tbl_activities;
use App\Models\Files;
use App\Models\PreparedCompanies;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AppOnlineController extends Controller
{

    public function app_add(Request $request)
    {
        $rules = [
            'name_id' => 'required',
            'country_id' => 'nullable|numeric',
            'kodtnved' => 'nullable|numeric',
            'party_number' => 'required|numeric',
            'measure_type' => 'nullable|numeric',
            'amount' => 'nullable|numeric',
            'year' => 'required|numeric',
            'toy_count' => 'nullable|numeric',
            'data'=>'nullable',
            'user_id' => 'required|numeric',
            'organization_id' => 'required|numeric',
            'prepared_id' => 'required|numeric'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->errorJson($validator->errors(), 422, 'Validation error');
        }

        // $cropCheck = CropsName::find($request->input('name_id'));

        // if (!$cropCheck) {
        //     return response()->errorJson(null, 404, 'Crop Name not found');
        // }

        $userA = Auth::user();
        $crop = CropData::create([
            "name_id" => $request->input('name_id'),
            "country_id" => $request->input('country_id'),
            "kodtnved" => $request->input('kodtnved'),
            "party_number" => $request->input('party_number'),
            "measure_type" => $request->input('measure_type'),
            "amount" => $request->input('amount'),
            "year" => $request->input('year'),
            "toy_count" => $request->input('toy_count'),
            "sxeme_number" => 7,
        ]);
        if (!$crop) {
            return response()->errorJson(null, 422, 'Crop not created');
        }

        $now = Carbon::now()->format('Y-m-d');

        $application = new Application();
        $application->crop_data_id = $crop->id;
        $application->organization_id = $request->input('organization_id');
        $application->prepared_id = $request->input('prepared_id');
        $application->type = Application::TYPE_1;
        $application->date = $now;
        $application->status = Application::STATUS_NEW;
        $application->data = $request->input('data')??null;
        $application->type = $request->input('type')??1;
        $application->created_by =$request->input('user_id');
        $application->save();

        if ($application) {
            $active = new tbl_activities();
            $active->ip_adress = $_SERVER['REMOTE_ADDR'];
            $active->user_id = $userA->id;
            $active->action_id = $application->id;
            $active->action_type = 'app_add';
            $active->action = "Ariza qo'shildi";
            $active->time = date('Y-m-d H:i:s');
            $active->save();
            return response()->successJson($application->id, 201, 'Application created');
        }
        return response()->errorJson(null, 422, 'Application not created');
    }


    public function apps_user(Request $request)
    {
        $id = $request->input('id');
        $page = $request->input('page') ?? 1;
        $rows = 10;
        $year = $request->input('year') ?? now()->year;
        $user = Application::withoutGlobalScopes()->with(['organization', 'prepared', 'crops.name', 'crops.type'])->whereYear('date', $year)
            ->where('created_by', $id)
            ->where('status', '!=', Application::STATUS_DELETED)
            ->paginate($rows, ['*'], 'page', $page);

        if (!isset($user)) {
            return response()->json(null);
        }
        return response()->json($user);
    }
    public function app_view(Request $request)
    {
        $rules = [
            'user_id' => 'required|numeric',
            'app_id' => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->errorJson($validator->errors(), 422, 'Validation error');
        }

        $user_id = $request->input('user_id');
        $app_id = $request->input('app_id');

        $user = Application::with(['organization', 'prepared', 'crops.name', 'files'])->where('created_by', $user_id)->where('id', $app_id)->first();
        if (!$user) {
            return response()->errorJson(false, 404, 'Not found');
        }

        return response()->successJson($user, 200);
    }

    public function app_edit(Request $request)
    {
            $rules = [
                'app_id' => 'required|numeric',
                'crop_id' => 'required|numeric',
                'prepared_id' => 'required|numeric',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->errorJson($validator->errors(), 422, 'Validation error');
            }

            $application = Application::findOrFail($request->input('app_id'));
            $application->update([
                'type'  => $request->input('app_type'),
            ]);
            $crop = CropData::findOrFail($request->input('crop_id'));
            $crop->update([
                'name_id' => $request->input('crop_name_id'),
                'kodtnved' => $request->input('crop_kodtnved'),
                'party_number' => $request->input('crop_party_number'),
                'measure_type' => $request->input('crop_measure_type'),
                'amount' => $request->input('crop_amount'),
                'year' => $request->input('crop_year'),
                'sxeme_number' => $request->input('crop_sxeme_number'),
                'toy_count' => $request->input('crop_toy_count'),
                'country_id' => $request->input('crop_country_id'),
            ]);
            $prepared = PreparedCompanies::findOrFail($request->input('prepared_id'));
            $prepared->update([
                'name'  => $request->input('prepared_name'),
                'kod'  => $request->input('prepared_kod')??0,
                'tara'  => $request->input('prepared_tara'),
                'state_id'  => $request->has('prepared_state_id') ? $request->input('prepared_state_id') : null,
            ]);

            if (!$crop) {
                return response()->errorJson(null, 404, 'Crop Data ID Not found');
            }

            return response()->successJson(['app' => $application, 'crop' => $crop, 'prepared' => $prepared], 200);

    }
    public function app_delete(Request $request)
    {
        $id = $request->id;

        $application = Application::where('id', $id)->first();
        if (!$application) {
            return response()->errorJson([], 404, 'Application not found');
        }

        $application->update(['status' => Application::STATUS_DELETED]);

        return response()->successJson($application, 200, 'Application deleted successfully');
    }
    public function app_file(Request $request)
    {
        $rules = [
            'app_id' => 'required|numeric',
            'name' => 'required|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->errorJson($validator->errors(), 422, 'Validation error');
        }
        $app_id = $request->input('app_id');
        $file_path = $request->input('name');

        $application = Application::find($app_id);

        if (!$application) {
            return response()->errorJson(null, 404, 'Application ID Not found');
        }


        $result = Files::create([
            'app_id' => $app_id,
            'name' => $file_path,       //qilish kerak
        ]);

        return response()->successJson($result->app_id, 200, 'File add application successfully');
    }

    public function app_file_find($id)
    {
        $result = Files::where('app_id', $id)->first();

        return response()->successJson($result, 200, 'File find application successfully');
    }

    public function app_file_update(Request $request)
    {
        $rules = [
            'app_id' => 'required|numeric',
            'file_path' => 'required|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->errorJson($validator->errors(), 422, 'Validation error');
        }
        $app_id = $request->input('app_id');
        $file_path = $request->input('file_path');


        $application = Files::where('app_id', $app_id)->firstOrFail();

        $result = $application->update([
            'name' => $file_path,
        ]);

        return response()->successJson($result, 200, 'File add application successfully');
    }
}
