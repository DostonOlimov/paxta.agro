<?php

namespace App\Http\Controllers;

use App\Jobs\InsideQueueJob;
use App\Models\AktAmount;
use App\Models\Application;
use App\Models\ClampData;
use App\Models\Dalolatnoma;
use App\Models\FinalResult;
use App\Models\GinBalles;
use App\Models\Humidity;
use App\Models\InXaus;
use App\Models\LaboratoryResult;
use App\Models\MeasurementMistake;
use App\Rules\ExsistInXaus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DefaultModels\MyTableReader;
use App\Jobs\ProcessFile;
use Illuminate\Support\Facades\DB;


class MeasurementMistakeController extends Controller
{
    //search
    public function search(Request $request)
    {
        $user = Auth::user();
        $city = $request->input('city');
        $crop = $request->input('crop');
        $from = $request->input('from');
        $till = $request->input('till');

        $apps= Dalolatnoma::with('test_program')
            ->with('test_program.application')
            ->with('test_program.application.decision')
            ->with('test_program.application.crops.name')
            ->with('test_program.application.crops.type')
            ->with('test_program.application.organization');
        if ($user->role == \App\Models\User::STATE_EMPLOYEE) {
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

        $tests = $apps->latest('id')
            ->paginate(50)
            ->appends(['s' => $request->input('s')])
            ->appends(['till' => $request->input('till')])
            ->appends(['from' => $request->input('from')])
            ->appends(['city' => $request->input('city')])
            ->appends(['crop' => $request->input('crop')]);
        return view('measurement_mistake.search', compact('tests','from','till','city','crop'));
    }
    //index
    public function add($id)
    {
        $test = Dalolatnoma::with('gin_balles')->find($id);
        return view('measurement_mistake.add', compact('test'));
    }

    public function store(Request $request)
    {
        $userA = Auth::user();
        $this->authorize('create', Application::class);
        $request->validate([
            'number' => ['required'],
            'date' => ['required', 'date', new ExsistInXaus()],
        ]);
        $id = $request->input('dalolatnoma_id');
        $number = $request->input('number');
        $date =  join('-', array_reverse(explode('-', $request->input('date'))));
        $in_xaus = InXaus::whereDate('date','<=',$date)
            ->where('state_id',$userA->state_id)
            ->orderBy('date', 'desc')
            ->first();
        $in_xaus_value = $in_xaus->calculateMetrics();

        $dalolatnoma = Dalolatnoma::find($id);

        $count = ClampData::select(
            DB::raw('AVG(clamp_data.mic) as mic'),
            DB::raw('AVG(clamp_data.staple) as staple'),
            DB::raw('AVG(clamp_data.strength) as strength'),
            DB::raw('AVG(clamp_data.uniform) as uniform'),
            DB::raw('AVG(clamp_data.fiblength) as fiblength')
            )
            ->where('clamp_data.dalolatnoma_id',$id)
            ->get();
        $count = ClampData::select(
            DB::raw('AVG(clamp_data.mic) as mic'),
            DB::raw('AVG(clamp_data.staple) as staple'),
            DB::raw('AVG(clamp_data.strength) as strength'),
            DB::raw('AVG(clamp_data.uniform) as uniform'),
            DB::raw('AVG(clamp_data.fiblength) as fiblength')
        )
            ->where('clamp_data.dalolatnoma_id',$id)
            ->get();
        $result = new LaboratoryResult();
        $result->dalolatnoma_id = $id;
        $result->mic = $count->mic;
        $result->staple = $count->staple;
        $result->strength = $count->strength;
        $result->uniform = $count->uniform;
        $result->fiblength = $count->fiblength;
        $result->save();

        $mistake = new MeasurementMistake();
        $mistake->dalolatnoma_id = $id;
        $mistake->number = $number;
        $mistake->date = join('-', array_reverse(explode('-', $request->input('date'))));
        $mistake->mic = $count->mic;
        $mistake->staple = $count->staple;
        $mistake->strength = $count->strength;
        $mistake->uniform = $count->uniform;
        $mistake->fiblength = $count->fiblength;
        $mistake->save();
        dd($counts);die();
            foreach($counts as $count){
                $result = new FinalResult();
                $result->dalolatnoma_id = $id;
                $result->test_program_id = $id;
                $result->sort = $count->sort;
                $result->class = $count->class;
                $result->count = $count->count;
                $result->amount = $count->total_amount;
                $result->mic = $count->mic;
                $result->staple = $count->staple;
                $result->strength = $count->strength;
                $result->uniform = $count->uniform;
                $result->humidity = $count->humidity;
                $result->save();
            }

        $test = new Humidity();
        $test->dalolatnoma_id = $test_id;
        $test->number = $number;
        $test->date = join('-', array_reverse(explode('-', $request->input('date'))));
        $test->selection_code = $selection_code;
        $test->toy_count = $toy_count;
        $test->party = $party_number;
        $test->nav = $nav;
        $test->sinf = $sinf;
        $test->save();

        return redirect('/humidity/search');
    }
    public function edit($id)
    {
        $tests = AktAmount::where('dalolatnoma_id',$id)->get()->toArray();

        $data1 =  array_chunk($tests, ceil(count($tests)/4));

        return view('measurement_mistake.edit', [
            'results' => $data1,
        ]);
    }


    public function save_amount(Request $request)
    {

        $id = $request->input('id');
        $amount = $request->input('amount');
        $result = AktAmount::find($id);
        if($amount > 0 and $amount < 1000){
            $result->amount = $amount;
        }
        $result->save();


        return response()->json(['message' => 'Answer saved successfully']);
    }
    public function view($id)
    {
        $tests = ClampData::where('dalolatnoma_id',$id)->get();
        return view('measurement_mistake.show', [
            'results' => $tests,
            'id' => $id
        ]);
    }


}
