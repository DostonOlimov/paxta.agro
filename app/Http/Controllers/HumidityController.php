<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\DalolatnomaTrait;
use App\Models\Application;
use App\Models\CropsSelection;
use App\Models\Decision;
use App\Models\Dalolatnoma;
use App\Models\Nds;
use App\Models\Humidity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HumidityController extends Controller
{
    use DalolatnomaTrait;

    // Search
    public function search(Request $request)
    {
        $city = $request->input('city');
        $crop = $request->input('crop');
        $from = $request->input('from');
        $till = $request->input('till');
        $sort_by = $request->get('sort_by', 'id');
        $sort_order = $request->get('sort_order', 'desc');

        $apps = $this->buildQuery($request);

        $tests = $apps->paginate(50)
            ->appends($request->except('page'));

        return view('humidity.search', compact('tests','from','till','city','crop', 'sort_by', 'sort_order'));
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
