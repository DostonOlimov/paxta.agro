<?php

namespace App\Http\Controllers;

use App\Filters\V1\DalolatnomaFilter;
use App\Models\Application;
use App\Models\CropsSelection;
use App\Models\Decision;
use App\Models\Dalolatnoma;
use App\Models\Humidity;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HumidityController extends Controller
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
                'humidity.search',
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
        $selection = CropsSelection::get();

        return view('humidity.add', compact('test', 'selection'));
    }

    //  store
    public function store(Request $request)
    {
        $user = Auth::user();
        $this->authorize('create', Application::class);

        $data = $request->only([
            'dalolatnoma_id',
            'number',
            'selection_code',
            'party',
            'toy_count',
            'toy_amount',
            'party_number',
            'nav',
            'sinf',
        ]);

        $data['date'] = join('-', array_reverse(explode('-', $request->input('date'))));

        $humidity = new Humidity();
        $humidity->fill($data);
        $humidity->save();

        return redirect('/humidity/search');
    }

    public function edit($id)
    {
        $userA = Auth::user();
        $result = Humidity::find($id);
        $selection = CropsSelection::get();

        return view('humidity.edit', compact('result','selection'));
    }

    // update
    public function update($id, Request $request)
    {
        $user = Auth::user();
        $result = Humidity::findOrFail($id);

        $result->fill($request->only([
            'number',
            'date',
            'selection_code',
            'toy_count',
            'toy_amount',
            'amount',
            'party',
            'nav',
            'sinf',
        ]));

        if ($result->isDirty('date')) {
            $result->date = join('-', array_reverse(explode('-', $result->date)));
        }

        $result->save();

        return redirect('/humidity/search')->with('message', 'Successfully Updated');
    }
    // destroy
    public function destory($id)
    {
        Decision::destroy($id);
        return redirect('humidity/search')->with('message', 'Successfully Deleted');
    }
    //view
    public function view($id)
    {
        $tests = Humidity::findOrFail($id);
        $my_date = formatUzbekDate($tests->date);

        return view('humidity.show', [
            'result' => $tests,
            'date' => $my_date
        ]);
    }
}
