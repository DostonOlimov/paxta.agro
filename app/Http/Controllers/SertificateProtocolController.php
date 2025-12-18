<?php

namespace App\Http\Controllers;

use App\Filters\V1\DalolatnomaFilter;
use App\Models\Application;
use App\Models\CropsName;
use App\Models\Dalolatnoma;
use App\Models\FinalResult;
use App\Models\LaboratoryFinalResults;
use App\Models\LaboratoryOperator;
use App\Models\LaboratoryResult;
use App\Models\SifatSertificates;
use App\Services\SearchService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SertificateProtocolController extends Controller
{
    //search
    public function list(Request $request, DalolatnomaFilter $filter,SearchService $service): View|Factory|JsonResponse|\Illuminate\Contracts\Foundation\Application
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
                    'test_program.application.sifat_sertificate',
                    'test_program.application.crops',
                    'test_program.application.crops.name',
                    'test_program.application.decision',
                    'test_program.application.organization',
                    'test_program.application.organization.area.region',
                    'test_program.application.prepared',
                    'laboratory_final_results',


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
    public function add(Dalolatnoma $dalolatnoma)
    {
        // Fetch laboratory operators based on laboratory ID
        $laboratoryId = $dalolatnoma->test_program->application->decision->laboratory_id ?? null;
        $operators = $laboratoryId
            ? LaboratoryOperator::where('laboratory_id', $laboratoryId)->get()
            : collect();

        // Fetch ClampData with Klassiyor relationship
        $clampData = $dalolatnoma->clamp_data()->first();

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

    public function store(Dalolatnoma $dalolatnoma, Request $request)
    {
        $this->authorize('create', Application::class);

        $user = Auth::user();
        $formData = $request->all();

        // Default klassiyor_id to current user if not provided
        $formData['klassiyor_id'] = $formData['klassiyor_id'] ?? $user->id;

        // Fetch lab-related data
        $clampData = $dalolatnoma->averageClampData();
        $tip = $dalolatnoma->findTipByAverageFiberLength();

        // Save lab result
        $this->storeLaboratoryResult($dalolatnoma, $clampData, $tip);

        // Save final results if not exists
        $this->saveFinalResults($dalolatnoma);

        // Parse and set date
        $formData['dalolatnoma_id'] = $dalolatnoma->id;
        $formData['date'] = Carbon::createFromFormat('d-m-Y', $request->input('date'))->toDateString();

        LaboratoryFinalResults::create($formData);

        return redirect('/sertificate-protocol/list')->with('message', 'Successfully Submitted');
    }

    public function refresh(Dalolatnoma $dalolatnoma)
    {
        $this->authorize('create', Application::class);

        $clampData = $dalolatnoma->averageClampData();

        if($clampData?->mic){
            $tip = $dalolatnoma->findTipByAverageFiberLength();

            // Store Laboratory Result
            $this->storeLaboratoryResult($dalolatnoma, $clampData, $tip);

            // Update final result
            $this->saveFinalResults($dalolatnoma);
        }

        return redirect('/sertificate-protocol/list')->with('message', 'Successfully Submitted');
    }

    //sertificate protocol view
    public function view(Dalolatnoma $dalolatnoma)
    {
        $test = $dalolatnoma->load(
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
        );

        $formattedDate = $this->formatDates($test->laboratory_final_results->date);
        $formattedDate2 = $this->formatDates($test->date);

        $labResults = $test->result()->get()
            ->groupBy('sort')
            ->values();

        $type = $this->determineCropType($dalolatnoma);
        $t = 1;

        return view('sertificate_protocol.view', compact(
            'labResults', 'test', 'type', 'formattedDate', 'formattedDate2','t'
        ));
    }

    //saving sertificate protocol
    public function change_status(Dalolatnoma $dalolatnoma)
    {
        $test = $dalolatnoma->load(
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
        );

        $type = $this->determineCropType($dalolatnoma);
        $groupedResults = $test->result()->get()
            ->groupBy('sort')
            ->values();
        $sortCount = count($groupedResults);

        $formattedDate = $this->formatDates($test->laboratory_final_results->date);
        $formattedDate2 = $this->formatDates($test->date);

        // Generate PDFs for each group
        foreach ($groupedResults as $index => $group) {
            $qrCode = $this->generateQrCode(route('laboratory_protocol.download', [
                'dalolatnoma' => $test,
                'type' => $index,
            ]));

            $this->generatePdf($test->id, $group, $test, $type, $formattedDate, $formattedDate2, $qrCode, $index);
        }

        // Update lab status and chp count
        $test->laboratory_final_results->chp = $sortCount;
        $test->laboratory_final_results->status = 1;
        $test->laboratory_final_results->save();

        return redirect('/sertificate-protocol/list?generatedAppId=' . $test->id)
            ->with('message', 'Protocol saved!');
    }


    public function sertificateView(Dalolatnoma $dalolatnoma)
    {
        $application = $dalolatnoma->test_program->application;
        $formattedDate = $this->formatDates($dalolatnoma->laboratory_final_results->date);
        $t = 1;

        $labResults =  $dalolatnoma->result()->get()
            ->groupBy('sort')
            ->values();

        return view('sertificate_protocol.sertificate_view', compact('application', 'dalolatnoma','labResults','formattedDate','t'));
    }


    // Accept online applications
    public function accept(Dalolatnoma $dalolatnoma)
    {
        $application = $dalolatnoma->test_program->application;
        $appId = $application->id;

        // Determine certificate type based on session
        $sertType = session('crop') == CropsName::CROP_TYPE_4
            ? SifatSertificates::LINT_TYPE
            : SifatSertificates::PAXTA_TYPE;

        $groupedResults = $dalolatnoma->result()->get()
            ->groupBy('sort')
            ->values();;
        $currentYear = date('Y');

        // Fetch the starting number for certificates
        $startingNumber = SifatSertificates::where('year', $currentYear)
                ->where('type', $sertType)
                ->max('number') ?? 0;

        // Create certificates if they don't exist
        if (!$application->sifat_sertificate) {
            $this->createCertificates($appId, $groupedResults, $startingNumber, $sertType, $currentYear);
        } else {
            $startingNumber = $application->sifat_sertificate->number - 1;
        }

        // Prepare and save certificate files
        $formattedDate = $this->formatDates($dalolatnoma->laboratory_final_results->date);
        $this->generateCertificateFiles($appId, $groupedResults, $startingNumber, $currentYear, $formattedDate);

        return redirect()->route('sertificate_protocol.list', ['generatedAppId' => $appId])
            ->with('message', 'Certificate saved!');
    }

    public function download( $dalolatnoma_id, Request $request)
    {
        // Determine the file path based on the request type
        $fileName = $request->input('type') >= 1
            ? 'protocol_' . $dalolatnoma_id . '_' . $request->input('type') . '.pdf'
            : 'protocol_' . $dalolatnoma_id . '.pdf';

        $filePath = storage_path('app/public/protocols/' . $fileName);

        // Check if the file exists
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        // Download the file
        return response()->download($filePath);
    }

    /**
     * Generate a QR code for a given route.
     */
    private function generateQrCode($route)
    {
        return base64_encode(QrCode::format('png')->size(100)->generate($route));
    }

    /**
     * Format date in Uzbek Latin format.
     */
    private function formatDates($date)
    {
        return $date ? formatUzbekDateInLatin($date) : null;
    }

    /**
     * Create certificates in bulk.
     */
    protected function createCertificates($appId, $groupedResults, $startingNumber, $sertType, $currentYear)
    {

        foreach ($groupedResults as $index => $group) {
            SifatSertificates::create([
                'app_id' => $appId,
                'number' => $startingNumber + $index + 1,
                'zavod_id' => 3,
                'year' => $currentYear,
                'type' => $sertType,
                'created_by' => auth()->id(),
                'chp' => $index + 1,
            ]);
        }
    }

    /**
     * Generate and save certificate files.
     */
    protected function generateCertificateFiles($appId, $groupedResults, $startingNumber, $currentYear, $formattedDate)
    {
        $application = Application::find($appId);
        foreach ($groupedResults as $index => $group) {
            $sertNumber = (($currentYear - 2000) * 1000000) + $startingNumber + $index + 1;
            $route = route('sifat_sertificate.download', ['id' => $appId, 'type' => $index]);
            $qrCode = $this->generateQrCode($route);

            // Generate and save the PDF file
            $pdf = Pdf::loadView('sertificate_protocol.sertificate_pdf', compact(
                'application', 'group', 'sertNumber', 'currentYear', 'formattedDate', 'qrCode','index'
            ));

            $fileName = $index == 0
                ? "certificate_{$appId}.pdf"
                : "certificate_{$appId}_{$index}.pdf";

            $pdf->save(storage_path("app/public/sifat_sertificates/{$fileName}"));
        }
    }
    /**
     * Determine crop type based on ClampData.
     */
    private function determineCropType(Dalolatnoma $dalolatnoma): int
    {
        $clampData = $dalolatnoma->clamp_data()->first();
        return ($clampData && $clampData->croptype === "Á") ? 2 : 1;
    }

    /**
     * Generate pdf file for protocol
     */
    private function generatePdf($id, $group, $test, $type, $formattedDate, $formattedDate2, $qrCode, $i)
    {
        $pdf = Pdf::loadView('sertificate_protocol.protocol_pdf', compact(
            'test', 'type', 'formattedDate', 'formattedDate2', 'qrCode', 'group', 'i'
        ));

        $fileName = $i === 0
            ? 'protocol_' . $id . '.pdf'
            : 'protocol_' . $id . '_' . $i . '.pdf';

        $filePath = storage_path('app/public/protocols/' . $fileName);
        $pdf->save($filePath);
    }
    /**
     * Store LaboratoryResult data.
     */
    private function storeLaboratoryResult(Dalolatnoma $dalolatnoma, $clampData, $tip): void
    {
        LaboratoryResult::updateOrCreate(
            ['dalolatnoma_id' => $dalolatnoma->id,],
            [
                'tip_id' => optional($tip)->id ?? 11,
                'mic' => $clampData->mic,
                'staple' => $clampData->staple,
                'strength' => $clampData->strength,
                'uniform' => $clampData->uniform,
                'fiblength' => $clampData->fiblength,
                'humidity' => 5.1,
            ]
        );
    }

    /**
     * Update FinalResults.
     */
    private function saveFinalResults(Dalolatnoma $dalolatnoma): void
    {
        $results = $dalolatnoma->summarizeClampData();
        $data = $dalolatnoma->averageClampData();

        foreach ($results as $result) {
            FinalResult::updateOrCreate(
                [
                    'dalolatnoma_id' => $dalolatnoma->id,
                    'sort' => $result->sort,
                    'class' => $result->class,
                ],
                [
                    'count' => $result->count,
                    'amount' => $result->total_amount,
                    'mic' => $data?->mic,
                    'staple' => $data?->staple,
                    'strength' => $data?->strength,
                    'uniform' => $data?->uniform,
                    'humidity' => 5,
                ]
            );
        }
    }
}
