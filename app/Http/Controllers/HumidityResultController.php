<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\DalolatnomaTrait;
use App\Models\Application;
use App\Models\Decision;
use App\Models\Dalolatnoma;
use App\Models\HumidityResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HumidityResultController extends Controller
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

        return view('humidity_result.search', compact('tests','from','till','city','crop', 'sort_by', 'sort_order'));
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
