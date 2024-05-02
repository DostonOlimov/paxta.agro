<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ClampData;
use App\Models\Dalolatnoma;
use App\Models\FinalResult;
use App\Models\LaboratoryFinalResults;
use App\Models\LaboratoryOperator;
use App\Models\MeasurementMistake;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LaboratoryProtocolController extends Controller
{

    public function list(Request $request)
    {
        $user = Auth::user();
        $city = $request->input('city');
        $crop = $request->input('crop');
        $from = $request->input('from');
        $till = $request->input('till');

        $apps= Dalolatnoma::with('test_program')
            ->with('measurement_mistake')
            ->with('laboratory_result')
            ->with('result')
            ->with('laboratory_final_results')
            ->with('test_program.application.decision')
            ->with('test_program.application.crops.name')
            ->with('test_program.application.organization')
            ->whereHas('laboratory_result')
            ->whereHas('result')
            ->whereHas('measurement_mistake');
        if ($user->branch_id == \App\Models\User::BRANCH_STATE ) {
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
                    });

                }
            });
        });

        $tests = $apps->latest('id')
            ->paginate(50)
            ->appends(['s' => $request->input('s')])
            ->appends(['till' => $request->input('till')])
            ->appends(['from' => $request->input('from')])
            ->appends(['city' => $request->input('city')])
            ->appends(['crop' => $request->input('crop')]);
        return view('laboratory_protocol.list', compact('tests','from','till','city','crop'));
    }
    public function add($id)
    {
        $user=Auth::user();
        $apps= Dalolatnoma::with('test_program.application.decision.laboratory.city')->find($id);
        $operators=LaboratoryOperator::query();
        if($user->branch_id==2){
            $operators=$operators->where('laboratory_id', $apps->test_program->application->decision->laboratory_id);
        }
        $operators=$operators->get();
        $klassiyor=ClampData::with('klassiyor')->where('dalolatnoma_id',$id)->first();

        $director=User::where('role',User::LABORATORY_DIRECTOR)
            ->where('state_id', $apps->test_program->application->decision->laboratory->city->state_id)
            ->first();

        return view('laboratory_protocol.add', compact('apps','director','klassiyor','operators'));
    }
    public function store(Request $request)
    {
        $userA = Auth::user();
        $this->authorize('create', Application::class);
        $data=$request->all();

        $parsedDate = Carbon::createFromFormat('d-m-Y', $request->input('date'));
        $reformattedDate = $parsedDate->format('Y-m-d');
        $data['date']=$reformattedDate;

        LaboratoryFinalResults::create($data);

        return redirect('/laboratory-protocol/list')->with('message', 'Successfully Submitted');
    }
    public function view($id)
    {
        $test = Dalolatnoma::with('measurement_mistake')
            ->with('laboratory_result')
            ->with('result')
            ->with('selection')
            ->with('laboratory_final_results.operator')
            ->with('laboratory_final_results.klassiyor')
            ->with('laboratory_final_results.director')
            ->with('test_program.application.decision.laboratory.city.region')
            ->with('test_program.application.crops.name')
            ->with('test_program.application.organization.city')
            ->with('test_program.application.prepared.region')
            ->find($id);

        $final_result=FinalResult::with('dalolatnoma.laboratory_result')->where('dalolatnoma_id', $id)->get();
        $measurement_mistake=MeasurementMistake::where('dalolatnoma_id', $id)->first();

        $qrCode = null;
        if ($test->laboratory_final_results->status == 1) {
            $url = route('lab.view', $id);
            $qrCode = QrCode::size(100)->generate($url);
        }

        return view('laboratory_protocol.view', compact('test', 'qrCode','final_result','measurement_mistake'));
    }

    function change_status($id)
    {
        $test = LaboratoryFinalResults::where('dalolatnoma_id',$id)->first();
        $test->status = 1;
        $test->save();

        return redirect('/laboratory-protocol/list');
    }
}
