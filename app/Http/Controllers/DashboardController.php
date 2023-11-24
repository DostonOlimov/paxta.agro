<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Area;
use App\Models\CropsName;
use App\Models\FinalResult;
use App\Models\Region;
use App\Models\Sertificate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use NunoMaduro\Collision\Adapters\Phpunit\State;


class DashboardController extends Controller
{

    public function index()
    {
        $user = auth()->user();
        if ($user->role== 1){
            return redirect(route('users.list'));
        }

    }
    public function colors(){
        return redirect(route('layouts.colors'));
    }

    public function home()
    {
        $crops = CropsName::get();
        $startDate = Carbon::now()->subDays(30)->toDateString();
        $all_app_count = Application::count();
        $local_app = Application::where('type','=',Application::TYPE_1)->count();
        $global_app = Application::where('type','=',Application::TYPE_2)->count();
        $month_app_count = Application::whereDate('created_at', '>=', $startDate)->count();
        $month_cer_count = Sertificate::whereDate('created_at', '>=', $startDate)->count();
        $all_cer_count = Sertificate::count();
        $crop_name = null;
        return view('front.index',
            compact('crops','crop_name','all_app_count','month_app_count','month_cer_count','all_cer_count','local_app','global_app')
        );
    }
    public function about($id)
    {
        $crop_name = CropsName::find($id);
        $crops = CropsName::get();
        $apps = Application::get();
//        foreach($crops as $crop){
//            $crop_id = $crop->id;
//            $chart_data[] = [
//                'year'=>$crop->name,
//                'income'=>Application::whereHas('crops', function ($query) use ($crop_id) {
//                                $query->where('crop_data.name_id', $crop_id);
//                                })->count()
//                    ];
//        }
        $chart_data = array();
        $chart_data[0]['name'] = "Kelib tushgan arizalar";
        $chart_data[0]['ball'] = Application::whereHas('crops', function ($query) use ($id) {
            $query->where('crop_data.name_id', $id);
                })
                ->count();
        $chart_data[1]['name'] = "Berilgan sertifikatlar";
        $chart_data[1]['ball'] =  FinalResult::whereHas('test_program.application.crops', function ($query) use ($id) {
            $query->where('crop_data.name_id', $id);
                })
                ->where('type',2)
                ->count();
        $chart_data[2]['name'] = "No'mufiq deb topilganlar";
        $chart_data[2]['ball'] = FinalResult::whereHas('test_program.application.crops', function ($query) use ($id) {
            $query->where('crop_data.name_id', $id);
        })
            ->where('type',0)
            ->count();

        $states = Region::get();
//        echo '<pre>';
//        var_dump($states);die();
//        $balls = [];
        foreach($states as $state)
        {
            $state_id = $state->id;
            $ball = FinalResult::whereHas('test_program.application.crops', function ($query) use ($id) {
                $query->where('crop_data.name_id', $id);
            })
                ->whereHas('test_program.application.organization.city',function ($query) use ($state_id) {
                    $query->where('tbl_cities.state_id', $state_id);
                })
                ->where('type',2)
                ->count();
            $balls[] = ['year'=>$state->name,'income'=>$ball];
        }
//        $balls[] = ['year'=>"Max",'income'=>100];

        return view('front.about',[
            'crop_name'=>$crop_name,
            'crops'=>$crops,
            'chart_data' => $chart_data,
            'balls' => $balls
        ]);
    }
    public function all()
    {
        $crop_name = 'all';
        $crops = CropsName::get();
        foreach($crops as $crop){
            $crop_id = $crop->id;
            $chart_data[] = [
                'year'=>$crop->name,
                'income'=>FinalResult::whereHas('test_program.application.crops', function ($query) use ($crop_id) {
                    $query->where('crop_data.name_id', $crop_id);
                })
                    ->where('type',2)
                    ->count()
                    ];
        }

        return view('front.all',[
            'crop_name'=>$crop_name,
            'crops'=>$crops,
            'data1'=>$chart_data
        ]);
    }


}
