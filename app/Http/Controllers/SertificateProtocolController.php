<?php

namespace App\Http\Controllers;

use App\Filters\V1\DalolatnomaFilter;
use App\Models\Application;
use App\Models\ClampData;
use App\Models\Dalolatnoma;
use App\Models\FinalResult;
use App\Models\LaboratoryFinalResults;
use App\Models\LaboratoryOperator;
use App\Models\MeasurementMistake;
use App\Models\OrganizationCompanies;
use App\Models\SifatSertificates;
use App\Models\Tips;
use App\Models\User;
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


    public function add($id)
    {
        $user=Auth::user();
        $apps= Dalolatnoma::with('test_program.application.decision.laboratory.city')->find($id);
        $operators = LaboratoryOperator::where('laboratory_id', $apps->test_program->application->decision->laboratory_id)->get();

        $klassiyor=ClampData::with('klassiyor')->where('dalolatnoma_id',$id)->first();
        $test = Dalolatnoma::with('gin_balles')
            ->with('clamp_data')
            ->find($id);

        if($klassiyor && !$klassiyor->klassiyor){
            return redirect('/sertificate-protocol/list')->with('message', $klassiyor->classer_id . ' kodli Klassiyor topilmadi');
        }

        return view('sertificate_protocol.add', compact('apps','klassiyor','operators','test'));
    }

    public function store(Request $request)
    {
        $userA = Auth::user();
        $this->authorize('create', Application::class);
        $data=$request->all();
        dd($data);
        $data['klassiyor_id'] = $data['klassiyor_id'] ?? $userA->id;

        $clamp_data = ClampData::selectRaw('
        AVG(mic) as mic,
        AVG(staple) as staple,
        AVG(strength) as strength,
        AVG(uniform) as uniform,
        AVG(fiblength) as fiblength'
        )
            ->where('dalolatnoma_id', $id)
            ->first();

        $fiblength = round($clamp_data->fiblength / 100,2);
        $tip = Tips::where('max', '>=', $fiblength)
            ->where('min', '<=', $fiblength)
            ->first();

        // Storing LaboratoryResult
        $this->storeLaboratoryResult($id, $clamp_data, $tip, $humidity_result);

        // Check if FinalResult exists, if not, create
        if (!FinalResult::where('dalolatnoma_id', $id)->exists()) {
            $this->storeFinalResults($id, $clamp_data, $humidity_result);
        }

        $parsedDate = Carbon::createFromFormat('d-m-Y', $request->input('date'));
        $reformattedDate = $parsedDate->format('Y-m-d');
        $data['date']=$reformattedDate;

        LaboratoryFinalResults::create($data);

        return redirect('/sertificate-protocol/list')->with('message', 'Successfully Submitted');
    }

    public function view($id)
    {
        $test = Dalolatnoma::with('measurement_mistake')
            ->with('laboratory_result')
            ->with('result')
            ->with('selection')
            ->with('laboratory_final_results.operator')
            ->with('laboratory_final_results.klassiyor')
            ->with('laboratory_final_results.director')
            ->with('test_program.application.decision.laboratory.city.region')
            ->with('test_program.application.crops.name')
            ->with('test_program.application.organization.city')
            ->with('test_program.application.prepared.region')
            ->find($id);

        // date format
        $formattedDate = formatUzbekDateInLatin($test->laboratory_final_results->date);
        $formattedDate2 = formatUzbekDateInLatin($test->date);

        $final_results=FinalResult::with('dalolatnoma.laboratory_result')->where('dalolatnoma_id', $id)->get();

        $qrCode = null;
        if ($test->laboratory_final_results->status == 1) {
            $url = route('lab.view', $id);
            $qrCode = QrCode::size(100)->generate($url);
        }
        $t = 1;

        return view('sertificate_protocol.view', compact('test','t', 'qrCode','final_results','formattedDate','formattedDate2'));
    }

    public function sertificateView ($id)
    {
        $dalolatnoma = Dalolatnoma::with('measurement_mistake')
            ->with('laboratory_result')
            ->with('result')
            ->with('selection')
            ->with('laboratory_final_results.operator')
            ->with('laboratory_final_results.klassiyor')
            ->with('laboratory_final_results.director')
            ->with('test_program.application.decision.laboratory.city.region')
            ->with('test_program.application.crops.name')
            ->with('test_program.application.organization.city')
            ->with('test_program.application.prepared.region')
            ->find($id);
        $test = $dalolatnoma->test_program->application;

        $formattedDate = formatUzbekDateInLatin($dalolatnoma->laboratory_result->date);

        // Generate QR code
        $url = route('sertificate_protocol.sertificate_view', $id);
        $qrCode = QrCode::size(100)->generate($url);
        $t = 1;

        $final_results = FinalResult::with('dalolatnoma.laboratory_result')->where('dalolatnoma_id', $id)->get();

        return view('sertificate_protocol.sertificate_view', compact('test', 'qrCode','final_results','formattedDate','t'));
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
        $dalolatnoma = Dalolatnoma::findOrFail($id);
        $test = $dalolatnoma->test_program->application;

        $final_results = FinalResult::with('dalolatnoma.laboratory_result')->where('dalolatnoma_id', $id)->get();

        // date format
        $formattedDate = formatUzbekDateInLatin($dalolatnoma->laboratory_final_results->date);
        $currentYear = date('Y');
        $zavod_id = $test->prepared_id;


//        setting sertificate number
        $number = 0;
        $number = SifatSertificates::where('year', $currentYear)
            ->where('type', SifatSertificates::PAXTA_TYPE)
            ->max('number');

        $number = $number ? $number + 1 : 1;

        // create sifat certificate
        if (!$test->sifat_sertificate) {

            $sertificate = new SifatSertificates();
            $sertificate->app_id = $test->id;
            $sertificate->number = $number;
            $sertificate->zavod_id = $zavod_id;
            $sertificate->year = $currentYear;
            $sertificate->type = SifatSertificates::PAXTA_TYPE;
            $sertificate->created_by = \auth()->user()->id;
            $sertificate->save();
        }

        $sert_number = ($currentYear - 2000) * 1000000  + $number;

        // Generate QR code
        $qrCode = base64_encode(QrCode::format('png')->size(100)->generate(route('sifat_sertificate.download', $id)));

        // Load the view and pass data to it
        $pdf = Pdf::loadView('sertificate_protocol.sertificate_pdf', compact('test','sert_number','formattedDate','qrCode','final_results'));

//        return $pdf->stream('sdf.pdf');
        // Save the PDF file
        $filePath = storage_path('app/public/sifat_sertificates/certificate_' . $id . '.pdf');
        $pdf->save($filePath);

        // Redirect to list page with success message
        return redirect('/sertificate-protocol/list?generatedAppId='. $id)
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

}
