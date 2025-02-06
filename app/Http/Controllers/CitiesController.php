<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CitiesController extends Controller
{
    // Show the add city form with a list of regions
    public function index()
    {
        $regions = DB::table('tbl_states')->where('country_id', 234)->get()->toArray();
        return view('cities.add', compact('regions'));
    }

    // List all cities along with their respective regions
    public function list()
    {
        $cities = DB::table('tbl_countries')
            ->select('tbl_cities.name as district', 'tbl_states.name as region', 'tbl_cities.id')
            ->where('tbl_countries.id', 234)
            ->join('tbl_states', 'tbl_countries.id', '=', 'tbl_states.country_id')
            ->join('tbl_cities', 'tbl_states.id', '=', 'tbl_cities.state_id')
            ->get()
            ->toArray();
        return view('cities.list', compact('cities'));
    }

    // Store a new city
    public function store(Request $request)
    {
        $city = $request->input('city');
        $region = $request->input('region');

        // Check for duplicate city name
        $count = DB::table('tbl_cities')->where('name', $city)->count();
        if ($count == 0) {
            $cityname = new tbl_cities;
            $cityname->name = $city;
            $cityname->state_id = $region;
            $cityname->save();
            return redirect('cities/list')->with('message', 'Successfully Submitted');
        } else {
            return redirect('cities/add')->with('message', 'Duplicate Data');
        }
    }

    // Delete a city
    public function destroy($id)
    {
        $this->authorize('setting_delete', User::class);
        DB::table('tbl_cities')->where('id', $id)->delete();
        return redirect('cities/list')->with('message', 'Successfully Deleted');
    }

    // Show the edit form for a specific city
    public function edit($id)
    {
        $city = DB::table('tbl_cities')->where('id', $id)->first();
        $regions = DB::table('tbl_states')->where('country_id', 234)->get()->toArray();
        return view('cities.edit', compact('city', 'regions', 'id'));
    }

    // Update the details of a specific city
    public function update(Request $request, $id)
    {
        $city = $request->input('city');
        $region = $request->input('region');

        $cityname = tbl_cities::find($id);
        $cityname->name = $city;
        $cityname->state_id = $region;
        $cityname->soato = $request->input('soato', 0);
        $cityname->save();

        return redirect('cities/list')->with('message', 'Successfully Updated');
    }
}
