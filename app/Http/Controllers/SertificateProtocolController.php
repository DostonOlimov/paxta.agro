<?php

namespace App\Http\Controllers;

use App\Filters\V1\DalolatnomaFilter;
use App\Models\Application;
use App\Models\ClampData;
use App\Models\Dalolatnoma;
use App\Models\FinalResult;
use App\Models\LaboratoryFinalResults;
use App\Models\LaboratoryOperator;
use App\Models\LaboratoryResult;
use App\Models\SifatSertificates;
use App\Models\Tips;
use App\Services\SearchService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SertificateProtocolController extends Controller
{
    //search
    public function list(Request $request, DalolatnomaFilter $filter,SearchService $service)
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
                'sertificate_protocol.list',
                [],
                false
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
        $user = Auth::user();

        // Fetch the Dalolatnoma with required relationships
        $dalolatnoma = Dalolatnoma::with([
            'test_program.application.decision.laboratory.city',
            'gin_balles',
            'clamp_data'
        ])->findOrFail($id);

        // Fetch laboratory operators based on laboratory ID
        $laboratoryId = $dalolatnoma->test_program->application->decision->laboratory_id ?? null;
        $operators = $laboratoryId
            ? LaboratoryOperator::where('laboratory_id', $laboratoryId)->get()
            : collect();

        // Fetch ClampData with Klassiyor relationship
        $clampData = ClampData::with('klassiyor')->where('dalolatnoma_id', $id)->first();

        // Redirect if Klassiyor is missing
        if ($clampData && !$clampData->klassiyor) {
            return redirect('/sertificate-protocol/list')->with(
                'message',
                "{$clampData->classer_id} kodli Klassiyor topilmadi"
            );
        }

        // Return the view with necessary data
        return view('sertificate_protocol.add', compact('dalolatnoma', 'clampData', 'operators'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Application::class);

        $user = Auth::user();
        $data = $request->all();

        // Set klassiyor_id to user ID if not provided
        $data['klassiyor_id'] = $data['klassiyor_id'] ?? $user->id;

        // Fetch average clamp data
        $clampData = ClampData::selectRaw('
        AVG(mic) as mic,
        AVG(staple) as staple,
        AVG(strength) as strength,
        AVG(uniform) as uniform,
        AVG(fiblength) as fiblength
    ')
            ->where('dalolatnoma_id', $data['dalolatnoma_id'])
            ->first();

        // Calculate fiber length
        $fiberLength = round($clampData->fiblength / 100, 2);

        // Find appropriate tip based on fiber length
        $tip = Tips::where('max', '>=', $fiberLength)
            ->where('min', '<=', $fiberLength)
            ->first();

        // Store Laboratory Result
        $this->storeLaboratoryResult($data['dalolatnoma_id'], $clampData, $tip, 5.1);

        // Check if FinalResult exists, otherwise create it
        if (!FinalResult::where('dalolatnoma_id', $data['dalolatnoma_id'])->exists()) {
            $this->storeFinalResults($data['dalolatnoma_id']);
        }

        // Parse and format date
        $data['date'] = Carbon::createFromFormat('d-m-Y', $request->input('date'))->format('Y-m-d');

        // Create Laboratory Final Results
        LaboratoryFinalResults::create($data);

        return redirect('/sertificate-protocol/list')->with('message', 'Successfully Submitted');
    }

    public function refresh($id)
    {
        $this->authorize('create', Application::class);

        $dalolatnoma = Dalolatnoma::findOrFail($id);

        if ($dalolatnoma->laboratory_final_results) {
            if (!$dalolatnoma->laboratory_result) {
                // Fetch average clamp data
                $clampData = ClampData::selectRaw('
                AVG(mic) as mic,
                AVG(staple) as staple,
                AVG(strength) as strength,
                AVG(uniform) as uniform,
                AVG(fiblength) as fiblength
            ')
                    ->where('dalolatnoma_id', $id)
                    ->first();

                // Calculate fiber length
                $fiberLength = round($clampData->fiblength / 100, 2);

                // Find appropriate tip based on fiber length
                $tip = Tips::where('max', '>=', $fiberLength)
                    ->where('min', '<=', $fiberLength)
                    ->first();

                // Store Laboratory Result
                $this->storeLaboratoryResult($id, $clampData, $tip, 5.1);
            }

            // Update or create Final Results
            if (!FinalResult::where('dalolatnoma_id', $id)->exists()) {
                $this->storeFinalResults($id);
            } else {
                $this->updateFinalResults($id);
            }
        }

        return redirect('/sertificate-protocol/list')->with('message', 'Successfully Submitted');
    }

    public function view($id)
    {
        $test = $this->fetchDalolatnoma($id);
        $formattedDate = $this->formatDates($test->laboratory_final_results->date);
        $formattedDate2 = $this->formatDates($test->date);


        $final_results = FinalResult::with('dalolatnoma.laboratory_result')->where('dalolatnoma_id', $id)->get();

        $qrCode = $test->laboratory_final_results->status == 1
            ? $this->generateQrCode(route('lab.view', $id))
            : null;
        $t =1;

        return view('sertificate_protocol.view', compact('test','t', 'final_results', 'formattedDate', 'formattedDate2', 'qrCode'));
    }

    public function sertificateView ($id)
    {
        $dalolatnoma = $this->fetchDalolatnoma($id);
        $application = $dalolatnoma->test_program->application;

        $formattedDate = $this->formatDates($dalolatnoma->laboratory_result->date);

        // Generate QR code
        $url = route('sertificate_protocol.sertificate_view', $id);
        $qrCode = QrCode::size(100)->generate($url);
        $t = 1;

        $final_results = FinalResult::with('dalolatnoma.laboratory_result')->where('dalolatnoma_id', $id)->get();

        return view('sertificate_protocol.sertificate_view', compact('application', 'dalolatnoma','qrCode','final_results','formattedDate','t'));
    }

    function change_status($id)
    {

        $test = Dalolatnoma::findOrFail($id);
        $lab = LaboratoryFinalResults::where('dalolatnoma_id',$id)->first();
        $lab->status = 1;
        $lab->save();

        $final_results = FinalResult::with('dalolatnoma.laboratory_result')->where('dalolatnoma_id', $id)->get();

        // date format
        $formattedDate = formatUzbekDateInLatin($test->laboratory_final_results->date);
        $formattedDate2 = formatUzbekDateInLatin($test->date);

        // Generate QR code
        $qrCode = base64_encode(QrCode::format('png')->size(100)->generate(route('laboratory_protocol.download', $id)));

        // Load the view and pass data to it
        $pdf = Pdf::loadView('sertificate_protocol.protocol_pdf', compact('test','formattedDate','formattedDate2','qrCode','final_results'));

//        return $pdf->stream('sdf.pdf');
        // Save the PDF file
        $filePath = storage_path('app/public/protocols/protocol_' . $id . '.pdf');
        $pdf->save($filePath);

        // Redirect to list page with success message
        return redirect('/sertificate-protocol/list?generatedAppId='. $id)
            ->with('message', 'Protocol saved!');
    }

    //accept online applications
    public function accept($id)
    {
        $dalolatnoma = $this->fetchDalolatnoma($id);
        $application = $dalolatnoma->test_program->application;

        $final_results = FinalResult::with('dalolatnoma.laboratory_result')->where('dalolatnoma_id', $id)->get();


        // Generate certificate number
        $currentYear = date('Y');
        $number = SifatSertificates::where('year', $currentYear)
                ->where('type', SifatSertificates::PAXTA_TYPE)
                ->max('number') ?? 0;
        $number++;

        // Create certificate if not exists
        if (!$application->sifat_sertificate) {
            SifatSertificates::create([
                'app_id' => $application->id,
                'number' => $number,
                'zavod_id' => $application->prepared_id,
                'year' => $currentYear,
                'type' => SifatSertificates::PAXTA_TYPE,
                'created_by' => auth()->id(),
            ]);
        }

        $sertNumber = ($currentYear - 2000) * 1000000 + $number;
        $qrCode = $this->generateQrCode(route('sifat_sertificate.download', $id));
        $formattedDate = $this->formatDates($dalolatnoma->laboratory_final_results->date);

        $pdf = Pdf::loadView('sertificate_protocol.sertificate_pdf', compact('application','final_results', 'sertNumber', 'formattedDate', 'qrCode'));
        $pdf->save(storage_path("app/public/sifat_sertificates/certificate_{$id}.pdf"));

        return redirect()->route('sertificate_protocol.list', ['generatedAppId' => $id])
            ->with('message', 'Certificate saved!');
    }

    public function download($id)
    {
        $filePath = storage_path('app/public/protocols/protocol_' . $id . '.pdf');

        if (file_exists($filePath)) {
            return response()->download($filePath);
        } else {
            return redirect()->back()->with('error', 'File not found.');
        }
    }

    // Reusable method for fetching dalolatnoma with relationships
    private function fetchDalolatnoma($id)
    {
        return Dalolatnoma::with([
            'measurement_mistake',
            'laboratory_result',
            'result',
            'selection',
            'laboratory_final_results.operator',
            'laboratory_final_results.klassiyor',
            'laboratory_final_results.director',
            'test_program.application.decision.laboratory.city.region',
            'test_program.application.crops.name',
            'test_program.application.organization.city',
            'test_program.application.prepared.region',
        ])->findOrFail($id);
    }

    // Reusable QR code generation
    private function generateQrCode($route)
    {
        return base64_encode(QrCode::format('png')->size(100)->generate($route));
    }

    // Format date
    private function formatDates($date)
    {
        return $date ? formatUzbekDateInLatin($date) : null;
    }

    /**
     * Store LaboratoryResult data.
     */
    private function storeLaboratoryResult($dalolatnomaId, $clampData, $tip, $humidityResult)
    {
        LaboratoryResult::updateOrCreate(
            ['dalolatnoma_id' => $dalolatnomaId],
            [
                'tip_id' => optional($tip)->id ?? 11,
                'mic' => $clampData->mic,
                'staple' => $clampData->staple,
                'strength' => $clampData->strength,
                'uniform' => $clampData->uniform,
                'fiblength' => $clampData->fiblength,
                'humidity' => $humidityResult,
            ]
        );
    }

    /**
     * Store FinalResults if they don't exist.
     */
    private function storeFinalResults($dalolatnomaId)
    {
        $results = $this->getAggregatedClampData($dalolatnomaId);

        foreach ($results as $result) {
            FinalResult::create([
                'dalolatnoma_id' => $dalolatnomaId,
                'test_program_id' => $dalolatnomaId,
                'sort' => $result->sort,
                'class' => $result->class,
                'count' => $result->count,
                'amount' => $result->total_amount,
                'mic' => $result->mic,
                'staple' => $result->staple,
                'strength' => $result->strength,
                'uniform' => $result->uniform,
                'humidity' => 5,
            ]);
        }
    }

    /**
     * Update FinalResults.
     */
    private function updateFinalResults($dalolatnomaId)
    {
        $results = $this->getAggregatedClampData($dalolatnomaId);

        foreach ($results as $result) {
            FinalResult::updateOrCreate(
                [
                    'dalolatnoma_id' => $dalolatnomaId,
                    'sort' => $result->sort,
                    'class' => $result->class,
                ],
                [
                    'count' => $result->count,
                    'amount' => $result->total_amount,
                    'mic' => $result->mic,
                    'staple' => $result->staple,
                    'strength' => $result->strength,
                    'uniform' => $result->uniform,
                    'humidity' => 5,
                ]
            );
        }
    }

    /**
     * Get aggregated clamp data.
     */
    private function getAggregatedClampData($dalolatnomaId)
    {
        return ClampData::selectRaw('
        sort, class,
        COUNT(*) as count,
        SUM(akt_amount.amount) as total_amount,
        AVG(mic) as mic,
        AVG(staple) as staple,
        AVG(strength) as strength,
        AVG(uniform) as uniform,
        AVG(humidity) as humidity
    ')
            ->join('akt_amount', function ($join) use ($dalolatnomaId) {
                $join->on('akt_amount.shtrix_kod', '=', 'clamp_data.gin_bale')
                    ->on('akt_amount.dalolatnoma_id', '=', 'clamp_data.dalolatnoma_id');
            })
            ->where('clamp_data.dalolatnoma_id', $dalolatnomaId)
            ->groupBy('sort', 'class')
            ->get();
    }

}
