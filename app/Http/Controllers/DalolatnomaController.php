<?php

namespace App\Http\Controllers;

use App\Models\AktAmount;
use App\Models\Application;
use App\Models\CropData;
use App\Models\Decision;
use App\Models\Dalolatnoma;
use App\Models\GinBalles;
use App\Models\Indicator;
use App\Models\Laboratories;
use App\Models\Nds;
use App\Models\Sertificate;
use App\Models\TestPrograms;
use App\Models\DefaultModels\tbl_activities;
use App\Models\User;
use App\Rules\DifferentsShtrixKod;
use App\Rules\EqualToyCount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DalolatnomaController extends Controller
{
    //search
    public function search(Request $request)
    {
        $user = Auth::user();
        $city = $request->input('city');
        $crop = $request->input('crop');
        $from = $request->input('from');
        $till = $request->input('till');

        $apps= TestPrograms::with('application')
            ->with('application.crops.name')
            ->with('application.crops.type')
            ->with('application.organization');
        if ($user->role == \App\Models\User::STATE_EMPLOYEE) {
            $user_city = $user->state_id;
            $apps = $apps->whereHas('application.organization', function ($query) use ($user_city) {
                $query->whereHas('city', function ($query) use ($user_city) {
                    $query->where('state_id', '=', $user_city);
                });
            });
        }
        if ($from && $till) {
            $fromTime = join('-', array_reverse(explode('-', $from)));
            $tillTime = join('-', array_reverse(explode('-', $till)));
            $apps->whereHas('application', function ($query) use ($fromTime,$tillTime) {
                $apps = $query->whereDate('date', '>=', $fromTime)
                    ->whereDate('date', '<=', $tillTime);
            });
        }
        if ($city) {
            $apps = $apps->whereHas('application.organization', function ($query) use ($city) {
                $query->whereHas('city', function ($query) use ($city) {
                    $query->where('state_id', '=', $city);
                });
            });
        }
        if ($crop) {
            $apps = $apps->whereHas('application.crops', function ($query) use ($crop) {
                $query->where('name_id', '=', $crop);
            });
        }
        $apps->when($request->input('s'), function ($query, $searchQuery) {
            $query->where(function ($query) use ($searchQuery) {
                if (is_numeric($searchQuery)) {
                    $query->whereHas('application', function ($query) use ($searchQuery) {
                        $query->where('app_number', $searchQuery);
                    });
                } else {
                    $query->whereHas('application.crops.name', function ($query) use ($searchQuery) {
                        $query->where('name', 'like', '%' . addslashes($searchQuery) . '%');
                    })->orWhereHas('application.crops.type', function ($query) use ($searchQuery) {
                        $query->where('name', 'like', '%' . addslashes($searchQuery) . '%');
                    })->orWhereHas('application.crops.generation', function ($query) use ($searchQuery) {
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
        return view('dalolatnoma.search', compact('tests','from','till','city','crop'));
    }
    //index
    public function add($id)
    {
        $test = TestPrograms::find($id);
        return view('dalolatnoma.add', compact('test'));
    }

    //list
    public function list()
    {
        $title = 'Normativ hujjatlar';
        $testss = Nds::with('crops')->orderBy('id')->get();
        return view('dalolatnoma.list', compact('decisions','title'));
    }

    //  store
    public function store(Request $request)
    {
        $kod_toy = $request->input('kod_toy');

        $request->validate([
            'kod_toy.*.0' => 'required|numeric',
            'kod_toy.*.1' => ['required', 'numeric', new DifferentsShtrixKod(), new EqualToyCount()],
            'toy_count' => ['required', 'numeric', new EqualToyCount()],
            'kod_toy.*.3' => ['required', 'numeric', new EqualToyCount()],
        ]);

        $userA = Auth::user();
        $this->authorize('create', Application::class);
        $test_id = $request->input('test_id');
        $number = $request->input('number');
        $selection_code = $request->input('selection_code');
        $toy_count = $request->input('toy_count');
        $amount = $request->input('amount');
        $party_number = $request->input('party_number');
        $nav = $request->input('nav');
        $sinf = $request->input('sinf');

        $test = new Dalolatnoma();
        $test->test_program_id = $test_id;
        $test->number = $number;
        $test->date = join('-', array_reverse(explode('-', $request->input('date'))));
        $test->selection_code = $selection_code;
        $test->toy_count = $toy_count;
        $test->amount = $amount;
        $test->party = $party_number;
        $test->nav = $nav;
        $test->sinf = $sinf;
        $test->save();

        $amounts = [];

        for ($i = 0; $i < count($kod_toy); $i++) {
            $from_kod = $kod_toy[$i][0];
            $to_kod = $kod_toy[$i][1];
            for ($j = $from_kod; $j <= $to_kod; $j++) {
                $amounts[] = [
                    'dalolatnoma_id' => $test->id,
                    'shtrix_kod' => $j,
                ];
            }
        }
        DB::transaction(function () use ($amounts) {
            AktAmount::insert($amounts);
        });

        for ($i = 0; $i < count($kod_toy); $i++) {
            if ($kod_toy[$i][0] && $kod_toy[$i][1] && $kod_toy[$i][2] && $kod_toy[$i][3]) {
                $ball = new GinBalles();
                $ball->dalolatnoma_id = $test->id;
                $ball->from_number = $kod_toy[$i][0];
                $ball->to_number = $kod_toy[$i][1];
                $ball->from_toy = $kod_toy[$i][2];
                $ball->to_toy = $kod_toy[$i][3];
                $ball->save();
            }
        }

        $active = new tbl_activities;
        $active->ip_adress = $_SERVER['REMOTE_ADDR'];
        $active->user_id = $userA->id;
        $active->action_id = $test->id;
        $active->action_type = 'new_dalolatnoma';
        $active->action = "Dalolatnoma qo'shildi";
        $active->time = date('Y-m-d H:i:s');
        $active->save();

        return redirect('/akt_amount/search');
    }

    public function edit($id)
    {
        $userA = Auth::user();
        $result = Dalolatnoma::find($id);
        $test = TestPrograms::find($result->test_program_id);
        $certificate =  Sertificate::where('final_result_id', '=', $result->id)->first();
        $gin_balles = GinBalles::where('dalolatnoma_id', $id)->get();


        return view('dalolatnoma.edit', compact('test', 'result', 'certificate', 'gin_balles'));
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
        if($result->date != $request->input('date')){
            $result->date = join('-', array_reverse(explode('-', $request->input('date'))));
        }
        $result->selection_code = $request->input('selection_code');
        $result->toy_count = $request->input('toy_count');
        $result->amount = $request->input('amount');
        $result->party = $request->input('party_number');
        $result->nav =  $request->input('nav');
        $result->sinf = $request->input('sinf');
        $result->save();

        foreach ($kod_toy as $item) {
                $conditions = ['id' => $item[0]];
                $data = [
                    'dalolatnoma_id' => $id,
                    'from_number' => $item[1],
                    'to_number' => $item[2],
                    'from_toy' => $item[3],
                    'to_toy' => $item[4],
                ];

            GinBalles::updateOrCreate($conditions, $data);
        }

        AktAmount::where('dalolatnoma_id', $id)->delete();
        $amount = new AktAmount();

        $amounts = [];
    for ($i = 0; $i < count($kod_toy); $i++) {
            $from_kod = $kod_toy[$i][0];
            $to_kod = $kod_toy[$i][1];
        for ($j = $from_kod; $j <= $to_kod; $j++) {
            $amounts[] = [
                'dalolatnoma_id' => $id,
                'shtrix_kod' => $j,
            ];
        }
    }

    DB::transaction(function () use ($amounts) {
        AktAmount::insert($amounts);
    });

        $active = new tbl_activities;
        $active->ip_adress = $_SERVER['REMOTE_ADDR'];
        $active->user_id = $userA->id;
        $active->action_id = $result->id;
        $active->action_type = 'edit_dalolatnoma';
        $active->action = "Dalolatnoma o'zgartirildi";
        $active->time = date('Y-m-d H:i:s');
        $active->save();
        return redirect('/dalolatnoma/search')->with('message', 'Successfully Updated');

    }


    public function destory($id)
    {
        Decision::destroy($id);
        return redirect('dalolatnoma/search')->with('message', 'Successfully Deleted');
    }
    public function view($id)
    {
        $tests = Dalolatnoma::find($id);
        return view('dalolatnoma.show', [
            'result' => $tests,
        ]);
    }

}
