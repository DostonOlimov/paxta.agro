<?php

namespace App\Http\Controllers;

use App\Models\AktAmount;
use App\Models\Application;
use App\Models\CropData;
use App\Models\CropsSelection;
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
use Carbon\Carbon;
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
        $sort_by = $request->get('sort_by', 'id'); // default sorting by 'id'
        $sort_order = $request->get('sort_order', 'desc'); // default order is ascending

        // Validate the sort_by column to prevent SQL injection
        $columns = ['id', 'party_number', 'date','organization','year']; // Add your table columns here
        if (!in_array($sort_by, $columns)) {
            $sort_by = 'id';
        }

        $apps= TestPrograms::with('application')
            ->with('application.crops.name')
            ->with('application.crops.type')
            ->with('application.organization');
        if ($user->branch_id == User::BRANCH_STATE ) {
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
        if ($sort_by == 'organization') {
            $apps->join('applications', 'test_programs.app_id', '=', 'applications.id')
                ->join('organization_companies', 'applications.organization_id', '=', 'organization_companies.id')
                ->orderBy('organization_companies.name', $sort_order);
        } elseif ($sort_by == 'party_number') {
            $apps->join('applications', 'test_programs.app_id', '=', 'applications.id')
                ->join('crop_data', 'applications.crop_data_id', '=', 'crop_data.id')
                ->orderBy('crop_data.party_number', $sort_order);
        }elseif ($sort_by == 'date') {
            $apps->join('applications', 'test_programs.app_id', '=', 'applications.id')
                ->orderBy('applications.date', $sort_order);
        }
        else{
            $apps->orderBy($sort_by, $sort_order);
        }
        $tests = $apps->paginate(50)
            ->appends(['s' => $request->input('s')])
            ->appends(['till' => $request->input('till')])
            ->appends(['from' => $request->input('from')])
            ->appends(['city' => $request->input('city')])
            ->appends(['crop' => $request->input('crop')])
            ->appends(['sort_by' => $sort_by, 'sort_order' => $sort_order]);
        return view('dalolatnoma.search', compact('tests','from','till','city','crop','sort_by','sort_order'));
    }
    //index
    public function add($id)
    {
        $test = TestPrograms::find($id);
        $selection = CropsSelection::get();
        $tara = optional(optional($test->application)->prepared)->tara;

        return view('dalolatnoma.add', compact('test', 'selection','tara'));
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
        $tara = $request->input('tara');

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
        $test->tara = $tara;
        $test->save();

        $balls = [];
        for ($i = 0; $i < count($kod_toy); $i++) {
            if ($kod_toy[$i][0] && $kod_toy[$i][1] && $kod_toy[$i][2] && $kod_toy[$i][3]) {
                $balls[] = [
                    'dalolatnoma_id' => $test->id,
                    'from_number' => $kod_toy[$i][0],
                    'to_number' => $kod_toy[$i][1],
                    'from_toy' => $kod_toy[$i][2],
                    'to_toy' => $kod_toy[$i][3],
                ];
            }
        }
        DB::transaction(function () use ($balls) {
            GinBalles::insert($balls);
        });
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
        $selection = CropsSelection::get();

        return view('dalolatnoma.edit', compact('test', 'result', 'certificate', 'gin_balles','selection'));
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
        $result->tara = $request->input('tara');
        $result->save();
        $akt_amount = AktAmount::where('dalolatnoma_id', $id)->sum('amount');

        if ($akt_amount = 0) {
        foreach ($kod_toy as $item) {
            $conditions = ['id' => $item[0]];
            $data = [
                'dalolatnoma_id' => $id,
                'from_number' => $item[1],
                'to_number' => $item[2],
                'from_toy' => $item[3],
                'to_toy' => $item[4],
            ];


        }
            dd($data);
            GinBalles::updateOrCreate($conditions, $data);
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
    }

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
        return view('dalolatnoma.show', [
            'result' => $tests,
            'date' => $my_date
        ]);
    }
    public function myadd()
    {
        $amounts = [];

            for ($j =70575 ; $j <=70634  ; $j++) {
                $amounts[] = [
                    'dalolatnoma_id' => 415,
                    'shtrix_kod' => $j,
                ];
            }

        DB::transaction(function () use ($amounts) {
            AktAmount::insert($amounts);
        });
    }
    public function tara_edit($id)
    {
        $userA = Auth::user();
        $result = Dalolatnoma::find($id);

        return view('dalolatnoma.tara_edit', compact('result'));
    }


    // application update

    public function tara_store($id, Request $request)
    {
        $userA = Auth::user();
        $result = Dalolatnoma::find($id);
        $result->tara = $request->input('tara');
        $result->save();

        return redirect('/akt_amount/search')->with('message', 'Successfully Updated');

    }
}
