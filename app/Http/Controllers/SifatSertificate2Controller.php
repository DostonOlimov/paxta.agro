<?php

namespace App\Http\Controllers;


use App\Filters\V1\ApplicationFilter;
use App\HelperClasses\ChigitQualityEvaluator;
use App\Models\Application;
use App\Models\ChigitLaboratories;
use App\Models\ChigitResult;
use App\Models\ClientData;
use App\Models\CropData;
use App\Models\CropsSelection;
use App\Models\Indicator;
use App\Models\OrganizationCompanies;
use App\Models\PreparedCompanies;
use App\Models\SifatSertificates;
use App\Services\SearchService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\DefaultModels\tbl_activities;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\HttpFoundation\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class SifatSertificate2Controller extends Controller
{

    public function applicationList(Request $request, ApplicationFilter $filter,SearchService $service)
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
                    'crops.name',
                    'organization.area.region',
                    'prepared',
                    'sifat_sertificate'
                ],
                compact('names', 'states', 'years','all_status'),
                'sifat_sertificate2.list',
                [],
                false,
                'crops',
                'amount',
                ['app_type','=',2]
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
        $user = Auth::user();
        $names = getCropsNames();
        $selection = getSelections();
        $years = CropData::getYear();
        $laboratories = ChigitLaboratories::whereHas('zavod', function ($query) use ($user) {
            $query->where('state_id', '=', $user->state_id);
        })->get();

        return view('sifat_sertificate2.add',compact('organization','user','years','laboratories','selection','names'));

    }


    // application store
    public function store(Request $request)
    {
        $user = Auth::user();

        // Define validation rules with camelCase attribute names
        $validatedData = $request->validate([
            'name' => 'required|int',
            'party_number' => 'required|string|max:10',
            'amount' => 'required',
            'selection_code' => 'required|int',
        ]);

        $crop = CropData::create([
            'name_id'       => $request->input('name'),
            'country_id'    => 234,
            'kodtnved'      => $request->input('tnved'),
            'party_number'  => $request->input('party_number'),
            'measure_type'  => $request->input('measure_type'),
            'amount'        => $request->input('amount'),
            'selection_code' => $request->input('selection_code'),
            'year'          => $request->input('year'),
            'toy_count'     => 1,
            'sxeme_number'  => 7,
        ]);

        $zavod_id = $user->zavod_id;
        if($lab_id = $request->input('laboratory')){
            $zavod_id = ChigitLaboratories::findOrFail($lab_id)->zavod->id;
        }
        $application = Application::create([
            'crop_data_id'     => $crop->id,
            'organization_id'  => $request->input('organization'),
            'prepared_id'      => $zavod_id ,
            'type'             => Application::TYPE_1,
            'date'             => $request->input('dob') ? date('Y-m-d', strtotime($request->input('dob'))) : date('Y-m-d'),
            'status'           => Application::STATUS_FINISHED,
            'data'             => $request->input('data'),
            'app_type'         => 2,
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

        return redirect()->route('sifat-sertificates2.add_client',$application->id)->with('message', 'Successfully Submitted');
    }

    public function addClientData($id)
    {
        $user = Auth::user();
        $clients = DB::table('clients')->get()->toArray();

        return view('sifat_sertificate2.client_data_add',compact('clients','id','user'));

    }
    public function ClientDataStore(Request $request)
    {

        $crop = ClientData::create([
            'app_id'       => $request->input('id'),
            'client_id'    => $request->input('client') ?? 0,
            'vagon_number'      => $request->input('number'),
            'yuk_xati'  => $request->input('yuk_xati'),
        ]);


        return redirect()->route('sifat-sertificates2.add_result',$request->input('id'))->with('message', 'Successfully Submitted');
    }

    public function addResult($id)
    {
        $indicators = Indicator::where('crop_id','=',2)
            ->get();
        return view('sifat_sertificate2.add_result',compact('indicators','id'));

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

        return redirect()->route('/sifat-sertificates2/list')
            ->with('message', 'Successfully Submitted');
    }

    public function showapplication($id)
    {
        $test = Application::with('user')->findOrFail($id);
        $company = OrganizationCompanies::with('city')->findOrFail($test->organization_id);
        $formattedDate = formatUzbekDateInLatin($test->date);

        // Generate QR code
        $url = route('sifat_sertificate2.view', $id);
        $qrCode = QrCode::size(100)->generate($url);

        // Fetch values and tip
        $chigitValues = $this->getChigitValuesAndTip($test);

        return view('sifat_sertificate2.show', compact('test', 'formattedDate','company', 'qrCode') + $chigitValues);
    }

    public function edit($id)
    {
        $data = Application::findOrFail($id);
        $company = OrganizationCompanies::with('city')->findOrFail($data->organization_id);

        // Fetch values and tip
        $chigitValues = $this->getChigitValuesAndTip($data);

        return view('sifat_sertificate2.edit', compact('data', 'company') + $chigitValues);
    }

    public function editData($id)
    {
        $data = Application::findOrFail($id);

        $names = DB::table('crops_name')->where('id','!=',1)->get()->toArray();
        $selection = CropsSelection::get();

        return view('sifat_sertificate2.edit_data', compact('data','names','selection'));
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
        return redirect()->route('sifat_sertificate2.edit', $id)->with('message', 'Successfully Submitted');
    }

    //edit result data
    public function resultEdit ($id)
    {
        $data = Application::findOrFail($id);

        $chigitValues = $this->getChigitValuesAndTip($data);

        return view('sifat_sertificate2.result_edit', compact('data')+$chigitValues);
    }

    public function resultUpdate(Request $request)
    {

        $id = $request->input('id');
        $client = Application::findOrFail($id);
        foreach ($client->chigit_result as $result){
            $result->value = $request->input('value'. $result->id);
            $result->save();
        }

        return redirect()->route('sifat_sertificate2.edit',$id)->with('message', 'Successfully Submitted');
    }

    //accept online applications
    public function accept($id)
    {
        $test = Application::findOrFail($id);

        //setting type
        $type = SifatSertificates::CIGIT_TYPE_XARIDORLI;
        if(optional($test->client_data)->client_id == 0){
            $type = SifatSertificates::CIGIT_TYPE_XARIDORSIZ;
        }

        $company = OrganizationCompanies::with('city')->findOrFail($test->organization_id);
        // Fetch values and tip
        $chigitValues = $this->getChigitValuesAndTip($test);

        // date format
        $formattedDate = formatUzbekDateInLatin($test->date);

        $currentYear =date("Y", strtotime($test->date));
        $zavod_id = $test->prepared_id;
        $number = 0;

        $sertQuery = SifatSertificates::where('year', $currentYear)
            ->where('type',$type);

        if($type == SifatSertificates::CIGIT_TYPE_XARIDORLI){
            $sertQuery = $sertQuery->where('zavod_id', $zavod_id);
        }

        $number = $sertQuery->max('number');

        $number = $number ? $number + 1 : 1;

        // create sifat certificate
        if (!$test->sifat_sertificate) {

            $sertificate = new SifatSertificates();
            $sertificate->app_id = $id;
            $sertificate->number = $number;
            $sertificate->zavod_id = $zavod_id;
            $sertificate->year = $currentYear;
            $sertificate->type = $type;
            $sertificate->quality = $chigitValues['quality'];
            $sertificate->amount = round($test->crops->amount * ((100 - $chigitValues['namlik'] - $chigitValues['zararkunanda']) / (100-10-0.5)),3);
            $sertificate->created_by = \auth()->user()->id;
            $sertificate->save();
        }else{
            $number = $test->sifat_sertificate->number;
        }

        $kod_middle =  ($type == 1) ? ($test->prepared->kod)*1000 : 500000;
        $sert_number = ($currentYear - 2000) * 1000000 + $kod_middle + $number;

        // Generate QR code
        $qrCode = base64_encode(QrCode::format('png')->size(100)->generate(route('sifat_sertificate2.download', $id)));

        // Load the view and pass data to it
        $pdf = Pdf::loadView('sifat_sertificate2.pdf', compact('test','sert_number','formattedDate', 'company', 'qrCode') + $chigitValues);

//        return $pdf->stream('sdf');
        // Save the PDF file
        $filePath = storage_path('app/public/sifat_sertificates/certificate_' . $id . '.pdf');
        $pdf->save($filePath);

        // Redirect to list page with success message
        return redirect()->route('/sifat-sertificates2/list', ['generatedAppId' => $id])
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

    // Private method to avoid code duplication
    private function getChigitValuesAndTip($application)
    {
        $evaluator = new ChigitQualityEvaluator($application);
        return $evaluator->getResults();
    }
}

