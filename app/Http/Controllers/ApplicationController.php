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
            $query = Application::query()
                ->select('applications.id as application_id', 'applications.*');

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
//
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

        $user = Auth::user();

        $crop = CropData::create([
            'name_id'       => $request->input('name'),
            'country_id'    => $request->input('country'),
            'kodtnved'      => $request->input('tnved'),
            'party_number'  => $request->input('party_number'),
            'measure_type'  => $request->input('measure_type'),
            'amount'        => $request->input('amount'),
            'year'          => $request->input('year'),
            'toy_count'     => $request->input('toy_count'),
            'sxeme_number'  => $request->input('sxeme_number'),
        ]);

        $application = Application::create([
            'crop_data_id'     => $crop->id,
            'organization_id'  => $request->input('organization'),
            'prepared_id'      => $request->input('prepared'),
            'type'             => Application::TYPE_1,
            'date'             => $request->input('dob') ? date('Y-m-d', strtotime($request->input('dob'))) : null,
            'status'           => Application::STATUS_FINISHED,
            'data'             => $request->input('data'),
            'created_by'       => $user->id,
        ]);

        tbl_activities::create([
            'ip_adress'   => request()->ip(),
            'user_id'     => $user->id,
            'action_id'   => $application->id,
            'action_type' => 'app_add',
            'action'      => "Ariza qo'shildi",
            'time'        => now(),
        ]);

        return redirect()->route('listapplication')->with('message', 'Successfully Submitted');
    }

    // application edit

    public function edit($id)
    {
        $title = "Arizani o'zgartirish";
        $app = Application::findOrFail($id); // Use findOrFail to handle missing records

        $type = Application::getType();
        $names = DB::table('crops_name')->get();
        $countries = DB::table('tbl_countries')->get();
        $measure_types = CropData::getMeasureType();
        $year = CropData::getYear();

        return view('application.edit', compact('app', 'type', 'names', 'countries', 'measure_types', 'year', 'title'));
    }


// application update

    public function update($id, Request $request)
    {
        $user = Auth::user();
        $app = Application::findOrFail($id); // Use findOrFail for better error handling

        $app->update([
            'organization_id' => $request->input('organization'),
            'prepared_id'     => $request->input('prepared'),
            'date'            => $request->input('dob') ? date('Y-m-d', strtotime($request->input('dob'))) : null,
            'data'            => $request->input('data'),
        ]);

        $crop = CropData::findOrFail($app->crop_data_id); // Same for CropData

        $crop->update([
            'name_id'       => $request->input('name'),
            'country_id'    => $request->input('country'),
            'kodtnved'      => $request->input('tnved'),
            'party_number'  => $request->input('party_number'),
            'measure_type'  => $request->input('measure_type'),
            'amount'        => $request->input('amount'),
            'year'          => $request->input('year'),
            'toy_count'     => $request->input('toy_count'),
            'sxeme_number'     => $request->input('sxeme_number'),
        ]);

        tbl_activities::create([
            'ip_adress'   => request()->ip(),
            'user_id'     => $user->id,
            'action_id'   => $app->id,
            'action_type' => 'app_edit',
            'action'      => "Ariza O'zgartirildi",
            'time'        => now(),
        ]);

        return redirect()->route('listapplication')->with('message', 'Successfully Updated');
    }

    public function showapplication($id)
    {
        $app = Application::findOrFail($id);
        $company = OrganizationCompanies::with('city')->findOrFail($app->organization_id);

        return view('application.show', compact('app', 'company'));
    }

    //accept online applications
    public function accept($id)
    {
        $app = Application::findOrFail($id);
        $this->authorize('update', $app);

        $app->update([
            'status'       => Application::STATUS_ACCEPTED,
            'progress'     => Application::PROGRESS_ANSWERED,
            'accepted_date'=> now(),
            'accepted_id'  => Auth::id(),
        ]);

        return redirect()->route('listapplication')->with('message', 'Successfully Accepted');
    }

    //reject online applications
    public function reject($id)
    {
        $app = Application::findOrFail($id);

        return view('application.reject', compact('app'));
    }

    public function reject_store(Request $request)
    {
        $app = Application::findOrFail($request->input('app_id'));
        $this->authorize('accept', $app);

        $app->update([
            'status' => Application::STATUS_REJECTED,
        ]);

        AppStatusChanges::create([
            'app_id'  => $app->id,
            'status'  => Application::STATUS_REJECTED,
            'comment' => $request->input('reason'),
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('listapplication')->with('message', 'Application Rejected Successfully');
    }

    //getting safe params for filter
    private function getFilters(Request $request, ApplicationFilter $filter): array
    {
        return $request->only(array_keys($filter->safeParams));
    }

}

