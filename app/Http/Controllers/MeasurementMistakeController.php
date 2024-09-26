<?php

namespace App\Http\Controllers;

use App\Filters\V1\DalolatnomaFilter;
use App\Models\Application;
use App\Models\ClampData;
use App\Models\Dalolatnoma;
use App\Models\FinalResult;
use App\Models\HumidityResult;
use App\Models\InXaus;
use App\Models\LaboratoryResult;
use App\Models\MeasurementMistake;
use App\Models\Tips;
use App\Rules\ExsistInXaus;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class MeasurementMistakeController extends Controller
{
    //search
    public function search(Request $request, DalolatnomaFilter $filter,SearchService $service)
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
                'measurement_mistake.search',
                [],
                false
            );

        } catch (\Throwable $e) {
            // Log the error for debugging
            \Log::error($e);
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        $id = $request->input('dalolatnoma_id');
        $dalolatnoma = Dalolatnoma::findOrFail($id);  // Using findOrFail for better error handling
        $humidity_result = HumidityResult::where('dalolatnoma_id',$id)->first();

        $state_id = optional($dalolatnoma->test_program->application->organization->city)->state_id;

        $request->validate([
            'number' => ['required'],
            'date' => ['required', 'date', new ExsistInXaus($state_id)],
        ]);

        $number = $request->input('number');
        $date = $this->formatDate($request->input('date'));  // Extract date formatting into a method for reusability
        $in_xaus = InXaus::whereDate('date', '<=', $date)
            ->where('state_id', $state_id)
            ->orderBy('date', 'desc')
            ->firstOrFail();  // Using firstOrFail to handle missing records

        $in_xaus_value = $in_xaus->calculateMetrics();
        $ball_values = $dalolatnoma->calculateDeviations();

        $clamp_data = ClampData::selectRaw('
        AVG(mic) as mic,
        AVG(staple) as staple,
        AVG(strength) as strength,
        AVG(uniform) as uniform,
        AVG(fiblength) as fiblength'
        )
            ->where('dalolatnoma_id', $id)
            ->first();

        $fiblength = $clamp_data->fiblength / 100;
        $tip = Tips::where('max', '>=', $fiblength)
            ->where('min', '<=', $fiblength)
            ->first();

        // Storing LaboratoryResult
        $this->storeLaboratoryResult($id, $clamp_data, $tip, $humidity_result);

        // Check if FinalResult exists, if not, create
        if (!FinalResult::where('dalolatnoma_id', $id)->exists()) {
            $this->storeFinalResults($id, $clamp_data, $humidity_result);
        }

        // Storing MeasurementMistake
        $mistake = $this->storeMeasurementMistake($id, $number, $date, $in_xaus_value, $ball_values, $humidity_result);

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
        $user = Auth::user();
        $this->authorize('create', Application::class);

        $mistake = MeasurementMistake::with([
            'dalolatnoma.laboratory_result',
            'dalolatnoma.humidity_result',
            'dalolatnoma.result',
        ])->findOrFail($id); // Use findOrFail to handle missing records

        $humidity_result = optional($mistake->dalolatnoma)->humidity_result;
        $state_id = optional($mistake->dalolatnoma->test_program->application->organization->city)->state_id;

        $request->validate([
            'number' => ['required'],
            'date' => ['required', 'date', new ExsistInXaus($state_id)],
        ]);

        $number = $request->input('number');
        $date = $this->formatDate($request->input('date'));

        $in_xaus = InXaus::whereDate('date', '<=', $date)
            ->where('state_id', $state_id)
            ->latest('date')
            ->first();

        $in_xaus_value = optional($in_xaus)->calculateMetrics();
        $ball_values = optional($mistake->dalolatnoma)->calculateDeviations();

        $clampData = $this->getClampData($mistake->dalolatnoma_id);

        $fiblength = $clampData->fiblength / 100;
        $tip = Tips::where('max', '>=', $fiblength)
            ->where('min', '<=', $fiblength)
            ->first();

        $this->updateLaboratoryResult($mistake->dalolatnoma->laboratory_result, $tip, $clampData, $humidity_result);

        if ($mistake->dalolatnoma->result->count() === 0) {
            $this->createFinalResults($mistake->dalolatnoma_id, $clampData, $humidity_result, $data->mic, $data->staple);
        }

        $this->updateMistake($mistake, $number, $date, $in_xaus_value, $ball_values, $humidity_result);

        return redirect('/measurement_mistake/search');
    }

    public function view($id)
    {
        $tests = MeasurementMistake::with('dalolatnoma.result')->findOrFail($id);
        $my_date = formatUzbekDate($tests->date);


        return view('measurement_mistake.show', [
            'result' => $tests,
            'date' => $my_date
        ]);
    }
    private function formatDate($date)
    {
        return join('-', array_reverse(explode('-', $date)));
    }

    /**
     * Store LaboratoryResult data.
     */
    private function storeLaboratoryResult($dalolatnoma_id, $clamp_data, $tip, $humidity_result)
    {
        $result = new LaboratoryResult();
        $result->dalolatnoma_id = $dalolatnoma_id;
        $result->tip_id = optional($tip)->id ?? 11;
        $result->mic = $clamp_data->mic;
        $result->staple = $clamp_data->staple;
        $result->strength = $clamp_data->strength;
        $result->uniform = $clamp_data->uniform;
        $result->fiblength = $clamp_data->fiblength;
        $result->humidity = optional($humidity_result)->humidity;
        $result->save();
    }

    /**
     * Store FinalResults if they don't exist.
     */
    private function storeFinalResults($dalolatnoma_id, $clamp_data, $humidity_result)
    {
        $counts = ClampData::selectRaw('
        sort, class,
        COUNT(*) as count,
        SUM(akt_amount.amount) as total_amount,
        AVG(mic) as mic,
        AVG(staple) as staple,
        AVG(strength) as strength,
        AVG(uniform) as uniform,
        AVG(humidity) as humidity'
        )
            ->join('akt_amount', function($join) use ($dalolatnoma_id) {
                $join->on('akt_amount.shtrix_kod', '=', 'clamp_data.gin_bale')
                    ->on('akt_amount.dalolatnoma_id', '=', 'clamp_data.dalolatnoma_id');
            })
            ->where('clamp_data.dalolatnoma_id', $dalolatnoma_id)
            ->groupBy('sort', 'class')
            ->get();

        foreach ($counts as $count) {
            $result = new FinalResult();
            $result->dalolatnoma_id = $dalolatnoma_id;
            $result->test_program_id = $dalolatnoma_id;
            $result->sort = $count->sort;
            $result->class = $count->class;
            $result->count = $count->count;
            $result->amount = $count->total_amount;
            $result->mic = $clamp_data->mic;
            $result->staple = $clamp_data->staple;
            $result->strength = $clamp_data->strength;
            $result->uniform = $clamp_data->uniform;
            $result->humidity = optional($humidity_result)->humidity;
            $result->save();
        }
    }

    /**
     * Store MeasurementMistake data.
     */
    private function storeMeasurementMistake($dalolatnoma_id, $number, $date, $in_xaus_value, $ball_values, $humidity_result)
    {
        $mistake = new MeasurementMistake();
        $mistake->dalolatnoma_id = $dalolatnoma_id;
        $mistake->number = $number;
        $mistake->date = $date;
        $mistake->mic = 2 * ($in_xaus_value[InXaus::TYPE_MIC] + $ball_values['mic']);
        $mistake->fiblength = 2 * ($in_xaus_value[InXaus::TYPE_LENGTH] + $ball_values['fiblength']);
        $mistake->uniform = 2 * ($in_xaus_value[InXaus::TYPE_INIFORMITY] + $ball_values['uniform']);
        $mistake->strength = 2 * ($in_xaus_value[InXaus::TYPE_STRENGTH] + $ball_values['strength']);
        $mistake->humidity = optional($humidity_result)->calculateMistake();
        $mistake->save();

        return $mistake;
    }

    private function getClampData($dalolatnoma_id)
    {
        return ClampData::select(
            DB::raw('AVG(clamp_data.mic) as mic'),
            DB::raw('AVG(clamp_data.staple) as staple'),
            DB::raw('AVG(clamp_data.strength) as strength'),
            DB::raw('AVG(clamp_data.uniform) as uniform'),
            DB::raw('AVG(clamp_data.fiblength) as fiblength')
        )
            ->where('clamp_data.dalolatnoma_id', $dalolatnoma_id)
            ->first();
    }

    private function updateLaboratoryResult($result, $tip, $clampData, $humidity_result)
    {
        $result->tip_id = optional($tip)->id ?? 11;
        $result->mic = $clampData->mic;
        $result->staple = $clampData->staple;
        $result->strength = $clampData->strength;
        $result->uniform = $clampData->uniform;
        $result->fiblength = $clampData->fiblength;
        $result->humidity = optional($humidity_result)->humidity;
        $result->save();
    }

    private function createFinalResults($dalolatnoma_id, $clampData, $humidity_result, $mic, $staple)
    {
        $counts = ClampData::select('sort', 'class',
            DB::raw('count(*) as count'),
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
            ->where('clamp_data.dalolatnoma_id', $dalolatnoma_id)
            ->where('akt_amount.dalolatnoma_id', $dalolatnoma_id)
            ->groupBy('sort', 'class')
            ->get();

        foreach ($counts as $count) {
            $result = new FinalResult();
            $result->dalolatnoma_id = $dalolatnoma_id;
            $result->test_program_id = $dalolatnoma_id; // Updated to avoid using $id incorrectly
            $result->sort = $count->sort;
            $result->class = $count->class;
            $result->count = $count->count;
            $result->amount = $count->total_amount;
            $result->mic = $mic;
            $result->staple = $staple;
            $result->strength = $clampData->strength;
            $result->uniform = $clampData->uniform;
            $result->humidity = optional($humidity_result)->humidity;
            $result->save();
        }
    }

    private function updateMistake($mistake, $number, $date, $in_xaus_value, $ball_values, $humidity_result)
    {
        $mistake->number = $number;
        $mistake->date = $date;
        $mistake->mic = 2 * ($in_xaus_value[InXaus::TYPE_MIC] + $ball_values['mic']);
        $mistake->fiblength = 2 * ($in_xaus_value[InXaus::TYPE_LENGTH] + $ball_values['fiblength']);
        $mistake->uniform = 2 * ($in_xaus_value[InXaus::TYPE_INIFORMITY] + $ball_values['uniform']);
        $mistake->strength = 2 * ($in_xaus_value[InXaus::TYPE_STRENGTH] + $ball_values['strength']);
        $mistake->humidity = optional($humidity_result)->calculateMistake();
        $mistake->save();
    }

}
