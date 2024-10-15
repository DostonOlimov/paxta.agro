<?php

namespace App\Http\Controllers;


use App\Filters\V1\ApplicationFilter;
use App\Models\Application;
use App\Models\ChigitResult;
use App\Models\ChigitTips;
use App\Models\ClientData;
use App\Models\Clients;
use App\Models\CropData;
use App\Models\CropsSelection;
use App\Models\Indicator;
use App\Models\OrganizationCompanies;
use App\Models\SifatSertificates;
use App\Services\SearchService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\DefaultModels\tbl_activities;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\HttpFoundation\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class SifatSertificateController extends Controller
{
    public function applicationList(Request $request, ApplicationFilter $filter,SearchService $service)
    {
        try {
            session(['crop'=>2]);

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
                'sifat_sertificate.list',
                [],
                false,
                null,
                null,
                ['prepared_id', '=', \auth()->user()->zavod_id]
            );

        } catch (\Throwable $e) {
            // Log the error for debugging
            \Log::error($e);
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    // application addform

    public function addApplication($organization)
    {
        $names = DB::table('crops_name')->where('id','!=',1)->get()->toArray();
        $selection = CropsSelection::get();

        return view('sifat_sertificate.add',compact('organization','selection','names'));

    }


    // application store
    public function store(Request $request)
    {
        $this->authorize('create', Application::class);

        $user = Auth::user();

        $crop = CropData::create([
            'name_id'       => $request->input('name'),
            'country_id'    => 234,
            'kodtnved'      => $request->input('tnved'),
            'party_number'  => $request->input('party_number'),
            'measure_type'  => $request->input('measure_type'),
            'amount'        => $request->input('amount'),
            'selection_code' => $request->input('selection_code'),
            'year'          => 2024,
            'toy_count'     => 1,
            'sxeme_number'  => 7,
        ]);

        $application = Application::create([
            'crop_data_id'     => $crop->id,
            'organization_id'  => $request->input('organization'),
            'prepared_id'      => $user->zavod_id,
            'type'             => Application::TYPE_1,
            'date'             => date('Y-m-d'),
            'status'           => Application::STATUS_FINISHED,
            'data'             => $request->input('data'),
            'created_by'       => $user->id,
        ]);

        tbl_activities::create([
            'ip_adress'   => request()->ip(),
            'user_id'     => $user->id,
            'action_id'   => $application->id,
            'action_type' => 'app_add',
            'action'      => "Ariza qo'shildi",
            'time'        => now(),
        ]);

        return redirect()->route('sifat-sertificates.add_client',$application->id)->with('message', 'Successfully Submitted');
    }

    public function addClientData($id)
    {
        $clients = DB::table('clients')->get()->toArray();

        return view('sifat_sertificate.client_data_add',compact('clients','id'));

    }
    public function ClientDataStore(Request $request)
    {

        $user = Auth::user();

        $crop = ClientData::create([
            'app_id'       => $request->input('id'),
            'client_id'    => $request->input('client'),
            'vagon_number'      => $request->input('number'),
            'yuk_xati'  => $request->input('yuk_xati'),
        ]);


        return redirect()->route('sifat-sertificates.add_result',$request->input('id'))->with('message', 'Successfully Submitted');
    }

    public function addResult($id)
    {
        $indicators = Indicator::where('crop_id','=',2)
            ->get();
        return view('sifat_sertificate.add_result',compact('indicators','id'));

    }
    public function ResultStore(Request $request)
    {
        $appId = $request->input('id');

        $indicators = Indicator::where('crop_id', 2)->pluck('id');

        $results = $indicators->map(function ($indicatorId) use ($appId, $request) {
            return [
                'app_id' => $appId,
                'indicator_id' => $indicatorId,
                'value' => $request->input('value' . $indicatorId),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        ChigitResult::insert($results->toArray());

        return redirect()->route('/sifat-sertificates/list')
            ->with('message', 'Successfully Submitted');
    }

    public function showapplication($id)
    {
        $test = Application::findOrFail($id);
        $company = OrganizationCompanies::with('city')->findOrFail($test->organization_id);
        $formattedDate = formatUzbekDateInLatin($test->date);

        // Generate QR code
        $url = route('sifat_sertificate.view', $id);
        $qrCode = QrCode::size(100)->generate($url);

        // Fetch values and tip
        $chigitValues = $this->getChigitValuesAndTip($test);

        return view('sifat_sertificate.show', compact('test', 'formattedDate','company', 'qrCode') + $chigitValues);
    }

    public function edit($id)
    {
        $data = Application::findOrFail($id);
        $company = OrganizationCompanies::with('city')->findOrFail($data->organization_id);

        // Fetch values and tip
        $chigitValues = $this->getChigitValuesAndTip($data);

        return view('sifat_sertificate.edit', compact('data', 'company') + $chigitValues);
    }

    public function editData($id)
    {
        $data = Application::findOrFail($id);

        $names = DB::table('crops_name')->where('id','!=',1)->get()->toArray();
        $selection = CropsSelection::get();

        return view('sifat_sertificate.edit_data', compact('data','names','selection'));
    }

    public function update(Request $request)
    {
        $id = $request->input('id');

        // Validate incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string',
            'tnved' => 'nullable|string',
            'party_number' => 'nullable|string',
            'amount' => 'required|numeric',
            'selection_code' => 'nullable|string',
        ]);

        // Find the application and related crop data
        $app = Application::findOrFail($id);
        $crop_data = $app->crops;

        // Update the crop data with validated input
        $crop_data->update([
            'name_id' => $validatedData['name'],
            'kodtnved' => $validatedData['tnved'],
            'party_number' => $validatedData['party_number'],
            'amount' => $validatedData['amount'],
            'selection_code' => $validatedData['selection_code'],
        ]);

        // Redirect with success message
        return redirect()->route('sifat_sertificate.edit', $id)->with('message', 'Successfully Submitted');
    }

    //edit client data
    public function clientEdit($id)
    {
        $data = ClientData::findOrFail($id);

        $clients = Clients::get();

        return view('sifat_sertificate.client_edit', compact('data','clients'));
    }

    public function clientUpdate (Request $request)
    {

        $id = $request->input('id');
        $client = ClientData::findOrFail($id);
        $client->client_id = $request->input('client');
        $client->vagon_number = $request->input('number');
        $client->yuk_xati = $request->input('yuk_xati');
        $client->save();

        return redirect()->route('sifat_sertificate.edit',$client->app_id)->with('message', 'Successfully Submitted');
    }

    //edit result data
    public function resultEdit ($id)
    {
        $data = Application::findOrFail($id);

        $chigitValues = $this->getChigitValuesAndTip($data);

        return view('sifat_sertificate.result_edit', compact('data')+$chigitValues);
    }

    public function resultUpdate(Request $request)
    {

        $id = $request->input('id');
        $client = Application::findOrFail($id);
        foreach ($client->chigit_result as $result){
            $result->value = $request->input('value'. $result->id);
            $result->save();
        }

        return redirect()->route('sifat_sertificate.edit',$id)->with('message', 'Successfully Submitted');
    }

    //accept online applications
    public function accept($id)
    {
        $test = Application::findOrFail($id);
        $company = OrganizationCompanies::with('city')->findOrFail($test->organization_id);

        // date format
        $formattedDate = formatUzbekDateInLatin($test->date);
        $currentYear = date('Y');
        $zavod_id = $test->user->zavod_id;
        $number = SifatSertificates::where('zavod_id', $zavod_id)
            ->where('year', $currentYear)
            ->max('number');
        // create sifat certificate
        if (!$test->sifat_sertificate) {
            $sertificate = new SifatSertificates();
            $sertificate->app_id = $id;
            $sertificate->number = $number ? $number + 1 : 1;
            $sertificate->zavod_id = $zavod_id;
            $sertificate->year = $currentYear;
            $sertificate->created_by = \auth()->user()->id;
            $sertificate->save();
        }

        $sert_number = ($currentYear - 2000) * 1000000 + ($test->prepared->kod)*1000 + $number;

        // Generate QR code
        $qrCode = base64_encode(QrCode::format('png')->size(100)->generate(route('sifat_sertificate.download', $id)));

        // Fetch values and tip
        $chigitValues = $this->getChigitValuesAndTip($test);

        // Load the view and pass data to it
        $pdf = Pdf::loadView('sifat_sertificate.pdf', compact('test', 'sert_number','formattedDate', 'company', 'qrCode') + $chigitValues);
        return $pdf->stream('certificate_' . $id . '.pdf');
        // Save the PDF file
        $filePath = storage_path('app/public/sifat_sertificates/certificate_' . $id . '.pdf');
        $pdf->save($filePath);

        // Redirect to list page with success message
        return redirect()->route('/sifat-sertificates/list', ['generatedAppId' => $id])
            ->with('message', 'Certificate saved!');
    }


    public function download($id)
    {
        $filePath = storage_path('app/public/sifat_sertificates/certificate_' . $id . '.pdf');

        if (file_exists($filePath)) {
            return response()->download($filePath);
        } else {
            return redirect()->back()->with('error', 'File not found.');
        }
    }

    //accept online applications
    public function reject($id)
    {
        $test = Application::findOrFail($id);
        $company = OrganizationCompanies::with('city')->findOrFail($test->organization_id);

        // date format
        $formattedDate = formatUzbekDateInLatin($test->date);
        $currentYear = date('Y');
        $zavod_id = $test->user->zavod_id;
        $number = SifatSertificates::where('zavod_id', $zavod_id)
            ->where('year', $currentYear)
            ->max('number');
        // create sifat certificate
        if (!$test->sifat_sertificate) {
            $sertificate = new SifatSertificates();
            $sertificate->app_id = $id;
            $sertificate->number = $number ? $number + 1 : 1;
            $sertificate->zavod_id = $zavod_id;
            $sertificate->year = $currentYear;
            $sertificate->created_by = \auth()->user()->id;
            $sertificate->save();
        }

        $sert_number = ($currentYear - 2000) * 1000000 + ($test->prepared->kod)*1000 + $number;

//        test->sifat_sertificate->zavod->region->series
        // Generate QR code
        $qrCode = base64_encode(QrCode::format('png')->size(100)->generate(route('sifat_sertificate.download', $id)));

        // Fetch values and tip
        $chigitValues = $this->getChigitValuesAndTip($test);

        // Load the view and pass data to it
        $pdf = Pdf::loadView('sifat_sertificate.pdf2', compact('test', 'sert_number','formattedDate', 'company', 'qrCode') + $chigitValues);
        return $pdf->stream('certificate_' . $id . '.pdf');
        // Save the PDF file
        $filePath = storage_path('app/public/sifat_sertificates/certificate_' . $id . '.pdf');
        $pdf->save($filePath);

        // Redirect to list page with success message
        return redirect()->route('/sifat-sertificates/list', ['generatedAppId' => $id])
            ->with('message', 'Certificate saved!');
    }
// Private method to avoid code duplication
    private function getChigitValuesAndTip($application)
    {
        $nuqsondorlik = optional($application->chigit_result()->where('indicator_id', 9)->first())->value ?? 0;
        $tukdorlik = optional($application->chigit_result()->where('indicator_id', 12)->first())->value ?? 0;
        $namlik = optional($application->chigit_result()->where('indicator_id', 11)->first())->value ?? 0;
        $zararkunanda = optional($application->chigit_result()->where('indicator_id', 10)->first())->value ?? 0;

        $tip = ChigitTips::where('nuqsondorlik', '>=', $nuqsondorlik)
            ->where('tukdorlik', '>=', $tukdorlik)
            ->where('crop_id', $application->crops->name_id)
            ->first();

        return [
            'nuqsondorlik' => $nuqsondorlik,
            'tukdorlik' => $tukdorlik,
            'namlik' => $namlik,
            'zararkunanda' => $zararkunanda,
            'tip' => $tip,
        ];
    }
}

