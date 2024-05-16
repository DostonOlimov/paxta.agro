<?php

namespace App\Http\Controllers;

use App\Models\AktAmount;
use App\Models\Application;
use App\Models\ClampData;
use App\Models\Dalolatnoma;
use App\Models\FinalResult;
use App\Models\HumidityResult;
use App\Models\InXaus;
use App\Models\LaboratoryResult;
use App\Models\MeasurementMistake;
use App\Rules\ExsistInXaus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
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
            $apps->whereDate('date', '>=', $fromTime)
                ->whereDate('date', '<=', $tillTime);
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
        $test = Dalolatnoma::with('gin_balles')
            ->with('clamp_data')
            ->find($id);
//        var_dump(!empty($test->clamp_data));die();
        return view('measurement_mistake.add', compact('test'));
    }

    public function store(Request $request)
    {
        $userA = Auth::user();
        $this->authorize('create', Application::class);
        $id = $request->input('dalolatnoma_id');
        $dalolatnoma = Dalolatnoma::find($id);
        $humidity_result = HumidityResult::where('dalolatnoma_id',$id)->first();
        $state_id = $dalolatnoma->test_program->application->organization->city->state_id;

        $request->validate([
            'number' => ['required'],
            'date' => ['required', 'date', new ExsistInXaus($state_id)],
        ]);

        $number = $request->input('number');
        $date =  join('-', array_reverse(explode('-', $request->input('date'))));
        $in_xaus = InXaus::whereDate('date','<=',$date)
            ->where('state_id',$state_id)
            ->orderBy('date', 'desc')
            ->first();
        $in_xaus_value = $in_xaus->calculateMetrics();
        $ball_values = $dalolatnoma->calculateDeviations();

        $data = ClampData::select(
            DB::raw('AVG(clamp_data.mic) as mic'),
            DB::raw('AVG(clamp_data.staple) as staple'),
            DB::raw('AVG(clamp_data.strength) as strength'),
            DB::raw('AVG(clamp_data.uniform) as uniform'),
            DB::raw('AVG(clamp_data.fiblength) as fiblength')
            )
            ->where('clamp_data.dalolatnoma_id',$id)
            ->first();

        $result = new LaboratoryResult();
        $result->dalolatnoma_id = $id;
        $result->mic = $data->mic;
        $result->staple = $data->staple;
        $result->strength = $data->strength;
        $result->uniform = $data->uniform;
        $result->fiblength = $data->fiblength;
        $result->humidity = optional($humidity_result)->humidity;
        $result->save();

        $data_count = FinalResult::where('dalolatnoma_id',$id)->count();

        if($data_count == 0){
            $counts = ClampData::select('sort', 'class',
                \DB::raw('count(*) as count'),
                DB::raw('SUM(akt_amount.amount) as total_amount'),
                DB::raw('AVG(clamp_data.mic) as mic'),
                DB::raw('AVG(clamp_data.staple) as staple'),
                DB::raw('AVG(clamp_data.strength) as strength'),
                DB::raw('AVG(clamp_data.uniform) as uniform'),
                DB::raw('AVG(clamp_data.humidity) as humidity')
            )
                ->join('akt_amount', function($join) {
                    $join->on('akt_amount.shtrix_kod', '=', 'clamp_data.gin_bale')
                        ->on('akt_amount.dalolatnoma_id', '=', 'clamp_data.dalolatnoma_id');
                })
                ->where('clamp_data.dalolatnoma_id',$id)
                ->where('akt_amount.dalolatnoma_id', $id)
                ->groupBy('sort', 'class')
                ->get();
            foreach($counts as $count){
                $result = new FinalResult();
                $result->dalolatnoma_id = $id;
                $result->test_program_id = $id;
                $result->sort = $count->sort;
                $result->class = $count->class;
                $result->count = $count->count;
                $result->amount = $count->total_amount;
                $result->mic = $data->mic;
                $result->staple = $data->staple;
                $result->strength = $data->strength;
                $result->uniform = $data->uniform;
                $result->humidity = optional($humidity_result)->humidity;
                $result->save();
            }
        }
        $mistake = new MeasurementMistake();
        $mistake->dalolatnoma_id = $id;
        $mistake->number = $number;
        $mistake->date = join('-', array_reverse(explode('-', $request->input('date'))));
        $mistake->mic = 2 * ($in_xaus_value[InXaus::TYPE_MIC] + $ball_values['mic']);
        $mistake->fiblength = 2 * ($in_xaus_value[InXaus::TYPE_LENGTH] + $ball_values['fiblength']);
        $mistake->uniform = 2 * ($in_xaus_value[InXaus::TYPE_INIFORMITY] + $ball_values['uniform']);
        $mistake->strength = 2 * ($in_xaus_value[InXaus::TYPE_STRENGTH] + $ball_values['strength']);
        $mistake->humidity = $humidity_result->calculateMistake();
        $mistake->save();

        return redirect('/measurement_mistake/search');
    }
    public function edit($id)
    {
        $result = MeasurementMistake::find($id);

        return view('measurement_mistake.edit', [
            'result' => $result,
        ]);
    }


    public function update($id, Request $request)
    {

        $userA = Auth::user();
        $this->authorize('create', Application::class);
        $mistake = MeasurementMistake::with('dalolatnoma.laboratory_result')
            ->with('dalolatnoma.humidity_result')
            ->with('dalolatnoma.result')
            ->find($id);
        $humidity_result = optional($mistake)->dalolatnoma->humidity_result;
        $state_id = optional($mistake)->dalolatnoma->test_program->application->organization->city->state_id;

        $request->validate([
            'number' => ['required'],
            'date' => ['required', 'date', new ExsistInXaus($state_id)],
        ]);

        $number = $request->input('number');
        $date =  join('-', array_reverse(explode('-', $request->input('date'))));
        $in_xaus = InXaus::whereDate('date','<=',$date)
            ->where('state_id',$state_id)
            ->orderBy('date', 'desc')
            ->first();
        $in_xaus_value = $in_xaus->calculateMetrics();
        $ball_values = $mistake->dalolatnoma->calculateDeviations();

        $data = ClampData::select(
            DB::raw('AVG(clamp_data.mic) as mic'),
            DB::raw('AVG(clamp_data.staple) as staple'),
            DB::raw('AVG(clamp_data.strength) as strength'),
            DB::raw('AVG(clamp_data.uniform) as uniform'),
            DB::raw('AVG(clamp_data.fiblength) as fiblength')
        )
            ->where('clamp_data.dalolatnoma_id',$mistake->dalolatnoma_id)
            ->first();

        $result = $mistake->dalolatnoma->laboratory_result;
        $result->mic = $data->mic;
        $result->staple = $data->staple;
        $result->strength = $data->strength;
        $result->uniform = $data->uniform;
        $result->fiblength = $data->fiblength;
        $result->humidity = optional($humidity_result)->humidity;
        $result->save();

        $data_count = $mistake->dalolatnoma->result->count();

        if($data_count == 0){
            $counts = ClampData::select('sort', 'class',
                \DB::raw('count(*) as count'),
                DB::raw('SUM(akt_amount.amount) as total_amount'),
                DB::raw('AVG(clamp_data.mic) as mic'),
                DB::raw('AVG(clamp_data.staple) as staple'),
                DB::raw('AVG(clamp_data.strength) as strength'),
                DB::raw('AVG(clamp_data.uniform) as uniform'),
                DB::raw('AVG(clamp_data.humidity) as humidity')
            )
                ->join('akt_amount', function($join) {
                    $join->on('akt_amount.shtrix_kod', '=', 'clamp_data.gin_bale')
                        ->on('akt_amount.dalolatnoma_id', '=', 'clamp_data.dalolatnoma_id');
                })
                ->where('clamp_data.dalolatnoma_id',$mistake->dalolatnoma_id)
                ->where('akt_amount.dalolatnoma_id',$mistake->dalolatnoma_id)
                ->groupBy('sort', 'class')
                ->get();
            foreach($counts as $count){
                $result = new FinalResult();
                $result->dalolatnoma_id = $id;
                $result->test_program_id = $id;
                $result->sort = $count->sort;
                $result->class = $count->class;
                $result->count = $count->count;
                $result->amount = $count->total_amount;
                $result->mic = $data->mic;
                $result->staple = $data->staple;
                $result->strength = $data->strength;
                $result->uniform = $data->uniform;
                $result->humidity = optional($humidity_result)->humidity;
                $result->save();
            }
        }

        $mistake->number = $number;
        $mistake->date = join('-', array_reverse(explode('-', $request->input('date'))));
        $mistake->mic = 2 * ($in_xaus_value[InXaus::TYPE_MIC] + $ball_values['mic']);
        $mistake->fiblength = 2 * ($in_xaus_value[InXaus::TYPE_LENGTH] + $ball_values['fiblength']);
        $mistake->uniform = 2 * ($in_xaus_value[InXaus::TYPE_INIFORMITY] + $ball_values['uniform']);
        $mistake->strength = 2 * ($in_xaus_value[InXaus::TYPE_STRENGTH] + $ball_values['strength']);
        $mistake->humidity = $humidity_result->calculateMistake();
        $mistake->save();

        return redirect('/measurement_mistake/search');
    }
    public function view($id)
    {
        $test = MeasurementMistake::with('dalolatnoma.result')->find($id);

        $date = Carbon::parse($test->date);

        $uzbekMonthNames = [
            '01' => 'yanvar',
            '02' => 'fevral',
            '03' => 'mart',
            '04' => 'aprel',
            '05' => 'may',
            '06' => 'iyun',
            '07' => 'iyul',
            '08' => 'avgust',
            '09' => 'sentabr',
            '10' => 'oktabr',
            '11' => 'noyabr',
            '12' => 'dekabr'
        ];

        $my_date = $date->isoFormat("D") . ' ' . $uzbekMonthNames[$date->isoFormat("MM")] . ' '. $date->isoFormat("Y") ;

        return view('measurement_mistake.show', [
            'result' => $test,
            'date' => $my_date
        ]);
    }


}
