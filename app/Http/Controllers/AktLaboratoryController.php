<?php

namespace App\Http\Controllers;

use App\Filters\V1\DalolatnomaFilter;
use App\Jobs\InsideQueueJob;
use App\Models\AktAmount;
use App\Models\ClampData;
use App\Models\Dalolatnoma;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AktLaboratoryController extends Controller
{

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
                'akt_laboratory.search',
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

    // Show the 'add' view with Dalolatnoma details
    public function add($id)
    {
        $test = Dalolatnoma::with('gin_balles')->find($id);
        return view('akt_laboratory.add', compact('test'));
    }

    // Store the uploaded file and dispatch a job to process it
    public function store(Request $request)
    {
        $id = $request->input('id');
        $user = Auth::user();
        $stateId = $user->state_id;

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Store the file in the 'uploads/{state_id}' directory with the original name
            $filePath = $file->storeAs('uploads/' . $stateId, $file->getClientOriginalName());

            // Dispatch a job to process the file
            InsideQueueJob::dispatch([
                'path' => $filePath,
                'id' => $id,
            ]);
        }

        return redirect('/akt_laboratory/search')->with('success', 'Role muvaffaqatli yaratildi.');
    }

    // Show the 'edit' view with AktAmount details
    public function edit($id)
    {
        $tests = AktAmount::where('dalolatnoma_id', $id)->get()->toArray();

        // Split the results into chunks for better display in the view
        $dataChunks = array_chunk($tests, ceil(count($tests) / 4));

        return view('akt_laboratory.edit', [
            'results' => $dataChunks,
        ]);
    }

    // Save the updated amount for a specific AktAmount record
    public function saveAmount(Request $request)
    {
        $id = $request->input('id');
        $amount = $request->input('amount');

        $result = AktAmount::find($id);
        if ($amount > 0 && $amount < 1000) {
            $result->amount = $amount;
            $result->save();
        }

        return response()->json(['message' => 'Answer saved successfully']);
    }

    // Show the 'view' page with ClampData details for a specific Dalolatnoma
    public function view($id)
    {
        $tests = ClampData::where('dalolatnoma_id', $id)->get();
        return view('akt_laboratory.show', [
            'results' => $tests,
            'id' => $id
        ]);
    }
}
