<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\CropData;

use App\Models\Decision;
use App\Models\FinalResult;
use App\Models\Indicator;
use App\Models\Nds;
use App\Models\TestProgramIndicators;
use App\Models\TestPrograms;
use App\tbl_activities;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinalDecisionController extends Controller
{
    //search
    public function search(Request $request)
    {
        $user = Auth::user();
        $city = $request->input('city');
        $crop = $request->input('crop');
        $from = $request->input('from');
        $till = $request->input('till');
        $apps= FinalResult::with('test_program')
            ->with('test_program.application')
            ->with('test_program.application.crops.name')
            ->with('test_program.application.crops.type')
            ->with('test_program.application.organization');
        if($user->role == \App\Models\User::STATE_EMPLOYEE){
            $user_city = $user->state_id;
            $apps = $apps->whereHas('test_program.application.organization', function ($query) use ($user_city) {
                $query->whereHas('city', function ($query) use ($user_city) {
                    $query->where('state_id', '=', $user_city);
                });
            });
        }
        if ($from && $till) {
            $fromTime = join('-', array_reverse(explode('-', $from)));
            $tillTime = join('-', array_reverse(explode('-', $till)));
            $apps->whereHas('test_program.application', function ($query) use ($fromTime,$tillTime) {
                $apps = $query->whereDate('date', '>=', $fromTime)
                    ->whereDate('date', '<=', $tillTime);
            });
        }
        if ($city) {
            $apps = $apps->whereHas('test_program.application.organization', function ($query) use ($city) {
                $query->whereHas('city', function ($query) use ($city) {
                    $query->where('state_id', '=', $city);
                });
            });
        }
        if ($crop) {
            $apps = $apps->whereHas('test_program.application.crops', function ($query) use ($crop) {
                $query->where('name_id', '=', $crop);
            });
        }
        $apps->when($request->input('s'), function ($query, $searchQuery) {
            $query->where(function ($query) use ($searchQuery) {
                if (is_numeric($searchQuery)) {
                    $query->whereHas('test_program.application', function ($query) use ($searchQuery) {
                        $query->where('app_number', $searchQuery);
                    });
                } else {
                    $query->whereHas('test_program.application.crops.name', function ($query) use ($searchQuery) {
                        $query->where('name', 'like', '%' . addslashes($searchQuery) . '%');
                    })->orWhereHas('test_program.application.crops.type', function ($query) use ($searchQuery) {
                        $query->where('name', 'like', '%' . addslashes($searchQuery) . '%');
                    })->orWhereHas('test_program.application.crops.generation', function ($query) use ($searchQuery) {
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

        return view('final_decision.search', compact('apps','from','till','city','crop'));
    }


    public function edit($id)
    {
        $editid = $id;
        $userA = Auth::user();
        $test = TestPrograms::find($editid);
        $app = Application::find($test->app_id);

        $measure_types = CropData::getMeasureType();
        $directors = User::where('role','=',55)->get();
        $indicators = Indicator::where('crop_id','=',$app->crops->name->id)->get();

        return view('final_decision.edit', compact('app', 'test','directors', 'indicators', 'measure_types'));
    }

    public function view($id)
    {
        $final_decision = FinalResult::with('test_program')
            ->with('test_program.application.crops')
            ->with('test_program.application.crops.name')
            ->with('test_program.application.crops.name.nds')
            ->with('test_program.application.crops.type')
            ->with('test_program.application.crops.generation')
            ->with('test_program.application')
            ->with('decision_maker')
            ->find($id);
//        $measure_type = CropData::getMeasureType(Application::find($final_decision->app_id)->crops->measure_type);
//        $nds_type = Nds::getType(Application::find($final_decision->app_id)->crops->name->nds->type_id);
        return view('final_decision.show', [
            'decision' => $final_decision,
//            'measure_type'=>$measure_type,
//            'nds_type'=>$nds_type,
        ]);
    }

}
