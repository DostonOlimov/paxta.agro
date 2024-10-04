<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Decision;
use App\Models\Indicator;
use App\Models\Nds;
use App\Models\TestPrograms;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TestProgramsController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth')->except('my_view');
    }

    public function view($id)
    {
        $tests = TestPrograms::with('application.crops')
            ->with('application.decision.director')
            ->with('application.crops.name')
            ->with('application.crops.name.nds')
            ->with('application.crops.type')
            ->with('application.crops.generation')
            ->with('application')
            ->find($id);
        // Generate QR code if available
        $qrCode = null;
        if ($tests->application->decision->status == Decision::STATUS_ACCEPTED) {
            $url = route('tests.show', $id);
            $qrCode = QrCode::size(100)->generate($url);
        }
        $indicators = Indicator::where('crop_id','=',Application::find($tests->app_id)->crops->name->id)
            ->get();
        $nds_type = Nds::getType( Application::find($tests->app_id)->crops->name->nds->type_id).' '.Application::find($tests->app_id)->crops->name->nds->number;
        return view('tests.show', [
            'decision' => $tests,
            'nds_type'=>$nds_type,
            'indicators'=>$indicators,
            'qrCode' => $qrCode
        ]);
    }

    public function my_view($id)
    {
        $tests = TestPrograms::with('application.crops')
            ->with('application.decision.director')
            ->with('application.crops.name')
            ->with('application.crops.name.nds')
            ->with('application.crops.type')
            ->with('application.crops.generation')
            ->with('application')
            ->find($id);
        // Generate QR code if available
        $qrCode = null;
        if ($tests->application->decision->status == Decision::STATUS_ACCEPTED) {
            $url = route('tests.show', $id);
            $qrCode = QrCode::size(100)->generate($url);
        }
        $indicators = Indicator::where('crop_id','=',Application::find($tests->app_id)->crops->name->id)
            ->get();
        $nds_type = Nds::getType( Application::find($tests->app_id)->crops->name->nds->type_id).' '.Application::find($tests->app_id)->crops->name->nds->number;
        return view('tests.my_view', [
            'decision' => $tests,
            'nds_type'=>$nds_type,
            'indicators'=>$indicators,
            'qrCode' => $qrCode
        ]);
    }

}
