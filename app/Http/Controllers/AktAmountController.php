<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\DalolatnomaTrait;
use App\Models\AktAmount;
use App\Models\Dalolatnoma;
use App\Models\GinBalles;
use App\Models\TestPrograms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AktAmountController extends Controller
{
    use DalolatnomaTrait;

    // Search
    public function search(Request $request)
    {
        $city = $request->input('city');
        $crop = $request->input('crop');
        $from = $request->input('from');
        $till = $request->input('till');
        $sort_by = $request->get('sort_by', 'id');
        $sort_order = $request->get('sort_order', 'desc');

        $apps = $this->buildQuery($request);

        $tests = $apps->withSum('akt_amount', 'amount')
            ->paginate(50)
            ->appends($request->except('page'));

        return view('akt_amount.search', compact('tests', 'from', 'till', 'city', 'crop', 'sort_by', 'sort_order'));
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
}
