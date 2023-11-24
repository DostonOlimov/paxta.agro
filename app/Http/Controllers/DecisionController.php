<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\CropData;
use App\Models\Decision;
use App\Models\Laboratories;
use App\Models\Nds;
use App\Models\DefaultModels\tbl_activities;
use App\Models\TestProgramIndicators;
use App\Models\TestPrograms;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DecisionController extends Controller
{
    //search
    public function search(Request $request)
    {
        $user = Auth::user();
        $city = $request->input('city');
        $crop = $request->input('crop');
        $from = $request->input('from');
        $till = $request->input('till');

        $apps = Application::with('crops')
            ->with('crops.name')
            ->with('crops.type')
            ->with('organization')
            ->with('decision')
            ->whereIn('status',[Application::STATUS_ACCEPTED,Application::STATUS_FINISHED]);

        if($user->role == \App\Models\User::STATE_EMPLOYEE){
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
        return view('decision.search', compact('apps','from','till','city','crop'));
    }
    //index
    public function add($id)
    {
        $app = Application::find($id);

        if($nd = Nds::where('crop_id','=',$app->crops->name->id)->first()){
            $laboratories = Laboratories::get();
            $directors = User::where('role','=',55)->get();
            return view('decision.add', compact('app','nd','directors','laboratories'));
        }else{
            return redirect('nds/list')->with('message', 'nds not found');
        }
    }

    //  store
    public function store(Request $request)
    {
        $userA = Auth::user();
        $this->authorize('create', User::class);
        $app_id = $request->input('app_id');
        $number = $request->input('number');
        $date = join('-', array_reverse(explode('-', $request->input('dob'))));
        $laboratory_id = $request->input('laboratory_id');

            $decision = new Decision();
            $decision->app_id = $app_id;
            $decision->director_id = $userA->id;
            $decision->number = $number;
            $decision->laboratory_id = $laboratory_id;
            $decision->created_by = $userA->id;
            $decision->date = $date;
            $decision->status = Decision::STATUS_NEW;
            $decision->save();

            $active = new tbl_activities;
            $active->ip_adress = $_SERVER['REMOTE_ADDR'];
            $active->user_id = $userA->id;
            $active->action_id = $decision->id;
            $active->action_type = 'new_decision';
            $active->action = "Yangi buyruq qo'shildi";
            $active->time = date('Y-m-d H:i:s');
            $active->save();

            $tests = new TestPrograms();
            $tests->app_id = $app_id;
            $tests->director_id = $userA->id;
            $tests->save();

        return redirect('/decision/search')->with('message', 'Successfully Submitted');
    }

    public function destory($id)
    {
        $this->authorize('delete', User::class);
        Decision::destroy($id);
        return redirect('decision/search')->with('message', 'Successfully Deleted');
    }
    public function view($id)
    {
        $decision = Decision::with('director')
            ->with('application.organization')
            ->with('application.crops')
            ->with('application.crops.name')
            ->with('application.crops.name.nds')
            ->with('application.crops.type')
            ->with('application.crops.generation')
            ->with('application')
            ->with('laboratory')
            ->find($id);
        $nds_type = Nds::getType(Application::find($decision->app_id)->crops->name->nds->type_id);
        return view('decision.show', [
            'decision' => $decision,
            'nds_type'=>$nds_type
        ]);
    }

}
