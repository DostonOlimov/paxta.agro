<?php

namespace App\Http\Controllers;

use App\Models\AktAmount;
use App\Models\Application;
use App\Models\CropsName;
use App\Models\Dalolatnoma;
use App\Models\FinalResult;
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

        $dalolatnoma = Dalolatnoma::with('test_program');
        $applications_count  = $dalolatnoma->count();
        if ($from && $till) {
            $fromTime = join('-', array_reverse(explode('-', $from)));
            $tillTime = join('-', array_reverse(explode('-', $till)));
            $dalolatnoma = $dalolatnoma->whereDate('date', '>=', $fromTime)
                ->whereDate('date', '<=', $tillTime);
        }

        if($city){
            $dalolatnoma = $dalolatnoma->whereHas('test_program.application.organization', function ($query) use ($city) {
                $query->whereHas('city', function ($query) use ($city) {
                    $query->where('state_id', '=', $city);
                });
            });
        }
        $state_count = $dalolatnoma->count();
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
            $sum_amount = $sum_amount->whereHas('dalolatnoma', function ($query) use ($fromTime,$tillTime) {
                $query->whereDate('date', '>=', $fromTime)
                    ->whereDate('date', '<=', $tillTime);
            });
        }
        $year =  session('year') ?  session('year') : 2024;
        $app_states = Region::select('tbl_states.id', 'tbl_states.name', DB::raw('COUNT(applications.id) as application_count'))
            ->leftJoin('tbl_cities', 'tbl_states.id', '=', 'tbl_cities.state_id')
            ->leftJoin('organization_companies', 'tbl_cities.id', '=', 'organization_companies.city_id')
            ->join('applications', 'organization_companies.id', '=', 'applications.organization_id')
            ->join('test_programs', 'applications.id', '=', 'test_programs.app_id')
            ->join('dalolatnoma', 'test_programs.id', '=', 'dalolatnoma.test_program_id')
            ->leftJoin('crop_data', 'applications.crop_data_id', '=', 'crop_data.id')
            ->where('crop_data.year',$year);

        if ($crop) {
            $app_states = $app_states->where('crop_data.name_id', '=', $crop);
        }
        if ($from && $till) {
            $fromTime = join('-', array_reverse(explode('-', $from)));
            $tillTime = join('-', array_reverse(explode('-', $till)));

            $app_states->whereDate('dalolatnoma.date', '>=', $fromTime)
                ->whereDate('dalolatnoma.date', '<=', $tillTime);
        }
        //        sum of final result
        $sum_final_result = FinalResult::with('dalolatnoma');
        if($city){
            $sum_final_result = $sum_final_result->whereHas('dalolatnoma.test_program.application.organization', function ($query) use ($city) {
                $query->whereHas('city', function ($query) use ($city) {
                    $query->where('state_id', '=', $city);
                });
            });
        }
        if ($from && $till) {
            $fromTime = join('-', array_reverse(explode('-', $from)));
            $tillTime = join('-', array_reverse(explode('-', $till)));
            $sum_final_result = $sum_final_result->whereHas('dalolatnoma', function ($query) use ($fromTime,$tillTime) {
                $query->whereDate('date', '>=', $fromTime)
                    ->whereDate('date', '<=', $tillTime);
            });
        }
        $sum_final_result = $sum_final_result->sum('amount');

        $app_states = $app_states->groupBy('tbl_states.id', 'tbl_states.name')
            ->orderBy('application_count', 'desc')
            ->get();

        $count_amount = $sum_amount->count('id');
        $sum_amount = $sum_amount->sum('amount');

        $states = DB::table('tbl_states')->where('country_id', '=', 234)->get()->toArray();
        $crop_names = DB::table('crops_name')->get()->toArray();


        return view('dashboard.dashboard', compact(
            'app_states',
            'states',
            'crop_names',
            'applications_count',
            'from',
            'till',
            'city',
            'crop',
            'app_type_selector',
            'sum_amount',
            'count_amount',
            'sum_final_result',
            'state_count'
        ));
    }

}
