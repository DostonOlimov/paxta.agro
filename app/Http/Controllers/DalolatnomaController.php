<?php

namespace App\Http\Controllers;

use App\Filters\V1\ApplicationFilter;
use App\Models\AktAmount;
use App\Models\Application;
use App\Models\CropsName;
use App\Models\CropsSelection;
use App\Models\Dalolatnoma;
use App\Models\GinBalles;
use App\Models\Humidity;
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
    public function search(Request $request, ApplicationFilter $filter, SearchService $service)
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
                    'tests.dalolatnoma',
                    'organization',
                    'prepared',
                    'crops.name',
                    'organization.area.region'
                ],
                compact('names', 'states', 'years', 'all_status'),
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
        $test = TestPrograms::findOrFail($id);
        $selection = CropsSelection::get();
        $tara = optional(optional($test->application)->prepared)->tara;

        switch ($test->application->app_type) {
            case CropsName::CROP_TYPE_2:
                return view('dalolatnoma.chigit.add', compact('test', 'selection', 'tara'));
            case CropsName::CROP_TYPE_5:
                return view('dalolatnoma.conclusion.add', compact('test', 'selection', 'tara'));
            default:
                return view('dalolatnoma.add', compact('test', 'selection', 'tara'));
        }
    }
    public function store(Request $request)
    {
        $kod_toy = $request->input('kod_toy');

        $request->validate([
            'kod_toy.*.0' => 'required|numeric',
            'kod_toy.*.1' => ['required', 'numeric', new DifferentsShtrixKod(), new EqualToyCount()],
            'toy_count' => ['required', 'numeric', new EqualToyCount()],
            'kod_toy.*.3' => ['required', 'numeric', new EqualToyCount()],
        ]);

        $this->authorize('create', Application::class);

        $data = $request->only([
            'test_id',
            'number',
            'selection_code',
            'toy_count',
            'amount',
            'party_number',
            'nav',
            'sinf',
            'tara',
            'date'
        ]);

        $date = $this->formatDate($data['date']);

        DB::transaction(function () use ($data, $date, $kod_toy) {
            // Create Dalolatnoma
            $dal = Dalolatnoma::create([
                'test_program_id' => $data['test_id'],
                'number' => $data['number'],
                'date' => $date,
                'selection_code' => isset($data['selection_code']) ? $data['selection_code'] : 99,
                'toy_count' => $data['toy_count'],
                'amount' => $data['amount'],
                'party' => $data['party_number'] ?? '',
                'nav' => $data['nav'] ?? 1,
                'sinf' => $data['sinf'] ?? 1,
                'tara' => $data['tara'] ?? 1,
            ]);

            if (getApplicationType() == CropsName::CROP_TYPE_1) {
                // Create Humidity
                Humidity::create([
                    'dalolatnoma_id' => $dal->id,
                    'number' => $data['number'],
                    'date' => $date,
                    'selection_code' => $data['selection_code'],
                    'toy_count' => $data['toy_count'],
                    'toy_amount' => ceil($data['toy_count'] / 10),
                    'party_number' => $data['party_number'],
                    'nav' => $data['nav'],
                    'sinf' => $data['sinf'],
                ]);
            }

            // Process GinBalles and AktAmount
            $balls = $this->prepareGinBalles($dal->id, $kod_toy);
            $amounts = $this->prepareAktAmount($dal->id, $kod_toy);

            // Insert GinBalles and AktAmount in batches
            GinBalles::insert($balls);
            AktAmount::insert($amounts);

            // Log user activity
            $this->logActivity($dal->id, Auth::user()->id);
        });

        return redirect('/akt_amount/search');
    }


    //update
    public function edit($id)
    {
        $result = Dalolatnoma::find($id);
        $test = TestPrograms::find($result->test_program_id);
        $certificate =  Sertificate::where('final_result_id', '=', $result->id)->first();
        $gin_balles = GinBalles::where('dalolatnoma_id', $id)->get();
        $selection = CropsSelection::get();

        switch ($test->application->app_type) {
            case CropsName::CROP_TYPE_2:
                return view('dalolatnoma.chigit.edit', compact('test', 'result', 'certificate', 'gin_balles', 'selection'));
            case CropsName::CROP_TYPE_5:
                return view('dalolatnoma.conclusion.edit', compact('test', 'result', 'certificate', 'gin_balles', 'selection'));
            default:
                return view('dalolatnoma.edit', compact('test', 'result', 'certificate', 'gin_balles', 'selection'));
        }
    }

    public function update2($id, Request $request)
    {
        $dalolatnoma = Dalolatnoma::findOrFail($id);

        $dalolatnoma->number = $request->input('number');

        if ($dalolatnoma->date != $request->input('date')) {
            $dalolatnoma->date = $this->formatDate($request->input('date'));
        }

        $dalolatnoma->selection_code = $request->input('selection_code') ?? 99;
        $dalolatnoma->toy_count = $request->input('toy_count');
        $dalolatnoma->amount = $request->input('amount');
        $dalolatnoma->party = $request->input('party_number') ?? '';
        // $dalolatnoma->amount2 = $request->input('amount2');
        $dalolatnoma->save();


        return redirect('/dalolatnoma/search')->with('message', 'Successfully Created');
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

        $user = Auth::user();
        $dalolatnoma = Dalolatnoma::findOrFail($id);

        // Update Dalolatnoma fields if needed
        $this->updateDalolatnoma($dalolatnoma, $request);

        // Check if AktAmount needs to be updated
        $aktAmount = AktAmount::where('dalolatnoma_id', $id)->sum('amount');

        if ($aktAmount == 0) {
            // Handle GinBalles update
            $this->updateGinBalles($id, $kod_toy);

            // Delete existing AktAmount and insert new amounts
            AktAmount::where('dalolatnoma_id', $id)->delete();
            $amounts = $this->prepareAktAmount($id, $kod_toy, true);

            DB::transaction(function () use ($amounts) {
                AktAmount::insert($amounts);
            });
        }

        // Log the user activity
        $this->logActivity($dalolatnoma->id, $user->id);

        return redirect('/dalolatnoma/search')->with('message', 'Successfully Updated');
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

        $my_date = $date->isoFormat("D") . ' - ' . $uzbekMonthNames[$date->isoFormat("MM")] . ' ' . $date->isoFormat("Y");

         switch ($tests->test_program->application->app_type) {
            case CropsName::CROP_TYPE_2:
                return view('dalolatnoma.chigit.show', ['result'=>$tests, 'date'=>$my_date]);
            case CropsName::CROP_TYPE_5:
                return view('dalolatnoma.conclusion.show', ['result'=>$tests, 'date'=>$my_date]);
            default:
                return view('dalolatnoma.show', ['result'=>$tests, 'date'=>$my_date]);
        }
    }
    public function myadd()
    {
        $amounts = [];

        for ($j = 70575; $j <= 70634; $j++) {
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

    private function formatDate(string $date): string
    {
        return join('-', array_reverse(explode('-', $date)));
    }

    private function prepareGinBalles(int $dalolatnomaId, array $kod_toy): array
    {
        $balls = [];
        foreach ($kod_toy as $toy) {
            if ($toy[0] && $toy[1] && $toy[2] && $toy[3]) {
                $balls[] = [
                    'dalolatnoma_id' => $dalolatnomaId,
                    'from_number' => $toy[0],
                    'to_number' => $toy[1],
                    'from_toy' => $toy[2],
                    'to_toy' => $toy[3],
                ];
            }
        }
        return $balls;
    }

    private function prepareAktAmount(int $dalolatnomaId, array $kod_toy, bool $updated = false): array
    {
        $amounts = [];
        foreach ($kod_toy as $toy) {
            if ($updated) {
                for ($j = $toy[1]; $j <= $toy[2]; $j++) {
                    $amounts[] = [
                        'dalolatnoma_id' => $dalolatnomaId,
                        'shtrix_kod' => $j,
                    ];
                }
            } else {
                for ($j = $toy[0]; $j <= $toy[1]; $j++) {
                    $amounts[] = [
                        'dalolatnoma_id' => $dalolatnomaId,
                        'shtrix_kod' => $j,
                    ];
                }
            }
        }
        return $amounts;
    }

    private function updateDalolatnoma(Dalolatnoma $dalolatnoma, Request $request)
    {
        $dalolatnoma->number = $request->input('number');

        if ($dalolatnoma->date != $request->input('date')) {
            $dalolatnoma->date = $this->formatDate($request->input('date'));
        }

        $dalolatnoma->selection_code = $request->input('selection_code');
        $dalolatnoma->toy_count = $request->input('toy_count');
        $dalolatnoma->amount = $request->input('amount');
        $dalolatnoma->party = $request->input('party_number');
        $dalolatnoma->nav = $request->input('nav');
        $dalolatnoma->sinf = $request->input('sinf');
        $dalolatnoma->tara = $request->input('tara');
        $dalolatnoma->save();
    }

    private function updateGinBalles(int $dalolatnomaId, array $kod_toy)
    {
        foreach ($kod_toy as $item) {
            $conditions = ['id' => $item[0]];
            $data = [
                'dalolatnoma_id' => $dalolatnomaId,
                'from_number' => $item[1],
                'to_number' => $item[2],
                'from_toy' => $item[3],
                'to_toy' => $item[4],
            ];

            GinBalles::updateOrCreate($conditions, $data);
        }
    }


    private function logActivity(int $dalolatnomaId, int $userId): void
    {
        tbl_activities::create([
            'ip_adress' => $_SERVER['REMOTE_ADDR'],
            'user_id' => $userId,
            'action_id' => $dalolatnomaId,
            'action_type' => 'new_dalolatnoma',
            'action' => "Dalolatnoma qo'shildi",
            'time' => now(),
        ]);
    }
}
