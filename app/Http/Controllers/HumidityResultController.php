<?php

namespace App\Http\Controllers;

use App\Filters\V1\DalolatnomaFilter;
use App\Http\Controllers\Traits\DalolatnomaTrait;
use App\Models\Application;
use App\Models\Decision;
use App\Models\Dalolatnoma;
use App\Models\HumidityResult;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                    'test_program.application.decision',
                    'test_program.application.organization',
                    'test_program.application.prepared',
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
