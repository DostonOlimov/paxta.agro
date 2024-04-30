<?php

namespace App\Http\Controllers;

use App\Models\AktAmount;
use App\Models\Application;
use App\Models\CropsSelection;
use App\Models\Decision;
use App\Models\Dalolatnoma;
use App\Models\GinBalles;
use App\Models\HumidityResult;
use App\Models\Nds;
use App\Models\Sertificate;
use App\Models\TestPrograms;
use App\Models\DefaultModels\tbl_activities;
use App\Models\Humidity;
use App\Models\User;
use App\Rules\DifferentsShtrixKod;
use App\Rules\EqualToyCount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HumidityResultController extends Controller
{
    //search
    public function search(Request $request)
    {
        $user = Auth::user();
        $city = $request->input('city');
        $crop = $request->input('crop');
        $from = $request->input('from');
        $till = $request->input('till');

        $apps= Dalolatnoma::with('test_program')
            ->with('test_program.application')
            ->with('test_program.application.decision')
            ->with('test_program.application.crops.name')
            ->with('test_program.application.crops.type')
            ->with('test_program.application.organization');
        if ($user->branch_id == User::BRANCH_STATE ) {
            $user_city = $user->state_id;
            $apps = $apps->whereHas('test_program.application.organization', function ($query) use ($user_city) {
                $query->whereHas('city', function ($query) use ($user_city) {
                    $query->where('state_id', '=', $user_city);
                });
            });
        }
        if ($from && $till) {
            $fromTime = join('-', array_reverse(explode('-', $from)));
            $tillTime = join('-', array_reverse(explode('-', $till)));
            $apps->whereHas('test_program.application', function ($query) use ($fromTime,$tillTime) {
                $query->whereDate('date', '>=', $fromTime)
                    ->whereDate('date', '<=', $tillTime);
            });
        }
        if ($city) {
            $apps = $apps->whereHas('test_program.application.organization', function ($query) use ($city) {
                $query->whereHas('city', function ($query) use ($city) {
                    $query->where('state_id', '=', $city);
                });
            });
        }
        if ($crop) {
            $apps = $apps->whereHas('test_program.application.crops', function ($query) use ($crop) {
                $query->where('name_id', '=', $crop);
            });
        }
        $apps->when($request->input('s'), function ($query, $searchQuery) {
            $query->where(function ($query) use ($searchQuery) {
                if (is_numeric($searchQuery)) {
                    $query->whereHas('test_program.application', function ($query) use ($searchQuery) {
                        $query->where('app_number', $searchQuery);
                    });
                } else {
                    $query->whereHas('test_program.application.crops.name', function ($query) use ($searchQuery) {
                        $query->where('name', 'like', '%' . addslashes($searchQuery) . '%');
                    })->orWhereHas('test_program.application.crops.type', function ($query) use ($searchQuery) {
                        $query->where('name', 'like', '%' . addslashes($searchQuery) . '%');
                    })->orWhereHas('test_program.application.crops.generation', function ($query) use ($searchQuery) {
                        $query->where('name', 'like', '%' . addslashes($searchQuery) . '%');
                    });

                }
            });
        });

        $tests = $apps->latest('id')
            ->paginate(50)
            ->appends(['s' => $request->input('s')])
            ->appends(['till' => $request->input('till')])
            ->appends(['from' => $request->input('from')])
            ->appends(['city' => $request->input('city')])
            ->appends(['crop' => $request->input('crop')]);
        return view('humidity_result.search', compact('tests','from','till','city','crop'));
    }
    //index
    public function add($id)
    {
        $test = Dalolatnoma::find($id);
        return view('humidity_result.add', compact('test'));
    }

    //list
    public function list()
    {
        $title = 'Normativ hujjatlar';
        $testss = Nds::with('crops')->orderBy('id')->get();
        return view('humidity_result.list', compact('decisions','title'));
    }

    //  store
    public function store(Request $request)
    {
        $user = Auth::user();
        $this->authorize('create', Application::class);

        $data = $request->only([
            'dalolatnoma_id',
            'number',
            'm0',
            'm1',
            'mk0',
            'mk1',
            'kalibrovka',
        ]);

        $data['date'] = join('-', array_reverse(explode('-', $request->input('date'))));

        $humidity_result = new HumidityResult();
        $humidity_result->fill($data);
        $humidity_result->save();

        return redirect('/humidity_result/search');
    }

    public function edit($id)
    {
        $userA = Auth::user();
        $result = HumidityResult::find($id);

        return view('humidity_result.edit', compact('result'));
    }


    // application update
    public function update($id, Request $request)
    {
        $user = Auth::user();
        $result = HumidityResult::findOrFail($id);

        $result->fill($request->only([
            'number',
            'date',
            'm0',
            'mk0',
            'm1',
            'mk1',
            'kalibrovka',
        ]));

        if ($result->isDirty('date')) {
            $result->date = join('-', array_reverse(explode('-', $result->date)));
        }

        $result->save();

        return redirect('/humidity_result/search')->with('message', 'Successfully Updated');
    }


    public function destory($id)
    {
        Decision::destroy($id);
        return redirect('humidity_result/search')->with('message', 'Successfully Deleted');
    }
    public function view($id)
    {
        $tests = HumidityResult::find($id);
        $date = Carbon::parse($tests->date);

        $uzbekMonthNames = [
            '01' => 'yanvar',
            '02' => 'fevral',
            '03' => 'mart',
            '04' => 'aprel',
            '05' => 'may',
            '06' => 'iyun',
            '07' => 'iyul',
            '08' => 'avgust',
            '09' => 'sentabr',
            '10' => 'oktabr',
            '11' => 'noyabr',
            '12' => 'dekabr'
        ];

        $my_date = $date->isoFormat("D") . ' - ' . $uzbekMonthNames[$date->isoFormat("MM")] . ' '. $date->isoFormat("Y") ;

        return view('humidity_result.show', [
            'result' => $tests,
            'date' => $my_date
        ]);
    }
}
