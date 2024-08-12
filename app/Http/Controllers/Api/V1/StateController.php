<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\StateFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\StateCollection;
use App\Http\Resources\V1\StateResource;
use App\Models\Region;
use App\Models\Role;
use Illuminate\Http\Request;


class StateController extends Controller
{
    public function index(Request $request, StateFilter $filter)
    {
        $query = Region::query();
        $filters = $request->only(array_keys($filter->safeParams));
        $query = $filter->apply($query, $filters);

        $includeCities = $request->input('includeCities');

        if($includeCities){
            $query = $query->with('areas');
        }

        $states = $query->get();

        return new StateCollection($states);
    }

    public function show($id)
    {
        $includeCities = request()->query('includeCities');

        if(!$includeCities){
             $application = Region::findOrFail($id);
        }else{
            $application = Region::with('areas')->findOrFail($id);
        }

        return new StateResource($application);
    }

}
