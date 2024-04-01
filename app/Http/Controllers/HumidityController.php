<?php

namespace App\Http\Controllers;

use App\Models\AktAmount;
use App\Models\Application;
use App\Models\CropsSelection;
use App\Models\Decision;
use App\Models\Dalolatnoma;
use App\Models\GinBalles;
use App\Models\Nds;
use App\Models\Sertificate;
use App\Models\TestPrograms;
use App\Models\DefaultModels\tbl_activities;
use App\Models\Humidity;
use App\Rules\DifferentsShtrixKod;
use App\Rules\EqualToyCount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HumidityController extends Controller
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
        if ($user->role == \App\Models\User::STATE_EMPLOYEE) {
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
        return view('humidity.search', compact('tests','from','till','city','crop'));
    }
    //index
    public function add($id)
    {
        $test = Dalolatnoma::find($id);
        $selection = CropsSelection::get();

        return view('humidity.add', compact('test', 'selection'));
    }

    //list
    public function list()
    {
        $title = 'Normativ hujjatlar';
        $testss = Nds::with('crops')->orderBy('id')->get();
        return view('humidity.list', compact('decisions','title'));
    }

    //  store
    public function store(Request $request)
    {
        $userA = Auth::user();
        $this->authorize('create', Application::class);
        $test_id = $request->input('dalolatnoma_id');
        $number = $request->input('number');
        $selection_code = $request->input('selection_code');
        $toy_count = $request->input('toy_count');
        $party_number = $request->input('party_number');
        $nav = $request->input('nav');
        $sinf = $request->input('sinf');

        $test = new Humidity();
        $test->dalolatnoma_id = $test_id;
        $test->number = $number;
        $test->date = join('-', array_reverse(explode('-', $request->input('date'))));
        $test->selection_code = $selection_code;
        $test->toy_count = $toy_count;
        $test->party = $party_number;
        $test->nav = $nav;
        $test->sinf = $sinf;
        $test->save();

        return redirect('/humidity/search');
    }

    public function edit($id)
    {
        $userA = Auth::user();
        $result = Dalolatnoma::find($id);
        $test = TestPrograms::find($result->test_program_id);
        $certificate =  Sertificate::where('final_result_id', '=', $result->id)->first();
        $gin_balles = GinBalles::where('humidity_id', $id)->get();
        $selection = CropsSelection::get();

        return view('humidity.edit', compact('test', 'result', 'certificate', 'gin_balles','selection'));
    }


    // application update

    public function update($id, Request $request)
    {
        $kod_toy = $request->input('kod_toy');

        $request->validate([
            'kod_toy.*.1' => 'required|numeric',
            'kod_toy.*.2' => ['required', 'numeric', new DifferentsShtrixKod(), new EqualToyCount()],
            'toy_count' => ['required', 'numeric', new EqualToyCount()],
            'kod_toy.*.4' => ['required', 'numeric', new EqualToyCount()],
        ]);

        $userA = Auth::user();
        $result = Dalolatnoma::find($id);
        $result->number = $request->input('number');
        if ($result->date != $request->input('date')) {
            $result->date = join('-', array_reverse(explode('-', $request->input('date'))));
        }
        $result->selection_code = $request->input('selection_code');
        $result->toy_count = $request->input('toy_count');
        $result->amount = $request->input('amount');
        $result->party = $request->input('party_number');
        $result->nav = $request->input('nav');
        $result->sinf = $request->input('sinf');
        $result->save();
        $akt_amount = AktAmount::where('humidity_id', $id)->sum('amount');
        if ($akt_amount = 0) {
            foreach ($kod_toy as $item) {
                $conditions = ['id' => $item[0]];
                $data = [
                    'humidity_id' => $id,
                    'from_number' => $item[1],
                    'to_number' => $item[2],
                    'from_toy' => $item[3],
                    'to_toy' => $item[4],
                ];

                GinBalles::updateOrCreate($conditions, $data);
            }

            AktAmount::where('humidity_id', $id)->delete();
            $amount = new AktAmount();

            $amounts = [];
            for ($i = 0; $i < count($kod_toy); $i++) {
                $from_kod = $kod_toy[$i][0];
                $to_kod = $kod_toy[$i][1];
                for ($j = $from_kod; $j <= $to_kod; $j++) {
                    $amounts[] = [
                        'humidity_id' => $id,
                        'shtrix_kod' => $j,
                    ];
                }
            }

            DB::transaction(function () use ($amounts) {
                AktAmount::insert($amounts);
            });
        }

        $active = new tbl_activities;
        $active->ip_adress = $_SERVER['REMOTE_ADDR'];
        $active->user_id = $userA->id;
        $active->action_id = $result->id;
        $active->action_type = 'edit_humidity';
        $active->action = "Dalolatnoma o'zgartirildi";
        $active->time = date('Y-m-d H:i:s');
        $active->save();
        return redirect('/humidity/search')->with('message', 'Successfully Updated');

    }


    public function destory($id)
    {
        Decision::destroy($id);
        return redirect('humidity/search')->with('message', 'Successfully Deleted');
    }
    public function view($id)
    {
        $tests = Dalolatnoma::find($id);
        return view('humidity.show', [
            'result' => $tests,
        ]);
    }
    public function myadd()
    {
        $amounts = [];

        for ($j =81252 ; $j <=81471  ; $j++) {
            $amounts[] = [
                'humidity_id' => 10,
                'shtrix_kod' => $j,
            ];
        }

        DB::transaction(function () use ($amounts) {
            AktAmount::insert($amounts);
        });
    }
}
