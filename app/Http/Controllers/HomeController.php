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

        $user = auth()->user();
        $timezone = $user->timezone;
        config(['app.timezone' => $timezone]);
        $role = auth()->user()->role == 'admin' ? 'Admin':(auth()->user()->role == 171 ? 'Tadbirkor':'Inspektor');
        $startDate = Carbon::now()->subDays(30)->toDateString();
        $all_app_count = Application::count();
        $local_app = Application::where('type','=',Application::TYPE_1)->count();
        $global_app = Application::where('type','=',Application::TYPE_2)->count();
        $month_app_count = Application::whereDate('created_at', '>=', $startDate)->count();
        $month_cer_count = Sertificate::whereDate('created_at', '>=', $startDate)->count();
        $all_cer_count = Sertificate::count();

           $crops = CropsName::select('crops_name.name','crops_name.id',
                DB::raw('COUNT(crop_data.id) as count'),
                DB::raw('SUM((CASE WHEN crop_data.measure_type = 2 THEN amount * 0.001 ELSE amount END)) as total_amount'))
            ->leftJoin("crop_data","crop_data.name_id","=","crops_name.id")
               ->join("applications","crop_data.id","=","applications.id");

        if ($city) {
            $crops = $crops->leftJoin("organization_companies","organization_companies.id","=","applications.organization_id")
                ->leftJoin("tbl_cities","tbl_cities.id","=","organization_companies.city_id")
                ->where('tbl_cities.state_id','=',$city);
        }

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
                $crops->whereDate('applications.date', '>=', $fromTime)
                    ->whereDate('applications.date', '<=', $tillTime);
                $app_states->whereDate('applications.date', '>=', $fromTime)
                    ->whereDate('applications.date', '<=', $tillTime);
            }
        if (!is_null($app_type_selector)) {
            if($app_type_selector == 3){
                $crops = $crops->leftjoin("test_programs","test_programs.app_id","=","applications.id")
                    ->leftjoin("final_results","final_results.test_program_id","=","test_programs.id")
                    ->whereNull('final_results.type');
                $app_states = $app_states->leftjoin("test_programs","test_programs.app_id","=","applications.id")
                    ->leftjoin("final_results","final_results.test_program_id","=","test_programs.id")
                    ->whereNull('final_results.type');
            }else{
                $crops = $crops->join("test_programs","test_programs.app_id","=","applications.id")
                    ->join("final_results","final_results.test_program_id","=","test_programs.id")
                    ->where('final_results.type','=',$app_type_selector);
                $app_states = $app_states->join("test_programs","test_programs.app_id","=","applications.id")
                    ->join("final_results","final_results.test_program_id","=","test_programs.id")
                    ->where('final_results.type','=',$app_type_selector);
            }
        }

        $app_states = $app_states->groupBy('tbl_states.id', 'tbl_states.name')
            ->orderBy('application_count', 'desc')
            ->get();
        $crops = $crops->groupBy("crops_name.name",'crops_name.id')
            ->orderBy('count','desc')
            ->get();

        $states = DB::table('tbl_states')->where('country_id', '=', 234)->get()->toArray();
        $crop_names = DB::table('crops_name')->get()->toArray();

        $sum_amount = AktAmount::sum('amount');

        return view('dashboard.dashboard',
            compact('crops','app_states','states','crop_names','role','all_app_count',
                'month_app_count','month_cer_count','all_cer_count','local_app','global_app',
                'from','till','city','crop','app_type_selector','sum_amount')
        );
    }
}
