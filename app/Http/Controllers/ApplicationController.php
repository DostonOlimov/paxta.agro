<?php

namespace App\Http\Controllers;


use App\Filters\V1\ApplicationFilter;
use App\Models\Application;
use App\Models\AppStatusChanges;
use App\Models\CropData;
use App\Models\Decision;
use App\Models\OrganizationCompanies;
use App\Models\TestPrograms;
use App\Rules\CheckingParyNumber;
use App\Services\SearchService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\DefaultModels\tbl_activities;
use Symfony\Component\HttpFoundation\Response;

class ApplicationController extends Controller
{
    public function applicationlist(Request $request, ApplicationFilter $filter,SearchService $service)
    {
        try {
            $names = getCropsNames();
            $states = getRegions();
            $years = getCropYears();
            $all_status = getAppStatus();

                return $service->search(
                    $request,
                    $filter,
                    Application::class,
                    [
                        'crops',
                        'organization',
                        'prepared'
                    ],
                    compact('names', 'states', 'years','all_status'),
                    'application.list',
                    [],
                    false,
                    null,
                    null,
                    []
                );

        } catch (\Throwable $e) {
            // Log the error for debugging
            \Log::error($e);
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    // application addform

    public function addapplication()
    {
        $crop = session('crop', 1);
        $names = DB::table('crops_name')->where('id', ($crop != 2) ? '=' : '!=', 1)->get()->toArray();
        $countries = DB::table('tbl_countries')->get()->toArray();
        $measure_types = CropData::getMeasureType();
        $years = CropData::getYear();
        $year = session('year', 2024);

        return view('application.add',compact('names', 'countries','measure_types','years','year'));

    }


    // application store

    public function store(Request $request)
    {
        $this->authorize('create', Application::class);

        $user = Auth::user();
//        $validated = $request->validate([
//            'party_number' => ['required', new CheckingParyNumber($request->input('prepared'),$request->input('party_number'))],
//        ]);


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
            'app_type'         => session('crop', 1)
        ]);

        tbl_activities::create([
            'ip_adress'   => request()->ip(),
            'user_id'     => $user->id,
            'action_id'   => $application->id,
            'action_type' => 'app_add',
            'action'      => "Ariza qo'shildi",
            'time'        => now(),
        ]);
        if(session('crop') == 3){
            // Create new decision
            $decision = Decision::create([
                'app_id'       => $application->id,
                'director_id'  => 27, // Hardcoded, but ideally fetched dynamically
                'number'       => $application->id,
                'laboratory_id'=> 2,
                'created_by'   => $user->id,
                'date'         => $request->input('dob') ? date('Y-m-d', strtotime($request->input('dob'))) : null,
                'status'       => Decision::STATUS_NEW,
            ]);

            // Create test program entry
            TestPrograms::create([
                'app_id'      => $application->id,
                'director_id' => 27, // Hardcoded, but same suggestion as above
            ]);
        }

        return redirect()->route('listapplication')->with('message', 'Successfully Submitted');
    }

    // application edit

    public function edit($id)
    {
        $title = "Arizani o'zgartirish";
        $app = Application::findOrFail($id); // Use findOrFail to handle missing records

        $type = Application::getType();
        $crop = session('crop', 1);
        $names = DB::table('crops_name')->where('id', ($crop != 2) ? '=' : '!=', 1)->get();
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

}

