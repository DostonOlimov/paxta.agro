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
           ;
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
        $dalolatnoma = Dalolatnoma::find($id);
        $tests = ClampData::where('dalolatnoma_id',$id)->count();
        $data_count = FinalResult::where('dalolatnoma_id',$id)->count();

        if($data_count == 0){
            $counts = ClampData::select('sort', 'class',
                \DB::raw('count(*) as count'),
                DB::raw('SUM(akt_amount.amount) as total_amount'),
                DB::raw('AVG(clamp_data.mic) as mic'),
                DB::raw('AVG(clamp_data.staple) as staple'),
                DB::raw('AVG(clamp_data.strength) as strength'),
                DB::raw('AVG(clamp_data.uniform) as uniform'),
                DB::raw('AVG(clamp_data.humidity) as humidity')
            )
                ->join('akt_amount', function($join) {
                    $join->on('akt_amount.shtrix_kod', '=', 'clamp_data.gin_bale')
                        ->on('akt_amount.dalolatnoma_id', '=', 'clamp_data.dalolatnoma_id');
                })
                ->where('clamp_data.dalolatnoma_id',$id)
                ->where('akt_amount.dalolatnoma_id', $id)
                ->groupBy('sort', 'class')
                ->get();
            foreach($counts as $count){
                $result = new FinalResult();
                $result->dalolatnoma_id = $id;
                $result->test_program_id = $id;
                $result->sort = $count->sort;
                $result->class = $count->class;
                $result->count = $count->count;
                $result->amount = $count->total_amount;
                $result->mic = $count->mic;
                $result->staple = $count->staple;
                $result->strength = $count->strength;
                $result->uniform = $count->uniform;
                $result->humidity = $count->humidity;
                $result->save();
            }
        }


        return view('final_results.add', [
            'results' => $tests,
            'counts' => FinalResult::where('dalolatnoma_id',$id)->get(),
            'dalolatnoma'=>$dalolatnoma,
        ]);
    }

    //index
    public function add_view($id)
    {
        $dalolatnoma = Dalolatnoma::find($id);
        $tests = ClampData::where('dalolatnoma_id',$id)->count();
        $data_count = FinalResult::where('dalolatnoma_id',$id)->count();

        if($data_count == 0){
            $counts = ClampData::select('sort', 'class',
                \DB::raw('count(*) as count'),
                DB::raw('SUM(akt_amount.amount) as total_amount'),
                DB::raw('AVG(clamp_data.mic) as mic'),
                DB::raw('AVG(clamp_data.staple) as staple'),
                DB::raw('AVG(clamp_data.strength) as strength'),
                DB::raw('AVG(clamp_data.uniform) as uniform'),
                DB::raw('AVG(clamp_data.humidity) as humidity')
            )
                ->join('akt_amount', function($join) {
                    $join->on('akt_amount.shtrix_kod', '=', 'clamp_data.gin_bale')
                        ->on('akt_amount.dalolatnoma_id', '=', 'clamp_data.dalolatnoma_id');
                })
                ->where('clamp_data.dalolatnoma_id',$id)
                ->where('akt_amount.dalolatnoma_id', $id)
                ->groupBy('sort', 'class')
                ->get();
            foreach($counts as $count){
                $result = new FinalResult();
                $result->dalolatnoma_id = $id;
                $result->test_program_id = $id;
                $result->sort = $count->sort;
                $result->class = $count->class;
                $result->count = $count->count;
                $result->amount = $count->total_amount;
                $result->mic = $count->mic;
                $result->staple = $count->staple;
                $result->strength = $count->strength;
                $result->uniform = $count->uniform;
                $result->humidity = $count->humidity;
                $result->save();
            }
        }


        return view('final_results.add_view', [
            'results' => $tests,
            'counts' => FinalResult::where('dalolatnoma_id',$id)->get(),
            'dalolatnoma'=>$dalolatnoma,
        ]);
    }

    public function add2($id)
    {

        return view('final_results.add2', [
            'id'=>$id,
        ]);
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
        $id = $request->input('id');
        $given_certificate = $request->input('given_certificate');
        $reestr_number = $request->input('reestr_number');
        $cer = new Sertificate();
        $cer->final_result_id = $id;
        $cer->reestr_number = $reestr_number;
        $cer->given_date = join('-', array_reverse(explode('-', $request->input('given_date'))));;;
        $cer->save();

        if ($request->hasFile('reason-file')) {
            $this->attachmentService->upload($request->file('reason-file'), $cer);
        }
        $result_id = FinalResult::find($id);
        return redirect('/final_results/add/'.$result_id->dalolatnoma_id)->with('message', 'Successfully Submitted');


    }

    public function destory($id)
    {
        Decision::destroy($id);
        return redirect('final_results/search')->with('message', 'Successfully Deleted');
    }
    public function view($id)
    {
        $dalolatnoma = Dalolatnoma::find($id);
        $tests = ClampData::where('dalolatnoma_id',$id)->get()->toArray();
        $data1 = [];
        if($tests){
            $data1 =  array_chunk($tests, ceil(count($tests)/4));
        }

        $counts = ClampData::select('sort', 'class', \DB::raw('count(*) as count'))
            ->groupBy('sort', 'class')
            ->where('dalolatnoma_id',$id)
            ->get();
        $mic = ClampData::where('dalolatnoma_id',$id)->avg('mic');
        $length = ClampData::where('dalolatnoma_id',$id)->avg('fiblength');
        $strength = ClampData::where('dalolatnoma_id',$id)->avg('strength');
        $uniform = ClampData::where('dalolatnoma_id',$id)->avg('uniform');

        return view('final_results.show', [
            'results' => $data1,
            'counts' => $counts,
            'dalolatnoma'=>$dalolatnoma,
            'mic' => $mic,
            'length' => $length,
            'strength' => $strength,
            'uniform' => $uniform
        ]);
    }
    public function update($id)
    {
       $result = FinalResult::find($id);

        if ($result) {
            $counts = ClampData::select('sort', 'class',
                \DB::raw('count(*) as count'),
                DB::raw('SUM(akt_amount.amount) as total_amount'),
                DB::raw('AVG(clamp_data.mic) as mic'),
                DB::raw('AVG(clamp_data.staple) as staple'),
                DB::raw('AVG(clamp_data.strength) as strength'),
                DB::raw('AVG(clamp_data.uniform) as uniform'),
                DB::raw('AVG(clamp_data.humidity) as humidity')
            )
                ->join('akt_amount', function($join) {
                    $join->on('akt_amount.shtrix_kod', '=', 'clamp_data.gin_bale')
                        ->on('akt_amount.dalolatnoma_id', '=', 'clamp_data.dalolatnoma_id');
                })
                ->where('clamp_data.dalolatnoma_id', $result->dalolatnoma_id)
                ->where('akt_amount.dalolatnoma_id', $result->dalolatnoma_id)
                ->where('clamp_data.sort', $result->sort)
                ->where('clamp_data.class', $result->class)
                ->groupBy('sort', 'class')
                ->get();

            foreach ($counts as $count) {
                $result->count = $count->count;
                $result->amount = $count->total_amount;
                $result->mic = $count->mic;
                $result->staple = $count->staple;
                $result->strength = $count->strength;
                $result->uniform = $count->uniform;
                $result->humidity = $count->humidity;
                $result->save();
            }
        }
        return redirect('/final_results/add/'.$result->dalolatnoma_id)->with('message', 'Successfully Submitted');

    }


}
