<?php

namespace App\Http\Controllers;


use App\Filters\V1\ApplicationFilter;
use App\Models\Application;
use App\Models\AppStatusChanges;
use App\Models\CropData;
use App\Models\CropsName;
use App\Models\OrganizationCompanies;
use App\Models\Region;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\DefaultModels\tbl_activities;
use Symfony\Component\HttpFoundation\Response;

class ApplicationController extends Controller
{
    public function applicationlist(Request $request, ApplicationFilter $filter)
    {
//        try {
            // Default sorting by 'id' and order by 'desc'
            $sort_by = $request->get('sort_by', 'id');
            $sort_order = $request->get('sort_order', 'desc');

            // Extract filters from request
            $filters = $this->getFilters($request, $filter);

            // Initialize filter values for use in the view
            $filterValues = array_map(fn($conditions) => reset($conditions), $filters);

            // Start building the query
            $query = Application::query();

            // Apply filters and sorting to the query
            $filteredQuery = $filter->apply($query, $filters);
            $sortedQuery = $filter->applySorting($filteredQuery, $sort_by, $sort_order);

            // Arrays for filter selects
            $all_status = Application::getStatus();
            $names = CropsName::all();
            $states = Region::all();
            $years = CropData::getYear();

            // Fetch organization data if companyId filter is applied
            $organization = $filterValues['companyId'] ?? null
                    ? OrganizationCompanies::find($filterValues['companyId'])
                    : null;

            // Fetch the paginated results with relationships
            $apps = $sortedQuery->with(['crops', 'organization', 'prepared'])
                ->paginate(50);

            // Return the view with necessary data
            return view('application.list', compact(
                'apps', 'all_status', 'names', 'years', 'organization',
                'filterValues', 'sort_by', 'sort_order', 'states'
            ));

//        } catch (\Throwable $e) {
//            // Log the error for debugging
//            \Log::error($e);
//            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
//        }
    }



    // application addform

    public function addapplication()
    {
        $names = DB::table('crops_name')->get()->toArray();
        $countries = DB::table('tbl_countries')->get()->toArray();
        $measure_types = CropData::getMeasureType();
        $year = CropData::getYear();

        return view('application.add',compact('names', 'countries','measure_types','year'));

    }


    // application store

    public function store(Request $request)
    {
        $this->authorize('create', Application::class);
        $userA = Auth::user();
        $crop = new CropData();
        $crop->name_id = $request->input('name');
        $crop->country_id = $request->input('country');
        $crop->kodtnved = $request->input('tnved');
        $crop->party_number = $request->input('party_number');
        $crop->measure_type = $request->input('measure_type');
        $crop->amount = $request->input('amount');
        $crop->year = $request->input('year');
        $crop->toy_count = $request->input('toy_count');
        $crop->sxeme_number = 7;
        $crop->save();
        $id = $crop->id;

        $app = new Application();
        $app->crop_data_id = $id;
        $app->organization_id = $request->input('organization');
        $app->prepared_id = $request->input('prepared');
        $app->type = Application::TYPE_1;
        $app->date = join('-', array_reverse(explode('-', $request->input('dob'))));
        $app->status = Application::STATUS_FINISHED;
        $app->data = $request->input('data');
        $app->created_by = $userA->id;
        $app->save();

        $active = new tbl_activities;
        $active->ip_adress = $_SERVER['REMOTE_ADDR'];
        $active->user_id = $userA->id;
        $active->action_id = $app->id;
        $active->action_type = 'app_add';
        $active->action = "Ariza qo'shildi";
        $active->time = date('Y-m-d H:i:s');
        $active->save();

        return redirect('/application/list')->with('message', 'Successfully Submitted');

    }

    // application edit

    public function edit($id)
    {
        $editid = $id;
        $title = "Arizani o'zgartirish";
        $app = Application::find($editid);

        $type = Application::getType();
        $names = DB::table('crops_name')->get()->toArray();
        $countries = DB::table('tbl_countries')->get()->toArray();
        $measure_types = CropData::getMeasureType();
        $year = CropData::getYear();

        return view('application.edit', compact('app', 'type', 'names', 'countries', 'measure_types', 'year', 'title'));
    }


    // application update

    public function update($id, Request $request)
    {
        $userA = Auth::user();
        $app = Application::find($id);

        $app->organization_id = $request->input('organization');
        $app->prepared_id = $request->input('prepared');
        $app->date = join('-', array_reverse(explode('-', $request->input('dob'))));
        $app->data = $request->input('data');
        $app->save();

        $crop =CropData::find($app->crop_data_id);
        $crop->name_id = $request->input('name');
        $crop->country_id = $request->input('country');
        $crop->kodtnved = $request->input('tnved');
        $crop->party_number = $request->input('party_number');
        $crop->measure_type = $request->input('measure_type');
        $crop->amount = $request->input('amount');
        $crop->year = $request->input('year');
        $crop->toy_count = $request->input('toy_count');
        $crop->save();

        $active = new tbl_activities;
        $active->ip_adress = $_SERVER['REMOTE_ADDR'];
        $active->user_id = $userA->id;
        $active->action_id = $app->id;
        $active->action_type = 'app_edit';
        $active->action = "Ariza O'zgartirildi";
        $active->time = date('Y-m-d H:i:s');
        $active->save();
        return redirect('/application/list')->with('message', 'Successfully Updated');

    }

    public function showapplication($id)
    {
        $user = Application::findOrFail($id);
        $company = OrganizationCompanies::with('city')->findOrFail($user->organization_id);

        return view('application.show', compact('user','company'));
    }

    public function accept($id)
    {
        $app = Application::find($id);
        $this->authorize('update', $app);
        $app->status = Application::STATUS_ACCEPTED;
        $app->progress = Application::PROGRESS_ANSWERED;
        $app->accepted_date = date('Y-m-d');
        $app->accepted_id = Auth::user()->id;
        $app->save();
        return redirect('application/list')->with('message', 'Successfully Submitted');
    }
    public function reject(Request $request, $id)
    {
         $app = Application::find($id);

         return view('application.reject', compact('app'));
    }
    public function reject_store(Request $request)
    {
        $app_id = $request->input('app_id');
        $reason = $request->input('reason');
        $app = Application::find($app_id);
        $this->authorize('accept', $app);
        $app->status = Application::STATUS_REJECTED;
        $app->save();
         $changes = new AppStatusChanges();
         $changes->app_id = $app_id;
         $changes->status = Application::STATUS_REJECTED;
         $changes->comment = $reason;
         $changes->user_id = Auth::user()->id;
         $changes->save();

        return redirect('application/list')->with('message', 'Successfully Submitted');
    }

    private function getFilters(Request $request, ApplicationFilter $filter): array
    {
        return $request->only(array_keys($filter->safeParams));
    }

}

