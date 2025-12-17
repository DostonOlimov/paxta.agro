<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Region;
use Illuminate\Http\Request;
use App\Models\DefaultModels\tbl_cities;
use App\Models\DefaultModels\tbl_states as DefaultModelsTbl_states;
use App\Models\DefaultModels\User as DefaultModelsUser;
use Illuminate\Support\Facades\DB;

class StatesController extends Controller
{
    public function index()
    {
        $title = 'Viloyat qo\'shish';
        return view('states.add', compact('title'));
    }

    // vehiclebrand list
    public function list()
    {
        $title = 'Viloyatlar';
        $states = DB::table('tbl_states')->orderBy('name')->get()->toArray();
        return view('states.list', compact('states'));
    }

    // vehiclebrand store
    public function store(Request $request)
    {
        $city = $request->input('city');
        $region = $request->input('region');
        $count = DB::table('tbl_cities')->where('name', '=', $city)->count();
        if ($count == 0) {
            $cityname = new tbl_cities();
            $cityname->name = $city;
            $cityname->state_id = $region;
            $cityname->save();
            return redirect('cities/list')->with('message', 'Successfully Submitted');
        } else {
            return redirect('cities/add')->with('message', 'Duplicate Data');
        }
        echo $region;
    }

    public function destory($id)
    {
        $this->authorize('setting_delete', DefaultModelsUser::class);
        Area::destroy($id);
        return redirect('cities/list')->with('message', 'Successfully Deleted');
    }

    public function edit($id)
    {
        return view('states.edit', [
            'state' => Region::findOrFail($id),
            'editid' => $id,
        ]);
    }

    // vehiclebrand update
    public function update(Request $request, $id)
    {
        $state = DefaultModelsTbl_states::findOrFail($id);
        $state->name = $request->input('state');
        $state->save();

        return redirect('states/list')->with('message', 'Successfully Updated');
    }
}
