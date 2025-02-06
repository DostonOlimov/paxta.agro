<?php

namespace App\Http\Controllers;

use App\Filters\V1\DalolatnomaFilter;
use App\Jobs\ProcessFile;
use App\Models\Application;
use App\Models\Decision;
use App\Models\Dalolatnoma;
use App\Models\GinBalles;
use App\Models\HumidityResult;
use App\Models\Region;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HumidityResultController extends Controller
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
                    'test_program.application.crops',
                    'test_program.application.crops.name',
                    'test_program.application.decision',
                    'test_program.application.organization',
                    'test_program.application.organization.area.region',
                    'test_program.application.prepared',
                    'humidity',
                    'humidity_result'
                ],
                compact('names', 'states', 'years'),
                'humidity_result.search',
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
        $test = Dalolatnoma::find($id);
        return view('humidity_result.add', compact('test'));
    }

    //  store
    public function store(Request $request)
    {
        $user = Auth::user();
        $this->authorize('create', Application::class);
        $gin_balles = GinBalles::where('dalolatnoma_id',$request->input('dalolatnoma_id'))->get();

        foreach ($gin_balles as $balles) {
            $state = Region::find($user->state_id);
            $gin_id = 1000 * $state->clamp_id + $balles->dalolatnoma->test_program->application->prepared->kod;
            ProcessFile::dispatch([
                'path' => $state->hvi_file->path,
                'balles' => $balles,
                'count' => 100,
                'gin_id' => $gin_id,
            ]);
        }

        $data = $request->only([
            'dalolatnoma_id',
            'number',
            'm0',
            'm1',
            'mk0',
            'mk1',
            'kalibrovka',
        ]);

        $data['date'] = join('-', array_reverse(explode('-', $request->input('date'))));

        $humidity_result = new HumidityResult();
        $humidity_result->fill($data);
        $humidity_result->save();

        return redirect('/humidity_result/search');
    }

    public function edit($id)
    {
        $userA = Auth::user();
        $result = HumidityResult::find($id);

        return view('humidity_result.edit', compact('result'));
    }

    // application update
    public function update($id, Request $request)
    {
        $user = Auth::user();
        $result = HumidityResult::findOrFail($id);

        $result->fill($request->only([
            'number',
            'date',
            'm0',
            'mk0',
            'm1',
            'mk1',
            'kalibrovka',
        ]));

        if ($result->isDirty('date')) {
            $result->date = join('-', array_reverse(explode('-', $result->date)));
        }

        $result->save();

        return redirect('/humidity_result/search')->with('message', 'Successfully Updated');
    }


    public function destory($id)
    {
        Decision::destroy($id);
        return redirect('humidity_result/search')->with('message', 'Successfully Deleted');
    }

    public function view($id)
    {
        $tests = HumidityResult::findOrFail($id);
        $my_date = formatUzbekDate($tests->date);

        return view('humidity_result.show', [
            'result' => $tests,
            'date' => $my_date
        ]);
    }
}
