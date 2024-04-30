<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\CropData;
use App\Models\Decision;
use App\Models\Indicator;
use App\Models\Nds;
use App\Models\TestPrograms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TestProgramsController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth')->except('my_view');
    }

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
            ->with('tests')
            ->whereIn('status',[Application::STATUS_ACCEPTED,Application::STATUS_FINISHED]);
        if($user->branch_id == \App\Models\User::BRANCH_STATE ){
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
        return view('tests.search', compact('apps','from','till','city','crop'));
    }
    //index
    public function add($id)
    {
        $app = Application::find($id);

        if($nd = Nds::where('crop_id','=',$app->crops->name->id)->first()){
            $measure_types = CropData::getMeasureType();
            unset($measure_types[1]);
            $directors = User::where('role','=',55)->get();
            $indicators = Indicator::where('crop_id','=',$app->crops->name->id)->get();
            return view('tests.add', compact('app','nd','directors','measure_types','indicators'));
        }else{
            return redirect('nds/list')->with('message', 'nds not found');
        }
    }

    //  store
    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        $userA = Auth::user();
        $app_id = $request->input('app_id');
        $director_id = $request->input('director_id');
        $count = $request->input('count');
        $measure_type = $request->input('measure_type');
        $amount = $request->input('amount');
        $checkbox = $request->input('checkbox');
        $data = $request->input('data');

        $tests = new TestPrograms();
        $tests->app_id = $app_id;
        $tests->count = $count;
        $tests->measure_type = $measure_type;
        $tests->weight = $amount;
        $tests->extra_data = $data;
        $tests->director_id = $director_id;
        $tests->save();

        if(!empty($checkbox)) {
            foreach ($checkbox as $check) {
                $ch = new TestProgramIndicators();
                $ch->indicator_id = $check;
                $ch->test_program_id = $tests->id;
                $ch->save();
            }
        }
        $active = new tbl_activities;
        $active->ip_adress = $_SERVER['REMOTE_ADDR'];
        $active->user_id = $userA->id;
        $active->action_id = $tests->id;
        $active->action_type = 'new_tests';
        $active->action = "Yangi sinov dasturi qo'shildi";
        $active->time = date('Y-m-d H:i:s');
        $active->save();

        return redirect('/tests/search')->with('message', 'Successfully Submitted');


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

        return view('tests.edit', compact('app', 'test','directors', 'indicators', 'measure_types'));
    }


    // application update

    public function update($id, Request $request)
    {
        $this->authorize('update', User::class);
        $userA = Auth::user();
        $app_id = $request->input('app_id');
        $director_id = $request->input('director_id');
        $count = $request->input('count');
        $measure_type = $request->input('measure_type');
        $amount = $request->input('amount');
        $checkbox = $request->input('checkbox');
        $data = $request->input('data');

        $tests = TestPrograms::find($id);
        $tests->app_id = $app_id;
        $tests->count = $count;
        $tests->measure_type = $measure_type;
        $tests->weight = $amount;
        $tests->extra_data = $data;
        $tests->director_id = $director_id;
        $tests->save();
        TestProgramIndicators::where('test_program_id','=',$id)
            ->delete();
        foreach ($checkbox as $check)
        {
            $ch = new TestProgramIndicators();
            $ch->indicator_id = $check;
            $ch->test_program_id = $tests->id;
            $ch->save();
        }

        $active = new tbl_activities;
        $active->ip_adress = $_SERVER['REMOTE_ADDR'];
        $active->user_id = $userA->id;
        $active->action_id = $tests->id;
        $active->action_type = 'edit_test';
        $active->action = "Sinov dasturi yangilandi";
        $active->time = date('Y-m-d H:i:s');
        $active->save();
        return redirect('/tests/search')->with('message', 'Successfully Updated');

    }


    public function destory($id)
    {
        $this->authorize('delete', User::class);
        Decision::destroy($id);
        return redirect('test/search')->with('message', 'Successfully Deleted');
    }
    public function view($id)
    {
        $tests = TestPrograms::with('application.crops')
            ->with('application.decision.director')
            ->with('application.crops.name')
            ->with('application.crops.name.nds')
            ->with('application.crops.type')
            ->with('application.crops.generation')
            ->with('application')
            ->find($id);
        // Generate QR code if available
        $qrCode = null;
        if ($tests->application->decision->status == Decision::STATUS_ACCEPTED) {
            $url = route('tests.show', $id);
            $qrCode = QrCode::size(100)->generate($url);
        }
        $indicators = Indicator::where('crop_id','=',Application::find($tests->app_id)->crops->name->id)
            ->get();
        $nds_type = Nds::getType( Application::find($tests->app_id)->crops->name->nds->type_id).' '.Application::find($tests->app_id)->crops->name->nds->number;
        return view('tests.show', [
            'decision' => $tests,
            'nds_type'=>$nds_type,
            'indicators'=>$indicators,
            'qrCode' => $qrCode
        ]);
    }

    public function my_view($id)
    {
        $tests = TestPrograms::with('application.crops')
            ->with('application.decision.director')
            ->with('application.crops.name')
            ->with('application.crops.name.nds')
            ->with('application.crops.type')
            ->with('application.crops.generation')
            ->with('application')
            ->find($id);
        // Generate QR code if available
        $qrCode = null;
        if ($tests->application->decision->status == Decision::STATUS_ACCEPTED) {
            $url = route('tests.show', $id);
            $qrCode = QrCode::size(100)->generate($url);
        }
        $indicators = Indicator::where('crop_id','=',Application::find($tests->app_id)->crops->name->id)
            ->get();
        $nds_type = Nds::getType( Application::find($tests->app_id)->crops->name->nds->type_id).' '.Application::find($tests->app_id)->crops->name->nds->number;
        return view('tests.my_view', [
            'decision' => $tests,
            'nds_type'=>$nds_type,
            'indicators'=>$indicators,
            'qrCode' => $qrCode
        ]);
    }

}
