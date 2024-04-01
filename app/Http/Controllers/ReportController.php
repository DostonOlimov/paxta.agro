<?php

namespace App\Http\Controllers;

use App\Exports\FullReportExport;
use App\Http\Requests;
use App\Models\Application;
use App\Models\Area;
use App\Models\FinalResult;
use App\Models\Invoice;
use App\Models\ListRegion;
use App\Models\PaymentCategory;
use App\Models\Region;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use NunoMaduro\Collision\Adapters\Phpunit\State;

//use NunoMaduro\Collision\Adapters\Phpunit\State;

class ReportController extends Controller{

    public function __construct(){
        $this->middleware('auth');
    }

    private function reportData(Request $request)
    {
        $reportPeriod = [
            Carbon::createFromFormat(USER_DATE_FORMAT, $request->input('date_from', now()->startOfQuarter()->format(USER_DATE_FORMAT)))->startOfDay(),
            Carbon::createFromFormat(USER_DATE_FORMAT, $request->input('date_to', now()->format(USER_DATE_FORMAT)))->endOfDay(),
        ];

$regions = Region::get();
        return compact(
             //   'paymentCategories',
                'regions'
            ) + ['reportData' => Application::get()];
    }

    public function report(Request $request)
    {
        $user = Auth::User();
        $app_type_selector = $request->input('app_type_selector');
        $city = $request->input('city');
        $crop = $request->input('crop');
        $from = $request->input('from');
        $till = $request->input('till');

        $apps = Application::with('organization')
            ->with('organization.city')
            ->with('organization.city.region')
            ->with('prepared')
            ->with('crops')
            ->with('crops.country')
            ->with('crops.name')
            ->with('crops.type')
            ->with('crops.generation')
            ->with('decision')
            ->with('tests')
            ->with('tests.result')
            ->with('tests.result.certificate')
            ->whereIn('status',[Application::STATUS_ACCEPTED,Application::STATUS_FINISHED]);

        if($user->role == \App\Models\User::STATE_EMPLOYEE){
            $user_city = $user->state_id;
            $apps = $apps->whereHas('organization', function ($query) use ($user_city) {
                $query->whereHas('city', function ($query) use ($user_city) {
                    $query->where('state_id', '=', $user_city);
                });
            });
        }
        if ($from && $till) {
            $fromTime = join('-', array_reverse(explode('-', $from)));
            $tillTime = join('-', array_reverse(explode('-', $till)));
            $apps = $apps->whereDate('date', '>=', $fromTime)
                ->whereDate('date', '<=', $tillTime);
        }
        if ($city) {
            $apps = $apps->whereHas('organization', function ($query) use ($city) {
                $query->whereHas('city', function ($query) use ($city) {
                    $query->where('state_id', '=', $city);
                });
            });
        }
        if ($crop) {
            $apps = $apps->whereHas('crops', function ($query) use ($crop) {
                $query->where('name_id', '=', $crop);
            });
        }
        if (!is_null($app_type_selector)) {

            if($app_type_selector == 3){
                $apps = $apps->doesntHave('tests.result');
            }else{
                $apps = $apps->whereHas('tests.result', function ($query) use ($app_type_selector) {
                    $query->where('type', '=', $app_type_selector);
                });
            }
        }
        $apps->when($request->input('s'), function ($query, $searchQuery) {
            $query->where(function ($query) use ($searchQuery) {
                if (is_numeric($searchQuery)) {
                    $query->orWhere('app_number', $searchQuery);
                } else {
                    $query->whereHas('crops.name', function ($query) use ($searchQuery) {
                        $query->where('name', 'like', '%' . addslashes($searchQuery) . '%');
                    })->orWhereHas('crops.type', function ($query) use ($searchQuery) {
                        $query->where('name', 'like', '%' . addslashes($searchQuery) . '%');
                    })->orWhereHas('crops.generation', function ($query) use ($searchQuery) {
                        $query->where('name', 'like', '%' . addslashes($searchQuery) . '%');
                    });

                }
            });
        });

        $apps = $apps->latest('id')
            ->paginate(50)
            ->appends(['s' => $request->input('s')])
            ->appends(['till' => $request->input('till')])
            ->appends(['from' => $request->input('from')])
            ->appends(['city' => $request->input('city')])
            ->appends(['crop' => $request->input('crop')]);
        return view('reports.full_report', compact('apps','from','till','city','crop'));
    }

    public function myreport(Request $request)
    {
        $user = Auth::User();
        $app_type_selector = $request->input('app_type_selector');
        $city = $request->input('city');
        $crop = $request->input('crop');
        $from = $request->input('from');
        $till = $request->input('till');

        $apps = FinalResult::with('test_program');
//        $apps = Application::with('organization')
//            ->with('organization.city')
//            ->with('organization.city.region')
//            ->with('prepared')
//            ->with('crops')
//            ->with('crops.country')
//            ->with('crops.name')
//            ->with('crops.type')
//            ->with('crops.generation')
//            ->with('decision')
//            ->with('tests')
//            ->with('tests.result')
//            ->with('tests.result.certificate')
//            ->whereIn('status',[Application::STATUS_ACCEPTED,Application::STATUS_FINISHED]);

        if($user->role == \App\Models\User::STATE_EMPLOYEE){
            $user_city = $user->state_id;
            $apps = $apps->whereHas('organization', function ($query) use ($user_city) {
                $query->whereHas('city', function ($query) use ($user_city) {
                    $query->where('state_id', '=', $user_city);
                });
            });
        }
        if ($from && $till) {
            $fromTime = join('-', array_reverse(explode('-', $from)));
            $tillTime = join('-', array_reverse(explode('-', $till)));
            $apps = $apps->whereDate('date', '>=', $fromTime)
                ->whereDate('date', '<=', $tillTime);
        }
        if ($city) {
            $apps = $apps->whereHas('organization', function ($query) use ($city) {
                $query->whereHas('city', function ($query) use ($city) {
                    $query->where('state_id', '=', $city);
                });
            });
        }
        if ($crop) {
            $apps = $apps->whereHas('crops', function ($query) use ($crop) {
                $query->where('name_id', '=', $crop);
            });
        }
        if (!is_null($app_type_selector)) {

            if($app_type_selector == 3){
                $apps = $apps->doesntHave('tests.result');
            }else{
                $apps = $apps->whereHas('tests.result', function ($query) use ($app_type_selector) {
                    $query->where('type', '=', $app_type_selector);
                });
            }
        }
        $apps->when($request->input('s'), function ($query, $searchQuery) {
            $query->where(function ($query) use ($searchQuery) {
                if (is_numeric($searchQuery)) {
                    $query->orWhere('app_number', $searchQuery);
                } else {
                    $query->whereHas('crops.name', function ($query) use ($searchQuery) {
                        $query->where('name', 'like', '%' . addslashes($searchQuery) . '%');
                    })->orWhereHas('crops.type', function ($query) use ($searchQuery) {
                        $query->where('name', 'like', '%' . addslashes($searchQuery) . '%');
                    })->orWhereHas('crops.generation', function ($query) use ($searchQuery) {
                        $query->where('name', 'like', '%' . addslashes($searchQuery) . '%');
                    });

                }
            });
        });

        $apps = $apps->latest('id')
            ->paginate(50)
            ->appends(['s' => $request->input('s')])
            ->appends(['till' => $request->input('till')])
            ->appends(['from' => $request->input('from')])
            ->appends(['city' => $request->input('city')])
            ->appends(['crop' => $request->input('crop')]);
        return view('reports.report', compact('apps','from','till','city','crop'));
    }

}
