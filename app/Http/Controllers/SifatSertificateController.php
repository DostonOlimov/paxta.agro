<?php

namespace App\Http\Controllers;


use App\Filters\V1\ApplicationFilter;
use App\Models\Application;
use App\Models\AppStatusChanges;
use App\Models\ChigitResult;
use App\Models\ChigitTips;
use App\Models\ClientData;
use App\Models\CropData;
use App\Models\CropsSelection;
use App\Models\Indicator;
use App\Models\OrganizationCompanies;
use App\Services\SearchService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\DefaultModels\tbl_activities;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\HttpFoundation\Response;

class SifatSertificateController extends Controller
{
    public function applicationlist(Request $request, ApplicationFilter $filter,SearchService $service)
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

    public function addapplication($organization)
    {
        $names = DB::table('crops_name')->where('id','!=',1)->get()->toArray();
        $countries = DB::table('tbl_countries')->get()->toArray();
        $measure_types = CropData::getMeasureType();
        $year = CropData::getYear();
        $selection = CropsSelection::get();

        return view('sifat_sertificate.add',compact('organization','selection','names', 'countries','measure_types','year'));

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
            'party2'  => $request->input('party_number2'),
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
        $qrCode = null;
        $url = route('sifat_sertificate.view', $id);
        $qrCode = QrCode::size(100)->generate($url);
        $user = \auth()->user();

        $nuqsondorlik = optional($test->chigit_result()->where('indicator_id','=',9)->first())->value;
        $tukdorlik = optional($test->chigit_result()->where('indicator_id','=',12)->first())->value;
        $namlik = optional($test->chigit_result()->where('indicator_id','=',11)->first())->value;
        $zararkunanda = optional($test->chigit_result()->where('indicator_id','=',10)->first())->value;

        $tip = ChigitTips::where('nuqsondorlik', '>=', $nuqsondorlik )
            ->where('tukdorlik', '>=', $tukdorlik )
            ->where('crop_id', $test->crops->name_id)
            ->first();

        return view('sifat_sertificate.show', compact('test','tip', 'user','company','qrCode','nuqsondorlik','zararkunanda','namlik','tukdorlik'));
    }

    public function edit($id)
    {
        $data = Application::findOrFail($id);
        $company = OrganizationCompanies::with('city')->findOrFail($data->organization_id);
        $qrCode = null;
        $url = route('sifat_sertificate.view', $id);
        $qrCode = QrCode::size(100)->generate($url);
        $user = \auth()->user();

        return view('sifat_sertificate.edit', compact('data', 'user','company','qrCode'));
    }

    //accept online applications
    public function accept($id)
    {
        $app = Application::findOrFail($id);
        $this->authorize('update', $app);

        $app->update([
            'status'       => Application::STATUS_ACCEPTED,
            'progress'     => Application::PROGRESS_ANSWERED,
            'accepted_date'=> now(),
            'accepted_id'  => Auth::id(),
        ]);

        return redirect()->route('listapplication')->with('message', 'Successfully Accepted');
    }

    //reject online applications
    public function reject($id)
    {
        $app = Application::findOrFail($id);

        return view('application.reject', compact('app'));
    }

    public function reject_store(Request $request)
    {
        $app = Application::findOrFail($request->input('app_id'));
        $this->authorize('accept', $app);

        $app->update([
            'status' => Application::STATUS_REJECTED,
        ]);

        AppStatusChanges::create([
            'app_id'  => $app->id,
            'status'  => Application::STATUS_REJECTED,
            'comment' => $request->input('reason'),
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('listapplication')->with('message', 'Application Rejected Successfully');
    }

}

