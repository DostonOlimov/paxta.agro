<?php

namespace App\Http\Controllers;

use App\Filters\V1\ApplicationFilter;
use App\Models\AktAmount;
use App\Models\Application;
use App\Models\CropData;
use App\Models\CropsName;
use App\Models\CropsSelection;
use App\Models\Decision;
use App\Models\Dalolatnoma;
use App\Models\GinBalles;
use App\Models\OrganizationCompanies;
use App\Models\Region;
use App\Models\Sertificate;
use App\Models\TestPrograms;
use App\Models\DefaultModels\tbl_activities;
use App\Rules\DifferentsShtrixKod;
use App\Rules\EqualToyCount;
use App\Services\SearchService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class DalolatnomaController extends Controller
{
    public function search(Request $request, ApplicationFilter $filter,SearchService $service)
    {
        try {
            $names = getCropsNames();
            $states = getRegions();
            $years = getCropYears();
            $all_status = getAppStatus();

            return $service->search(
                $request,
                $filter,
                Application::class,
                [
                    'crops',
                    'organization',
                    'prepared'
                ],
                compact('names', 'states', 'years','all_status'),
                'dalolatnoma.search',
                [Application::STATUS_ACCEPTED, Application::STATUS_FINISHED],
                true
            );

        } catch (\Throwable $e) {
            // Log the error for debugging
            \Log::error($e);
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    //add
    public function add($id)
    {
        $test = TestPrograms::find($id);
        $selection = CropsSelection::get();
        $tara = optional(optional($test->application)->prepared)->tara;

        return view('dalolatnoma.add', compact('test', 'selection','tara'));
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
