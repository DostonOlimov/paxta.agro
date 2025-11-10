<?php

namespace App\Http\Controllers;

use App\Filters\V1\ApplicationFilter;
use App\Models\Application;
use App\Models\Decision;
use App\Models\Laboratories;
use App\Models\Nds;
use App\Models\DefaultModels\tbl_activities;
use App\Models\TestPrograms;
use App\Models\User;
use App\Services\SearchService;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DecisionController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth')->except('my_view');
    }

    //search
    public function search(Request $request, ApplicationFilter $filter,SearchService $service)
    {
        try {
            $names = getCropsNames();
            $states = getRegions();
            $years = getCropYears();

            return $service->search(
                $request,
                $filter,
                Application::class,
                [
                    'crops',
                    'organization',
                    'decision',
                    'tests',
                    'prepared',
                    'crops.name',
                    'organization.area.region'
                ],
                compact('names', 'states', 'years'),
                'decision.search',
                [Application::STATUS_ACCEPTED, Application::STATUS_FINISHED]
            );

        } catch (\Throwable $e) {
            // Log the error for debugging
            \Log::error($e);
            return $this->errorResponse('An unexpected error occurred', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function add($id)
    {
        $app = Application::findOrFail($id); // Use findOrFail for error handling
        $user = Auth::user();

        // Retrieve Nds record associated with the crop name ID
        $nd = Nds::where('crop_id', $app->crops->name->id)->first();

        if ($nd) {
            // Filter laboratories based on the user's branch or state
            $laboratories = ($user->branch_id == User::BRANCH_STATE)
                ? Laboratories::whereHas('city', function($query) use ($user,$app) {
                    $query->where('state_id', $user->state_id)
                        ->where('crop_type', '=' ,  $app->crops->name->id);
                })->get()
                : Laboratories::with('city')->where('crop_type', '=' ,  $app->crops->name->id)->get();

            // Fetch directors with the specified role
            $directors = User::where('role', 55)->get();

            return view('decision.add', compact('app', 'nd', 'directors', 'laboratories'));
        }

        return redirect()->route('nds.list')->with('message', 'NDS not found');
    }


    //  store
    public function store(Request $request)
    {
        $user = Auth::user();
        $this->authorize('create', User::class);

        $app_id = $request->input('app_id');
        $number = $request->input('number');
        $date = $request->input('dob') ? date('Y-m-d', strtotime($request->input('dob'))) : null;
        $laboratory_id = $request->input('laboratory_id');

        // Create new decision
        $decision = Decision::create([
            'app_id'       => $app_id,
            'director_id'  => 42, // Hardcoded, but ideally fetched dynamically
            'number'       => $number,
            'laboratory_id'=> $laboratory_id,
            'created_by'   => $user->id,
            'date'         => $date,
            'status'       => Decision::STATUS_NEW,
        ]);

        // Log activity
        tbl_activities::create([
            'ip_adress'   => request()->ip(), // Use Laravel helper for IP
            'user_id'     => $user->id,
            'action_id'   => $decision->id,
            'action_type' => 'new_decision',
            'action'      => "Yangi buyruq qo'shildi",
            'time'        => now(),
        ]);

        // Create test program entry
        TestPrograms::create([
            'app_id'      => $app_id,
            'director_id' => 42, // Hardcoded, but same suggestion as above
        ]);

        return redirect()->route('decision.search')->with('message', 'Successfully Submitted');
    }

    public function destory($id)
    {
        $this->authorize('edit', User::class);
        Decision::destroy($id);
        TestPrograms::destroy($id);
        return redirect('decision/search')->with('message', 'Successfully Deleted');
    }
    public function view($id)
    {
        $decision = Decision::with('director')
            ->with('application.organization')
            ->with('application.crops')
            ->with('application.crops.name')
            ->with('application.crops.name.nds')
            ->with('application.crops.type')
            ->with('application.crops.generation')
            ->with('application')
            ->with('laboratory')
            ->find($id);
        // Generate QR code if available
        $qrCode = null;
        if ($decision->status == Decision::STATUS_ACCEPTED) {
            $url = route('decision.show', $id);
            $qrCode = QrCode::size(100)->generate($url);
        }

        $nds_type = Nds::getType(Application::find($decision->app_id)->crops->name->nds->type_id);
        return view('decision.show', [
            'decision' => $decision,
            'nds_type'=>$nds_type,
            'qrCode' => $qrCode
        ]);
    }

    public function my_view($id)
    {
        $decision = Decision::with('director')
            ->with('application.organization')
            ->with('application.crops')
            ->with('application.crops.name')
            ->with('application.crops.name.nds')
            ->with('application.crops.type')
            ->with('application.crops.generation')
            ->with('application')
            ->with('laboratory')
            ->find($id);
        // Generate QR code if available
        $qrCode = null;
        if ($decision->status == Decision::STATUS_ACCEPTED) {
            $url = route('decision.show', $id);
            $qrCode = QrCode::size(100)->generate($url);
        }

        $nds_type = Nds::getType(Application::find($decision->app_id)->crops->name->nds->type_id);
        return view('decision.my_view', [
            'decision' => $decision,
            'nds_type'=>$nds_type,
            'qrCode' => $qrCode
        ]);
    }

    public function send($id, Request $request)
    {
        // Get authenticated user
        $user = Auth::user();

        // Find the test program by ID
        $test = TestPrograms::firstWhere('app_id',$id);
        $app = Application::find($id);
        $decision = Decision::firstWhere('app_id',$id);

        // Authorize the send action
        $this->authorize('send', $app);


        // Update decision
        $decision->update([
            'status' => Decision::STATUS_ACCEPTED
        ]);

        // Redirect with success message
        return redirect('decision/search?page=' . $request->input('page'))->with('message', 'Successfully Submitted');
    }

}
