<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\tbl_cities;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;

class CitiesController extends Controller
{
    public function index()
    {
        $regions = DB::table('tbl_states')->where('country_id', '=', 234)->get()->toArray();
        return view('cities.add', compact('regions'));
    }

    public function list()
    {
        $cities = DB::table('tbl_countries')->
        select('tbl_cities.name as district', 'tbl_states.name as region', 'tbl_cities.id')->
        where('tbl_countries.id', '=', 234)->
        join('tbl_states', 'tbl_countries.id', '=', 'tbl_states.country_id')->
        join('tbl_cities', 'tbl_states.id', '=', 'tbl_cities.state_id')->
        get()->toArray();
        return view('cities.list', compact('cities'));
    }

    public function store(Request $request)
    {
        $city = $request->input('city');
        $region = $request->input('region');
        $count = DB::table('tbl_cities')->where('name', '=', $city)->count();
        if ($count == 0) {
            $cityname = new tbl_cities;
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
        $this->authorize('setting_delete', User::class);
        $factory = DB::table('tbl_cities')->where('id', '=', $id)->delete();
        return redirect('cities/list')->with('message', 'Successfully Deleted');
    }


    public function edit($id)
    {
        $editid = $id;
        $city = DB::table('tbl_cities')->where('id', '=', $id)->get()->first();
        $regions = DB::table('tbl_states')->where('country_id', '=', 234)->get()->toArray();
        return view('cities.edit', compact('city', 'editid', 'regions'));
    }


    public function update(Request $request, $id)
    {
        $city = $request->input('city');
        $region = $request->input('region');
        $cityname = tbl_cities::find($id);
        $cityname->name = $city;
        $cityname->state_id = $region;
        $cityname->soato = request('soato', 0);
        $cityname->save();
        return redirect('cities/list')->with('message', 'Successfully Updated');
    }
}
