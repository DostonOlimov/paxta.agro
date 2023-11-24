<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ClampData;
use App\Models\CropData;
use App\Models\CropProductionType;
use App\Models\Dalolatnoma;
use App\Models\Decision;
use App\Models\FinalResult;
use App\Models\Indicator;
use App\Models\Laboratories;
use App\Models\Nds;
use App\Models\ProductionType;
use App\Models\Sertificate;
use App\Models\TestProgramIndicators;
use App\Models\TestPrograms;
use App\Services\AttachmentService;
use App\tbl_activities;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinalResultsController extends Controller
{
    private $attachmentService;

    public function __construct(AttachmentService $attachmentService)
    {
        $this->attachmentService = $attachmentService;
    }
    //search
    public function search(Request $request)
    {
        $user = Auth::user();
        $city = $request->input('city');
        $crop = $request->input('crop');
        $from = $request->input('from');
        $till = $request->input('till');

        $apps= Dalolatnoma::with('test_program')
            ->with('test_program.application')
            ->with('test_program.application.decision')
            ->with('test_program.application.crops.name')
            ->with('test_program.application.crops.type')
            ->with('test_program.application.organization')
            ->with('test_program.final_result');
        if ($user->role == \App\Models\User::STATE_EMPLOYEE) {
            $user_city = $user->state_id;
            $apps = $apps->whereHas('test_program.application.organization', function ($query) use ($user_city) {
                $query->whereHas('city', function ($query) use ($user_city) {
                    $query->where('state_id', '=', $user_city);
                });
            });
        }
        if ($from && $till) {
            $fromTime = join('-', array_reverse(explode('-', $from)));
            $tillTime = join('-', array_reverse(explode('-', $till)));
            $apps->whereHas('test_program.application', function ($query) use ($fromTime,$tillTime) {
                $apps = $query->whereDate('date', '>=', $fromTime)
                    ->whereDate('date', '<=', $tillTime);
            });
        }
        if ($city) {
            $apps = $apps->whereHas('test_program.application.organization', function ($query) use ($city) {
                $query->whereHas('city', function ($query) use ($city) {
                    $query->where('state_id', '=', $city);
                });
            });
        }
        if ($crop) {
            $apps = $apps->whereHas('test_program.application.crops', function ($query) use ($crop) {
                $query->where('name_id', '=', $crop);
            });
        }
        $apps->when($request->input('s'), function ($query, $searchQuery) {
            $query->where(function ($query) use ($searchQuery) {
                if (is_numeric($searchQuery)) {
                    $query->whereHas('test_program.application', function ($query) use ($searchQuery) {
                        $query->where('app_number', $searchQuery);
                    });
                } else {
                    $query->whereHas('test_program.application.crops.name', function ($query) use ($searchQuery) {
                        $query->where('name', 'like', '%' . addslashes($searchQuery) . '%');
                    })->orWhereHas('test_program.application.crops.type', function ($query) use ($searchQuery) {
                        $query->where('name', 'like', '%' . addslashes($searchQuery) . '%');
                    })->orWhereHas('test_program.application.crops.generation', function ($query) use ($searchQuery) {
                        $query->where('name', 'like', '%' . addslashes($searchQuery) . '%');
                    });

                }
            });
        });

        $tests = $apps->latest('id')
            ->paginate(50)
            ->appends(['s' => $request->input('s')])
            ->appends(['till' => $request->input('till')])
            ->appends(['from' => $request->input('from')])
            ->appends(['city' => $request->input('city')])
            ->appends(['crop' => $request->input('crop')]);
        return view('final_results.search', compact('tests','from','till','city','crop'));
    }
    //index
    public function add($id)
    {
        $test = TestPrograms::find($id);
        $makers = DB::table('decision_makers')->get();
        $types = FinalResult::getType();
        return view('final_results.add', compact('makers','test','types'));
    }

    //list
    public function list()
    {
        $title = 'Normativ hujjatlar';
        $testss = Nds::with('crops')->orderBy('id')->get();
        return view('final_results.list', compact('decisions','title'));
    }

    //  store
    public function store(Request $request)
    {
        $userA = Auth::user();
        $this->authorize('create', Application::class);
        $test_id = $request->input('test_id');
        $given_certificate = $request->input('given_certificate');
        $number = $request->input('number');
        $maker = $request->input('maker');

        $reestr_number = $request->input('reestr_number');
        $type = $given_certificate == 1 ? 2 : $request->input('type');
        $folder_number = $given_certificate == 0 ? $request->input('folder_number') : null;
        $comment = $given_certificate == 0 ? $request->input('comment') : null;

        $test = new FinalResult();
        $test->test_program_id = $test_id;
        $test->number = $number;
        $test->date = join('-', array_reverse(explode('-', $request->input('date'))));
        $test->type = $type;
        $test->folder_number = $folder_number;
        $test->comment = $comment;
        $test->maker = $maker;
        $test->save();

        if ($request->hasFile('reason-file')) {
            $this->attachmentService->upload($request->file('reason-file'), $test);
        }
        if($given_certificate == 1)
        {
            $cer = new Sertificate();
            $cer->final_result_id = $test->id;
            $cer->reestr_number = $reestr_number;
            $cer->given_date = join('-', array_reverse(explode('-', $request->input('given_date'))));;;
            $cer->save();
        }
        $active = new tbl_activities;
        $active->ip_adress = $_SERVER['REMOTE_ADDR'];
        $active->user_id = $userA->id;
        $active->action_id = $test->id;
        $active->action_type = 'new_result';
        $active->action = "Yakuniy natijalar qo'shildi";
        $active->time = date('Y-m-d H:i:s');
        $active->save();

        return redirect('/final_results/search')->with('message', 'Successfully Submitted');


    }

    public function edit($id)
    {
        $userA = Auth::user();
        $result = FinalResult::find($id);
        $test = TestPrograms::find($result->test_program_id);
        $certificate =  Sertificate::where('final_result_id','=',$result->id)->first() ;

        return view('final_results.edit', compact('test','result','certificate'));
    }


    // application update

    public function update($id, Request $request)
    {
        $userA = Auth::user();
        $result = FinalResult::find($id);
        $test = TestPrograms::find($result->test_program_id);
        $certificate =  Sertificate::where('final_result_id','=',$result->id)->first() ;

        $number = $request->input('number');
        $reestr_number = $request->input('reestr_number');
        $type = $certificate ? 2 : $request->input('type');
        $folder_number = !$certificate ? $request->input('folder_number') : null;
        $comment = !$certificate ? $request->input('comment') : null;

        $result->number = $number;
        $result->date = join('-', array_reverse(explode('-', $request->input('date'))));
        $result->type = $type;
        $result->folder_number = $folder_number;
        $result->comment = $comment;
        $result->save();
        if(!$certificate){
            $cer = Sertificate::find($certificate->id);
            $cer->reestr_number = $reestr_number;
            $cer->given_date = join('-', array_reverse(explode('-', $request->input('given_date'))));;;
            $cer->save();
        }
        if ($request->hasFile('reason-file')) {
            $this->attachmentService->upload($request->file('reason-file'), $result);
        }
        $active = new tbl_activities;
        $active->ip_adress = $_SERVER['REMOTE_ADDR'];
        $active->user_id = $userA->id;
        $active->action_id = $result->id;
        $active->action_type = 'edit_final_result';
        $active->action = "Yakuniy natijalar o'zgartirildi";
        $active->time = date('Y-m-d H:i:s');
        $active->save();
        return redirect('/final_results/search')->with('message', 'Successfully Updated');

    }


    public function destory($id)
    {
        Decision::destroy($id);
        return redirect('final_results/search')->with('message', 'Successfully Deleted');
    }
    public function view($id)
    {
        $tests = ClampData::where('dalolatnoma_id',$id)->get()->toArray();
        $data1 = [];
        if($tests){
            $data1 =  array_chunk($tests, ceil(count($tests)/4));
        }
        return view('final_results.show', [
            'results' => $data1,
        ]);
    }

}
