<?php

namespace App\Http\Controllers;

use App\Filters\V1\DalolatnomaFilter;
use App\Models\AktAmount;
use App\Models\Dalolatnoma;
use App\Services\SearchService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class AktAmountController extends Controller
{
    //search
    public function search(Request $request, DalolatnomaFilter $filter,SearchService $service): View|Factory|JsonResponse|Application
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
                    'test_program.application.crops',
                    'test_program.application.crops.name',
                    'test_program.application.decision',
                    'test_program.application.organization',
                    'test_program.application.organization.area.region',
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
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Edit
    public function edit(Dalolatnoma $dalolatnoma): Factory|View|Application
    {
        $tests = $dalolatnoma->akt_amount()->get()->toArray();
        $balls = $dalolatnoma->gin_balles()->get();

        if (empty($tests)) {
            $amounts = $this->generateAmounts($dalolatnoma->id, $balls);
            DB::transaction(function () use ($amounts) {
                AktAmount::insert($amounts);
            });

            $tests =  $dalolatnoma->akt_amount()->get()->toArray();
        }

        $this->populateCreatedAt($tests, $balls);

        $data1 = array_chunk($tests, 50);

        $id = $dalolatnoma->id;

        return view('akt_amount.edit', compact('data1', 'id', 'balls'));
    }

    // Save amount
    public function save_amount(Request $request): JsonResponse
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
    public function view(Dalolatnoma $dalolatnoma): Factory|View|Application
    {
        $results = $dalolatnoma->akt_amount()->get()->toArray();
        $ginBalles = $dalolatnoma->gin_balles()->get();

        $this->populateCreatedAt($results, $ginBalles);
        foreach ($results as &$result) {
            $result['order_number'] = $result['created_at'];
        }
        unset($result);

        $sum_amount = $dalolatnoma->akt_amount()->sum('amount');
        $count = $dalolatnoma->akt_amount()->count();
        $tara = $dalolatnoma->tara;
        $data1 = !empty($results) ? array_chunk($results, 50) : [];
        $id = $dalolatnoma->id;

        return view('akt_amount.show', compact('data1', 'id', 'sum_amount', 'count', 'tara'));
    }

    // Excel export
    public function excel(Dalolatnoma $dalolatnoma): Factory|View|Application
    {
        $tests = $dalolatnoma->akt_amount()->get()->toArray();
        $balls = $dalolatnoma->gin_balles()->get();

        if (empty($tests)) {
            $amounts = $this->generateAmounts($dalolatnoma->id, $balls);
            DB::transaction(function () use ($amounts) {
                AktAmount::insert($amounts);
            });

            $tests = $dalolatnoma->akt_amount()->get()->toArray();
        }

        $this->populateCreatedAt($tests, $balls);

        $data1 = array_chunk($tests, 50);

        $id = $dalolatnoma->id;

        return view('akt_amount.excel', compact('data1', 'id', 'balls'));
    }

    public function store(Request $request): Redirector|Application|RedirectResponse
    {

        $dal = Dalolatnoma::findOrFail($request->input('id'));

        // Amounts come as one JSON field {akt_amount_id: amount} so big acts don't exceed max_input_vars
        $amounts = json_decode((string) $request->input('amounts_json'), true);

        if (empty($amounts) || !is_array($amounts)) {
            return redirect('/akt_amount/search')->with('error', 'No amounts provided.');
        }

        DB::transaction(function () use ($dal, $amounts) {
            foreach ($dal->akt_amount as $akt) {
                if (!isset($amounts[$akt->id])) {
                    continue;
                }

                $amount = (double) str_replace(',', '.', $amounts[$akt->id]);

                if ($amount > 0 && $amount < 1000 && (double) $akt->amount !== $amount) {
                    $akt->update(['amount' => $amount]);
                }
            }
        });

        return redirect('/akt_amount/search')->with('message', 'Successfully saved');
    }

    // Helper methods
    private function generateAmounts($dalolatnomaId, $balls): array
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

    // Sets each record's toy number (stored in 'created_at' for the views) from its shtrix_kod and
    // sorts by it. Query order can't be trusted: the (dalolatnoma_id, amount) index makes MySQL return rows sorted by amount.
    private function populateCreatedAt(&$tests, $balls): void
    {
        foreach ($tests as &$test) {
            $test['created_at'] = self::toyNumber($test['shtrix_kod'], $balls);
        }
        unset($test);

        usort($tests, fn($a, $b) => [$a['created_at'] === null, $a['created_at'], $a['shtrix_kod']]
            <=> [$b['created_at'] === null, $b['created_at'], $b['shtrix_kod']]);
    }

    public static function toyNumber($shtrixKod, $balls): ?int
    {
        $ball = $balls->first(fn($b) => $shtrixKod >= $b->from_number && $shtrixKod <= $b->to_number);

        return $ball ? $ball->from_toy + ($shtrixKod - $ball->from_number) : null;
    }

}
