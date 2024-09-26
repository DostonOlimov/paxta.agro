<?php

namespace App\Http\Controllers;

use App\Filters\V1\DalolatnomaFilter;
use App\Models\Dalolatnoma;
use App\Models\Indicator;
use App\Models\LaboratoryFinalResults;
use App\Models\LaboratoryResult;
use App\Services\SearchService;
use Illuminate\Http\Request;

class LaboratoryResultController extends Controller
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
                'laboratory_result.list',
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
        $apps= Dalolatnoma::with('test_program.application.decision.laboratory.city')->find($id);
        $indicators = Indicator::where('crop_id','=',2)
            ->get();

        return view('laboratory_result.add', compact('apps','indicators'));
    }

    public function store(Request $request)
    {

        $result = new LaboratoryResult();
        $result->dalolatnoma_id = $request->input('dalolatnoma_id');
        $result->mic = $request->input('value9');
        $result->staple = $request->input('value10');
        $result->strength = $request->input('value11');
        $result->uniform = $request->input('value12');
        $result->fiblength = $request->input('value13');
        $result->humidity = 5.1;
        $result->save();

        return redirect('/laboratory_results/search')->with('message', 'Successfully Submitted');
    }

    public function view($id)
    {
        $apps= Dalolatnoma::with('test_program.application.decision.laboratory.city')->find($id);
        $indicators = Indicator::where('crop_id','=',2)
            ->get();

        return view('laboratory_result.view', compact('apps','indicators'));
    }

    public function edit($id)
    {
        $apps= Dalolatnoma::with('test_program.application.decision.laboratory.city')->find($id);
        $indicators = Indicator::where('crop_id','=',2)
            ->get();

        return view('laboratory_result.edit', compact('apps','indicators'));
    }

    public function update(Request $request)
    {
        $id = $request->input('id');
        $result = LaboratoryResult::findOrFail($id);
        $result->mic = $request->input('value9');
        $result->staple = $request->input('value10');
        $result->strength = $request->input('value11');
        $result->uniform = $request->input('value12');
        $result->fiblength = $request->input('value13');
        $result->humidity = 5.1;
        $result->save();

        return redirect('/laboratory_results/search')->with('message', 'Successfully Submitted');
    }

}
