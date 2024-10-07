<?php

namespace App\Http\Controllers;

use App\Filters\V1\DalolatnomaFilter;
use App\Models\AktAmount;
use App\Models\Dalolatnoma;
use App\Models\GinBalles;
use App\Models\TestPrograms;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AktAmountController extends Controller
{
    //search
    public function search(Request $request, DalolatnomaFilter $filter,SearchService $service)
    {
        try {
            $names = getCropsNames();
            $states = getRegions();
            $years = getCropYears();

            return $service->search(
                $request,
                $filter,
                Dalolatnoma::class,
                [
                    'test_program',
                    'test_program.application',
                    'test_program.application.decision',
                    'test_program.application.organization',
                    'test_program.application.prepared',
                ],
                compact('names', 'states', 'years'),
                'akt_amount.search',
                [],
                false,
                'akt_amount', // Related model for withSum
                'amount'      // Column to sum
            );

        } catch (\Throwable $e) {
            // Log the error for debugging
            \Log::error($e);
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Add
    public function add($id)
    {
        $test = TestPrograms::findOrFail($id);
        return view('akt_amount.add', compact('test'));
    }

    // Edit
    public function edit($id)
    {
        $tests = AktAmount::where('dalolatnoma_id', $id)->get()->toArray();
        $balls = GinBalles::where('dalolatnoma_id', $id)->get();

        if (empty($tests)) {
            $amounts = $this->generateAmounts($id, $balls);
            DB::transaction(function () use ($amounts) {
                AktAmount::insert($amounts);
            });

            $tests = AktAmount::where('dalolatnoma_id', $id)->get()->toArray();
        }

        $this->populateCreatedAt($tests, $balls);

        $data1 = array_chunk($tests, 50);

        return view('akt_amount.edit', compact('data1', 'id', 'balls'));
    }

    // Save amount
    public function save_amount(Request $request)
    {
        $id = $request->input('id');
        $amount = (double) $request->input('amount');

        $result = AktAmount::findOrFail($id);

        if ($amount > 0 && $amount < 1000) {
            $result->amount = $amount;
            $result->save();
        }

        return response()->json(['message' => 'Answer saved successfully']);
    }

    // View
    public function view($id)
    {
        $tests = AktAmount::where('dalolatnoma_id', $id)->get()->toArray();
        $sum_amount = AktAmount::where('dalolatnoma_id', $id)->sum('amount');
        $count = AktAmount::where('dalolatnoma_id', $id)->count();
        $tara = optional(Dalolatnoma::find($id))->tara;

        $data1 = !empty($tests) ? array_chunk($tests, 50) : [];

        return view('akt_amount.show', compact('data1', 'id', 'sum_amount', 'count', 'tara'));
    }

    // Helper methods
    private function generateAmounts($dalolatnomaId, $balls)
    {
        $amounts = [];
        foreach ($balls as $ball) {
            for ($j = $ball->from_number; $j <= $ball->to_number; $j++) {
                $amounts[] = [
                    'dalolatnoma_id' => $dalolatnomaId,
                    'shtrix_kod' => $j,
                ];
            }
        }
        return $amounts;
    }

    private function populateCreatedAt(&$tests, $balls)
    {
        $i = 0;
        foreach ($balls as $ball) {
            for ($j = $ball->from_toy; $j <= $ball->to_toy; $j++) {
                $tests[$i]['created_at'] = $j;
                $i++;
            }
        }
    }

    // Edit
    public function excel($id)
    {
        $tests = AktAmount::where('dalolatnoma_id', $id)->get()->toArray();
        $balls = GinBalles::where('dalolatnoma_id', $id)->get();

        if (empty($tests)) {
            $amounts = $this->generateAmounts($id, $balls);
            DB::transaction(function () use ($amounts) {
                AktAmount::insert($amounts);
            });

            $tests = AktAmount::where('dalolatnoma_id', $id)->get()->toArray();
        }

        $this->populateCreatedAt($tests, $balls);

        $data1 = array_chunk($tests, 50);

        return view('akt_amount.excel', compact('data1', 'id', 'balls'));
    }

    public function store(Request $request)
    {

        $id = $request->input('id');
        $dal = Dalolatnoma::findOrFail($id);

        // Sanitize and filter only relevant inputs (those that start with 'amount')
        $amounts = $request->only(array_filter($request->keys(), fn($key) => str_starts_with($key, 'amount')));

        // Early return if no amount data is found
        if (empty($amounts)) {
            return redirect('/akt_amount/search')->with('error', 'No amounts provided.');
        }


        foreach ($dal->akt_amount as $index => $akt) {
            // Match amount keys dynamically (amount1, amount2, etc.)
            $amountKey = 'amount' . ($index + 1);

            if (isset($amounts[$amountKey])) {
                $akt->update(['amount' => $amounts[$amountKey]]);
            }
        }

        return redirect('/akt_amount/search')->with('message', 'Successfully saved');
    }

}
