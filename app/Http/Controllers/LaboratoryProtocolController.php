<?php

namespace App\Http\Controllers;

use App\Filters\V1\DalolatnomaFilter;
use App\Http\Controllers\Traits\DalolatnomaTrait;
use App\Models\Application;
use App\Models\ClampData;
use App\Models\Dalolatnoma;
use App\Models\FinalResult;
use App\Models\LaboratoryFinalResults;
use App\Models\LaboratoryOperator;
use App\Models\MeasurementMistake;
use App\Models\User;
use App\Services\SearchService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LaboratoryProtocolController extends Controller
{
    //search
    public function list(Request $request, DalolatnomaFilter $filter,SearchService $service)
    {
        try {
            $names = getCropsNames();
            $states = getRegions();
            $years = getCropYears();

            return $service->search(
                $request,
                $filter,
                Dalolatnoma::class,
                [
                    'test_program',
                    'test_program.application',
                    'test_program.application.decision',
                    'test_program.application.organization',
                    'test_program.application.prepared',
                ],
                compact('names', 'states', 'years'),
                'laboratory_protocol.list',
                [],
                false
            );

        } catch (\Throwable $e) {
            // Log the error for debugging
            \Log::error($e);
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        if(!$klassiyor->klassiyor){
            return redirect('/laboratory-protocol/list')->with('message', $klassiyor->classer_id . ' kodli Klassiyor topilmadi');
        }

        $director=User::where('role','=',User::LABORATORY_DIRECTOR)
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
