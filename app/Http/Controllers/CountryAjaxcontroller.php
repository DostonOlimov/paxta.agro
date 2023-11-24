<?php

namespace App\Http\Controllers;
use App\Services\LocationService;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\tbl_states;
use App\tbl_cities;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CountryAjaxcontroller extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    //get state
    public function getstate(Request $request)
    {
        $id = $request->input('countryid');

        $states = DB::table('tbl_states')->where('country_id','=',$id)->get()->toArray();
        if(!empty($states))
        {
            foreach($states as $statess)
            { ?>

                <option value="<?php echo  $statess->id; ?>"  class="states_of_countrys"><?php echo $statess->name; ?></option>

            <?php }
        }
    }

    //get city
    public function getcity(Request $request)
    {
        $stateid = $request->input('stateid');

        $cities = LocationService::getAreas()->where('state_id', $stateid);

        foreach($cities as $city) {
            echo "<option value='{$city->id}'  class='cities'>{$city->name}</option>";
        }
    }

    public function getcitiesjson(Request $request){
        $stateid = $request->input('stateId');
        if($stateid){
            $cities = DB::table('tbl_cities')->where('state_id','=',$stateid)->get()->toArray();
            return json_encode($cities);
        }
    }

    public function edit_city(Request $request){
        $cityId=$request->input('cityId');
        $name=$request->input('name');
        DB::table('tbl_cities')->where('id','=',$cityId)->update(['name'=>$name]);
    }

    public function add_city(Request $request){
        $cityName=$request->input('city');
        $stateId=$request->input('stateId');

        $count = DB::table('tbl_cities')->where('name','=',$cityName)->where('state_id','=',$stateId)->count();

        if ($count==0){
            $city = new tbl_cities;
            $city->name = $cityName;
            $city->state_id=$stateId;
            $city->save();
            echo $city->id;
        }
        else{
            return "01";
        }
    }

    public function getcityfromsearch(Request $request){
        $search=$request->input('search');
        $cities=DB::table('tbl_cities')
            ->join('tbl_states','tbl_states.id','=','tbl_cities.state_id')
            ->where('tbl_cities.name','like','%'.$search.'%')
            ->where('tbl_states.country_id','=',234)
            ->select('tbl_cities.*')
            ->get()->toArray();
        if(!empty($cities)){
            echo json_encode($cities);
        }else{
            echo 'empty';
        }
    }

    public function update_state(Request $request){
        $stateid=$request->input('stateId');
        $state=tbl_states::find($stateid);
        $state->name=$request->input('name');
        $state->code=$request->input('code');
        $state->save();
        echo 'success';
    }
}
