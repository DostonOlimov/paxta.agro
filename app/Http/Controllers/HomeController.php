<?php

namespace App\Http\Controllers;

use App\Models\AktAmount;
use App\Models\Application;
use App\Models\CropsName;
use App\Models\Region;
use App\Models\Sertificate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard(Request $request)
    {
        $app_type_selector = $request->input('app_type_selector');
        $city = $request->input('city');
        $crop = $request->input('crop');
        $from = $request->input('from');
        $till = $request->input('till');

        $applications_count = Application::selectRaw('
                    COUNT(*) as all_app_count,
                    SUM(CASE WHEN type = ? THEN 1 ELSE 0 END) as local_app,
                    SUM(CASE WHEN type = ? THEN 1 ELSE 0 END) as global_app,
                    SUM(CASE WHEN type = ? THEN 1 ELSE 0 END) as old_app,
                    CASE WHEN COUNT(*) > 0 THEN SUM(CASE WHEN type = ? THEN 1 ELSE 0 END) / COUNT(*) ELSE NULL END as local_percentage,
                    CASE WHEN COUNT(*) > 0 THEN SUM(CASE WHEN type = ? THEN 1 ELSE 0 END) / COUNT(*) ELSE NULL END as glogal_percentage',
            [Application::TYPE_1, Application::TYPE_2, Application::TYPE_3,Application::TYPE_1,Application::TYPE_2])
            ->first();

//        sum of products
        $sum_amount = AktAmount::with('dalolatnoma');
        if($city){
            $sum_amount = $sum_amount->whereHas('dalolatnoma.test_program.application.organization', function ($query) use ($city) {
                     $query->whereHas('city', function ($query) use ($city) {
                         $query->where('state_id', '=', $city);
                     });
                 });
        }
        if ($from && $till) {
            $fromTime = join('-', array_reverse(explode('-', $from)));
            $tillTime = join('-', array_reverse(explode('-', $till)));
            $sum_amount = $sum_amount->whereHas('dalolatnoma.test_program.application', function ($query) use ($fromTime,$tillTime) {
                    $query->whereDate('applications.date', '>=', $fromTime)
                        ->whereDate('applications.date', '<=', $tillTime);
            });
        }
//        count of application by crop_name
        $crops = CropsName::withCount(['applications']);
        $crops = $crops->withCount(['applications' => function ($query) use ($city, $from, $till, $app_type_selector) {
            if ($city) {
                $query->join('organization_companies', 'applications.organization_id', '=', 'organization_companies.id')
                    ->join("tbl_cities","tbl_cities.id", "=", "organization_companies.city_id")
                    ->where('tbl_cities.state_id', $city);
            }
            if ($from && $till) {
                $fromTime = join('-', array_reverse(explode('-', $from)));
                $tillTime = join('-', array_reverse(explode('-', $till)));
                $query->whereDate('applications.date', '>=', $fromTime)
                    ->whereDate('applications.date', '<=', $tillTime);
            }
//            if (!is_null($app_type_selector)) {
//                if($app_type_selector == 3){
//                    $query->join("test_programs","test_programs.app_id","=","applications.id")
//                        ->join("final_results","final_results.test_program_id","=","test_programs.id")
//                        ->whereNull('final_results.type');
//                }else{
//                    $query->join("test_programs","test_programs.app_id","=","applications.id")
//                        ->join("final_results","final_results.test_program_id","=","test_programs.id")
//                        ->where('final_results.type','=',$app_type_selector);
//                }
//            }
        }]);


        $app_states = Region::select('tbl_states.id', 'tbl_states.name', DB::raw('COUNT(applications.id) as application_count'))
            ->leftJoin('tbl_cities', 'tbl_states.id', '=', 'tbl_cities.state_id')
            ->leftJoin('organization_companies', 'tbl_cities.id', '=', 'organization_companies.city_id')
            ->join('applications', 'organization_companies.id', '=', 'applications.organization_id');
        if ($crop) {
            $app_states = $app_states->leftJoin('crop_data', 'applications.crop_data_id', '=', 'crop_data.id')
                ->where('crop_data.name_id', '=', $crop);
        }
        if ($from && $till) {
            $fromTime = join('-', array_reverse(explode('-', $from)));
            $tillTime = join('-', array_reverse(explode('-', $till)));
            $app_states->whereDate('applications.date', '>=', $fromTime)
                ->whereDate('applications.date', '<=', $tillTime);
        }
//        if (!is_null($app_type_selector)) {
//            if($app_type_selector == 3){
//                $app_states = $app_states->leftjoin("test_programs","test_programs.app_id","=","applications.id")
//                    ->leftjoin("final_results","final_results.test_program_id","=","test_programs.id")
//                    ->whereNull('final_results.type');
//            }else{
//                $app_states = $app_states->join("test_programs","test_programs.app_id","=","applications.id")
//                    ->join("final_results","final_results.test_program_id","=","test_programs.id")
//                    ->where('final_results.type','=',$app_type_selector);
//            }
//        }

        $app_states = $app_states->groupBy('tbl_states.id', 'tbl_states.name')
            ->orderBy('application_count', 'desc')
            ->get();
        $crops = $crops->orderBy('applications_count','desc')
            ->get();
        $sum_amount = $sum_amount->sum('amount');

        $states = DB::table('tbl_states')->where('country_id', '=', 234)->get()->toArray();
        $crop_names = DB::table('crops_name')->get()->toArray();


        return view('dashboard.dashboard', compact(
            'crops',
            'app_states',
            'states',
            'crop_names',
            'applications_count',
            'from',
            'till',
            'city',
            'crop',
            'app_type_selector',
            'sum_amount'
        ));
    }

}
