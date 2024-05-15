<?php

namespace App\Http\Controllers;

use App\Models\AktAmount;
use App\Models\Application;
use App\Models\Decision;
use App\Models\Dalolatnoma;
use App\Models\GinBalles;
use App\Models\Nds;
use App\Models\Sertificate;
use App\Models\TestPrograms;
use App\Models\DefaultModels\tbl_activities;
use App\Models\User;
use App\Rules\DifferentsShtrixKod;
use App\Rules\EqualToyCount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AktAmountController extends Controller
{
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
            ->with('test_program.application.organization');
        if ($user->branch_id == User::BRANCH_STATE ) {
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
            $apps->whereDate('date', '>=', $fromTime)
                    ->whereDate('date', '<=', $tillTime);
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

        $tests = $apps->withSum('akt_amount','amount')
            ->latest('id')
            ->paginate(50)
            ->appends(['s' => $request->input('s')])
            ->appends(['till' => $request->input('till')])
            ->appends(['from' => $request->input('from')])
            ->appends(['city' => $request->input('city')])
            ->appends(['crop' => $request->input('crop')]);

        return view('akt_amount.search', compact('tests','from','till','city','crop'));
    }
    //index
    public function add($id)
    {
        $test = TestPrograms::find($id);
        return view('akt_amount.add', compact('test'));
    }

    public function edit($id)
    {
        $tests = AktAmount::where('dalolatnoma_id',$id)->get()->toArray();
        if(!$tests){
            $amounts = [];
            $balls = GinBalles::where('dalolatnoma_id',$id)->get();
            foreach ($balls as $ball){
                for ($j = $ball->from_number; $j <= $ball->to_number; $j++) {
                    $amounts[] = [
                        'dalolatnoma_id' => $id,
                        'shtrix_kod' => $j,
                    ];
                }
            }
            DB::transaction(function () use ($amounts) {
                AktAmount::insert($amounts);
            });

            $tests = AktAmount::where('dalolatnoma_id',$id)->get()->toArray();
        }

        $data1 = array_chunk($tests, 50);


        return view('akt_amount.edit', [
            'data1' => $data1,
            'id'=>$id
        ]);
    }


    public function save_amount(Request $request)
    {

        $id = $request->input('id');
        $amount = (double)$request->input('amount');

        $result = AktAmount::find($id);

        if($amount > 0 and $amount < 1000){

            $result->amount = $amount;
            $result->save();

        }



        return response()->json(['message' => 'Answer saved successfully']);
    }
    public function view($id)
    {
        $tests = AktAmount::where('dalolatnoma_id',$id)->get()->toArray();
        $sum_amount = AktAmount::where('dalolatnoma_id',$id)->sum('amount');
        $count= AktAmount::where('dalolatnoma_id',$id)->count();
        $tara = optional(Dalolatnoma::find($id)->test_program->application->prepared)->tara;

        if($tests){
            $data1 =array_chunk($tests, 50);
        }

        return view('akt_amount.show', compact('data1','id','sum_amount','count','tara'));
    }

}
