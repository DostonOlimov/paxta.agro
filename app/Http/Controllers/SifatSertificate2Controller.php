<?php

namespace App\Http\Controllers;


use App\Filters\V1\ApplicationFilter;
use App\Models\Application;
use App\Models\ChigitTips;
use App\Models\CropData;
use App\Models\CropsSelection;
use App\Models\OrganizationCompanies;;
use App\Services\SearchService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\DefaultModels\tbl_activities;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\HttpFoundation\Response;

class SifatSertificate2Controller extends Controller
{

    public function applicationList(Request $request, ApplicationFilter $filter,SearchService $service)
    {
        $user_id = Auth::user()->id;

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
                'sifat_sertificate2.list',
                [],
                false,
                null,
                null,
                ['app_type', '=', 2]
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

        return view('sifat_sertificate2.add',compact('organization','selection','names'));

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

        return view('sifat_sertificate2.show', compact('test', 'formattedDate','company', 'qrCode') + $chigitValues);
    }

// Private method to avoid code duplication
    private function getChigitValuesAndTip($application)
    {
        $nuqsondorlik = optional($application->chigit_result()->where('indicator_id', 9)->first())->value;
        $tukdorlik = optional($application->chigit_result()->where('indicator_id', 12)->first())->value;
        $namlik = optional($application->chigit_result()->where('indicator_id', 11)->first())->value;
        $zararkunanda = optional($application->chigit_result()->where('indicator_id', 10)->first())->value;

        $tip = null;
        if($nuqsondorlik and $tukdorlik){
            $tip = ChigitTips::where('nuqsondorlik', '>=', $nuqsondorlik);
            if($application->crops->name_id == 2){
                $tip = $tip->where('tukdorlik', '>=', $tukdorlik)
                    ->where('tukdorlik_min', '<=', $tukdorlik);
            }

            $tip = $tip->where('crop_id', $application->crops->name_id)
                ->first();
        }

        $quality = false;
        if($tip && $namlik <= $tip->namlik && $tukdorlik <= $tip->tukdorlik and $tukdorlik >= $tip->tukdorlik_min){
            $quality = true;
        }

        return [
            'nuqsondorlik' => $nuqsondorlik,
            'tukdorlik' => $tukdorlik,
            'namlik' => $namlik,
            'zararkunanda' => $zararkunanda,
            'tip' => $tip,
            'quality' => $quality
        ];
    }
}

