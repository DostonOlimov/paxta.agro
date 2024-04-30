<?php

namespace App\Http\Controllers;


use App\Models\Application;
use App\Models\CropData;
use App\Models\OrganizationCompanies;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\DefaultModels\tbl_activities;
use App\Models\User;

class ApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function applicationlist(Request $request)
    {

        $user = Auth::User();
        $city = $request->input('city');
        $crop = $request->input('crop');
        $from = $request->input('from');
        $till = $request->input('till');

        $apps = Application::with('organization')
            ->with('crops')
            ->with('crops.name')
            ->with('crops.type');
        if($user->branch_id == User::BRANCH_STATE ){
            $user_city = $user->state_id;
            $apps = $apps->whereHas('organization', function ($query) use ($user_city) {
                $query->whereHas('city', function ($query) use ($user_city) {
                    $query->where('state_id', '=', $user_city);
                });
            });
        }
        if ($from && $till) {
            $fromTime = join('-', array_reverse(explode('-', $from)));
            $tillTime = join('-', array_reverse(explode('-', $till)));
            $apps = $apps->whereDate('date', '>=', $fromTime)
                ->whereDate('date', '<=', $tillTime);
        }
        if ($city) {
            $apps = $apps->whereHas('organization', function ($query) use ($city) {
                $query->whereHas('city', function ($query) use ($city) {
                    $query->where('state_id', '=', $city);
                });
            });
        }
        if ($crop) {
            $apps = $apps->whereHas('crops', function ($query) use ($crop) {
                    $query->where('name_id', '=', $crop);
            });
        }
        $apps->when($request->input('s'), function ($query, $searchQuery) {
            $query->where(function ($query) use ($searchQuery) {
                if (is_numeric($searchQuery)) {
                    $query->orWhere('app_number', $searchQuery);
                } else {
                    $query->whereHas('crops.name', function ($query) use ($searchQuery) {
                            $query->where('name', 'like', '%' . addslashes($searchQuery) . '%');
                    })->orWhereHas('crops.type', function ($query) use ($searchQuery) {
                        $query->where('name', 'like', '%' . addslashes($searchQuery) . '%');
                    })->orWhereHas('crops.generation', function ($query) use ($searchQuery) {
                        $query->where('name', 'like', '%' . addslashes($searchQuery) . '%');
                    });

                }
            });
        });

        $apps = $apps->latest('id')
            ->paginate(50)
            ->appends(['s' => $request->input('s')])
            ->appends(['till' => $request->input('till')])
            ->appends(['from' => $request->input('from')])
            ->appends(['city' => $request->input('city')])
            ->appends(['crop' => $request->input('crop')]);
        return view('application.list', compact('apps','from','till','city','crop'));
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
        $app->save();
        return redirect('application/list')->with('message', 'Successfully Submitted');
    }
    public function reject(Request $request, $id)
    {
        // $app = Application::find($id);

        // return view('application.reject', compact('app'));

        $app = Application::find($id);
        $app->status = Application::STATUS_REJECTED;
        $app->save();
        return redirect('application/list')->with('message', 'Successfully Submitted');
    }
    public function reject_store(Request $request)
    {
        $app_id = $request->input('app_id');
        // $reason = $request->input('reason');
        $app = Application::find($app_id);
        $this->authorize('accept', $app);
        $app->status = Application::STATUS_REJECTED;
        $app->save();
        // $changes = new AppStatusChanges();
        // $changes->app_id = $app_id;
        // $changes->status = Application::STATUS_REJECTED;
        // $changes->comment = $reason;
        // $changes->user_id = Auth::user()->id;
        // $changes->save();

        return redirect('application/list')->with('message', 'Successfully Submitted');
    }

}

