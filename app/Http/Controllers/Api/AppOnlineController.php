<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppForeignFile;
use App\Models\Application;
use App\Models\CropData;
use App\Models\CropsName;
use App\Models\DefaultModels\tbl_activities;
use App\Models\Files;
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
            'country_id' => 'required|numeric',
            'kodtnved' => 'nullable|numeric',
            'party_number' => 'required|numeric',
            'measure_type' => 'nullable|numeric',
            'amount' => 'required|numeric',
            'year' => 'required|numeric',
            'toy_count' => 'numeric',
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
        $application->status = Application::STATUS_FINISHED;
        $application->data = $request->input('data')??null;
        $application->type = $request->input('type')??1;
        $application->created_by = $userA->id;
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

        $user = Application::with(['organization', 'prepared', 'crops.name', 'foreign_file', 'crops.type', 'comment'])->where('created_by', $user_id)->where('id', $app_id)->first();
        if (!$user) {
            return response()->errorJson(false, 404, 'Not found');
        }

        return response()->successJson($user, 200);
    }

    public function app_edit(Request $request)
    {
        try {
            $rules = [
                'id' => 'required|numeric',
                'name_id' => 'required',
                'country_id' => 'required|numeric',
                'kodtnved' => 'nullable|numeric',
                'party_number' => 'required|numeric',
                'measure_type' => 'nullable|numeric',
                'amount' => 'required|numeric',
                'year' => 'numeric',
                'toy_count' => 'numeric',
                // 'date'=>'nullable',
                // 'data'=>'nullable',
                'user_id' => 'required|numeric',
                'organization_id' => 'required|numeric',
                'prepared_id' => 'required|numeric'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->errorJson($validator->errors(), 422, 'Validation error');
            }

            $application = Application::findOrFail($request->input('id'));
            $application->update([
                'organization_id' => $request->input('crop_data_id'),
                'prepared_id' => $request->input('organization_id'),
                'date' => $request->input('date'),
                'data'  => $request->input('data')??null
            ]);

            if (!$application) {
                return response()->errorJson(null, 404, 'Application ID Not found');
            }

            return response()->successJson($application, 200);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1452) {
                return response()->errorJson(null, 422, 'Foreign key constraint violation: The organization ID provided does not exist.');
            }

            return response()->errorJson(null, 500, 'Database error: ' . $e->getMessage());
        }
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
}
