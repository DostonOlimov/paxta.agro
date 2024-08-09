<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\CropData;
use App\Models\DefaultModels\tbl_activities;
use App\Models\Files;
use App\Models\PreparedCompanies;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AppOnlineController extends Controller
{
    public function appAdd(Request $request)
    {
        // Define validation rules
        $rules = [
            'name_id' => 'required',
            'country_id' => 'nullable|numeric',
            'kodtnved' => 'nullable|numeric',
            'party_number' => 'required|numeric',
            'measure_type' => 'nullable|numeric',
            'amount' => 'nullable|numeric',
            'year' => 'required|numeric',
            'toy_count' => 'nullable|numeric',
            'data' => 'nullable',
            'user_id' => 'required|numeric',
            'organization_id' => 'required|numeric',
            'prepared_id' => 'required|numeric'
        ];

        // Validate the request data
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create a new crop data record
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
            return response()->json(['message' => 'Crop not created'], 422);
        }

        // Create a new application record
        $application = new Application();
        $application->crop_data_id = $crop->id;
        $application->organization_id = $request->input('organization_id');
        $application->prepared_id = $request->input('prepared_id');
        $application->type = $request->input('type') ?? Application::TYPE_1;
        $application->date = Carbon::now()->format('Y-m-d');
        $application->status = Application::STATUS_NEW;
        $application->data = $request->input('data');
        $application->created_by = $request->input('user_id');
        $application->save();

        // Log the activity if the application was created successfully
        if ($application) {
            $activity = new tbl_activities();
            $activity->ip_address = $_SERVER['REMOTE_ADDR'];
            $activity->user_id = Auth::id();
            $activity->action_id = $application->id;
            $activity->action_type = 'app_add';
            $activity->action = "Ariza qo'shildi";
            $activity->time = now();
            $activity->save();

            return response()->json(['id' => $application->id], 201);
        }

        return response()->json(['message' => 'Application not created'], 422);
    }

    public function appsUser(Request $request)
    {
        $id = $request->input('id');
        $page = $request->input('page') ?? 1;
        $rows = 10;
        $year = $request->input('year') ?? now()->year;

        $applications = Application::with(['organization', 'prepared', 'crops.name', 'crops.type'])
            ->whereYear('date', $year)
            ->where('created_by', $id)
            ->where('status', '!=', Application::STATUS_DELETED)
            ->paginate($rows, ['*'], 'page', $page);

        if ($applications->isEmpty()) {
            return response()->json(null);
        }

        return response()->json($applications);
    }

    public function appView(Request $request)
    {
        // Define validation rules
        $rules = [
            'user_id' => 'required|numeric',
            'app_id' => 'required|numeric',
        ];

        // Validate the request data
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userId = $request->input('user_id');
        $appId = $request->input('app_id');

        // Find the application and related data
        $application = Application::with(['organization', 'prepared', 'crops.name', 'files'])
            ->where('created_by', $userId)
            ->where('id', $appId)
            ->first();

        if (!$application) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json($application);
    }

    public function appEdit(Request $request)
    {
        // Define validation rules
        $rules = [
            'app_id' => 'required|numeric',
            'crop_id' => 'required|numeric',
            'prepared_id' => 'required|numeric',
            'app_type' => 'nullable|numeric',
            'crop_name_id' => 'nullable|numeric',
            'crop_kodtnved' => 'nullable|numeric',
            'crop_party_number' => 'nullable|numeric',
            'crop_measure_type' => 'nullable|numeric',
            'crop_amount' => 'nullable|numeric',
            'crop_year' => 'nullable|numeric',
            'crop_sxeme_number' => 'nullable|numeric',
            'crop_toy_count' => 'nullable|numeric',
            'crop_country_id' => 'nullable|numeric',
            'prepared_name' => 'nullable|string',
            'prepared_kod' => 'nullable|string',
            'prepared_tara' => 'nullable|string',
            'prepared_state_id' => 'nullable|numeric',
        ];

        // Validate the request data
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update the application record
        $application = Application::findOrFail($request->input('app_id'));
        $application->update(['type' => $request->input('app_type')]);

        // Update the crop data record
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

        // Update the prepared company record
        $prepared = PreparedCompanies::findOrFail($request->input('prepared_id'));
        $prepared->update([
            'name' => $request->input('prepared_name'),
            'kod' => $request->input('prepared_kod') ?? 0,
            'tara' => $request->input('prepared_tara'),
            'state_id' => $request->input('prepared_state_id'),
        ]);

        return response()->json(['application' => $application, 'crop' => $crop, 'prepared' => $prepared], 200);
    }

    public function appDelete(Request $request)
    {
        $id = $request->input('id');

        // Find the application by ID and update its status to deleted
        $application = Application::findOrFail($id);
        $application->update(['status' => Application::STATUS_DELETED]);

        return response()->json(['message' => 'Application deleted successfully', 'application' => $application], 200);
    }

    public function appFile(Request $request)
    {
        // Define validation rules
        $rules = [
            'app_id' => 'required|numeric',
            'name' => 'required|string',
        ];

        // Validate the request data
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $appId = $request->input('app_id');
        $filePath = $request->input('name');

        // Find the application by ID
        $application = Application::find($appId);
        if (!$application) {
            return response()->json(['message' => 'Application ID not found'], 404);
        }

        // Create a new file record
        $file = Files::create([
            'app_id' => $appId,
            'name' => $filePath,
        ]);

        return response()->json(['message' => 'File added successfully', 'file' => $file], 200);
    }

    public function appFileFind($id)
    {
        // Find the file by application ID
        $file = Files::where('app_id', $id)->first();

        if (!$file) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return response()->json(['file' => $file], 200);
    }

    public function appFileUpdate(Request $request)
    {
        // Define validation rules
        $rules = [
            'app_id' => 'required|numeric',
            'file_path' => 'required|string',
        ];

        // Validate the request data
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $appId = $request->input('app_id');
        $filePath = $request->input('file_path');

        // Find the file by application ID and update it
        $file = Files::where('app_id', $appId)->firstOrFail();
        $file->update(['name' => $filePath]);

        return response()->json(['message' => 'File updated successfully', 'file' => $file], 200);
    }
}
