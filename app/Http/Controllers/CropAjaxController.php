<?php

namespace App\Http\Controllers;
use App\Models\CropsName;
use App\Models\OrganizationCompanies;
use App\Services\LocationService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class CropAjaxController extends Controller
{

    //get state
    public function gettype(Request $request)
    {
        $id = $request->input('name_id');

        $types = DB::table('crops_type')->where('crop_id','=',$id)->get()->toArray();
        if(!empty($types))
        {
            echo "<option value=\"\">Ekin navini tanlang</option>";
            foreach($types as $type)
            { ?>
                <option value="<?php echo  $type->id; ?>"  class="states_of_countrys"><?php echo $type->name; ?></option>
            <?php }
        }
    }
    //get state
    public function getgeneration(Request $request)
    {
        $id = $request->input('name_id');

        $types = DB::table('crops_generation')->where('crop_id','=',$id)->get()->toArray();
        if(!empty($types))
        {
            echo "<option value=\"\">Ekin avlodini tanlang</option>";
            foreach($types as $type)
            { ?>
                <option value="<?php echo  $type->id; ?>"  class="states_of_countrys"><?php echo $type->name; ?></option>
            <?php }
        }
    }
    //get state
    public function getkodtnved($id)
    {
        $types = CropsName::find($id);
        if($types)
        {
           return response()->json(['code' => $types->kodtnved]);
        }
    }

    //get company
    public function getcompany(Request $request)
    {
        $stir = $request->input('stir');
        $company = OrganizationCompanies::where('inn',$stir)
            ->first();
        if($company)
        {
            return response()->json([
                'name' => $company->name,
                'owner_name'=>$company->owner_name,
                'phone_number'=>$company->phone_number,
                'address' => $company->address,
                'city' => $company->city_id,
                'cityName' => optional($company->city)->name,
                'state' => optional($company->city)->state_id

            ]);
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

    public function processExcel(Request $request)
    {
        $file = $request->file('data');
        dd($request);
        $rows = \Maatwebsite\Excel\Facades\Excel::toArray([], $file)[0];
        // Extract numbers from the Excel file
        $numbers = [];
        foreach ($rows as $row) {
            $numbers[] = $row[0]; // Assuming numbers are in the first column
        }

        return response()->json($numbers);
    }

}
