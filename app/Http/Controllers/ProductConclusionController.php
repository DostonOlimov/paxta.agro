<?php

namespace App\Http\Controllers;

use App\Filters\V1\DalolatnomaFilter;
use App\Models\Application;
use App\Models\CropsName;
use App\Models\Dalolatnoma;
use App\Models\FinalConclusionResult;
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

class ProductConclusionController extends Controller
{
    //search
    public function list(Request $request, DalolatnomaFilter $filter,SearchService $service): View|Factory|JsonResponse
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
                'product_conclusion.list',
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
        return view('product_conclusion.add', compact('dalolatnoma', 'clampData', 'operators'));
    }

    public function store(Dalolatnoma $dalolatnoma, Request $request)
    {
        $this->authorize('create', Application::class);

        $user = Auth::user();
        $formData = $request->all();

        // Default klassiyor_id to current user if not provided
        $formData['klassiyor_id'] = $formData['klassiyor_id'] ?? $user->id;

        // Save final results if not exists
        $this->saveFinalResults($dalolatnoma);

        // Parse and set date
        $formData['dalolatnoma_id'] = $dalolatnoma->id;
        $formData['date'] = Carbon::createFromFormat('d-m-Y', $request->input('date'))->toDateString();

        FinalConclusionResult::create([
            'dalolatnoma_id' => $dalolatnoma->id,
            'invoice_number' => $request->input('invoice_number'),
            'vehicle_number' => $request->input('vehicle_number'),
            'conclusion_part_1' => "kunidagi namuna tanlab olish dalolatnomasi bilan taqdim qilingan oʼralgan holdagi namunalar ochilib, namunalar klassyorlik usulida, orgonoleptik baho berish yoʼli bilan paxta mahsuloti va uni qayta ishlashdan olingan (ikkilamchi) mahsulotlar tashqi koʼrinish boʼyicha amaldagi standartlarga muvofiq belgilangan tartibda solishtirildi.",
            'conclusion_part_2' => "Solishtirish natijasida ushbu mahsulot: Orgonoleptik baho berishilishiga asosan taqdim qilingan namunalar O‘z DSt 604:2016 Paxta tolasi texnikaviy shartlar. O‘zMSt 456:2024 (O‘z DSt 645:2010) Paxta momigʼi texnikaviy shartlar. O‘z DSt 1029:2014 Kiyim kechak va mebelbop momiq paxta texnikaviy shartlar. Davlat standartlariga tashqi koʼrinish boʼyicha tugʼri kelmasligi aniqlandi.",
            'conclusion_part_3' => "Ushbu maxsulot O‘zDSt 1216:2015 Oqartirilgan gigroskopik paxta maxsulotlari texnikaviy shartlarining. Oqartirilgan Gigroskopik momiq (Vata otbelennaya gigroskopicheskaya) turiga toʼgʼri kelishini maʼlum qilamiz.",
            'type' => $request->input('type') ?? 1,
        ]);

        LaboratoryFinalResults::create($formData);

        return redirect('/product-conclusion/list')->with('message', 'Successfully Submitted');
    }

    public function refresh(Dalolatnoma $dalolatnoma)
    {
        $this->authorize('create', Application::class);

        $clampData = $dalolatnoma->averageClampData();

        if($clampData?->mic){

            // Store Laboratory Result
            $this->storeLaboratoryResult($dalolatnoma);

            // Update final result
            $this->saveFinalResults($dalolatnoma);
        }

        return redirect('/product-conclusion/list')->with('message', 'Successfully Submitted');
    }

    //product conclusion view
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

        return view('product_conclusion.view', compact(
            'labResults', 'test', 'type', 'formattedDate', 'formattedDate2','t'
        ));
    }

    //saving product conclusion
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

        return redirect('/product-conclusion/list?generatedAppId=' . $test->id)
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

        return view('product_conclusion.sertificate_view', compact('application', 'dalolatnoma','labResults','formattedDate','t'));
    }


    // Accept online applications
    public function accept(Dalolatnoma $dalolatnoma)
    {
        $application = $dalolatnoma->test_program->application;
        $appId = $application->id;

        // Determine certificate type based on session
        $sertType = getApplicationType() == CropsName::CROP_TYPE_4
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

        return redirect()->route('product_conclusion.list', ['generatedAppId' => $appId])
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
    private function storeLaboratoryResult(Dalolatnoma $dalolatnoma): void
    {
        LaboratoryResult::updateOrCreate(
            ['dalolatnoma_id' => $dalolatnoma->id,],
            [
                'tip_id' => 11,
                'mic' => 1,
                'staple' => 1,
                'strength' => 1,
                'uniform' => 1,
                'fiblength' => 1,
                'humidity' => 5.1,
            ]
        );
    }

    /**
     * Update FinalResults.
     */
    private function saveFinalResults(Dalolatnoma $dalolatnoma): void
    {
        FinalResult::updateOrCreate(
            [
                'dalolatnoma_id' => $dalolatnoma->id,
                'sort' => 1,
                'class' => 1,
            ],
            [
                'count' => $dalolatnoma->akt_amount->count(),
                'amount' => $dalolatnoma->akt_amount->sum('amount'),
                'mic' => 1,
                'staple' => 1,
                'strength' => 1,
                'uniform' => 1,
                'humidity' => 5,
            ]
        );
    }
}
