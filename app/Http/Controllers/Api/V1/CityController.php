<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\CityFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CityCollection;
use App\Http\Resources\V1\CityResource;
use App\Models\Area;
use App\Models\Region;
use App\Models\Role;
use Illuminate\Http\Request;


class CityController extends Controller
{
    public function index(Request $request, CityFilter $filter)
    {
        $query = Area::query();
        $filters = $request->only(array_keys($filter->safeParams));
        $query = $filter->apply($query, $filters);

        $statId = $request->input('stateId');

        if($statId){
            $query = $query->where('state_id',$statId);
        }

        $states = $query->get();

        return new CityCollection($states);
    }

    public function show($id)
    {
        $includeCities = request()->query('includeCities');

        if(!$includeCities){
             $application = Area::findOrFail($id);
        }else{
            $application = Area::findOrFail($id);
        }

        return new CityResource($application);
    }

}
