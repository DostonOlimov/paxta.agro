<?php

namespace App\Http\Controllers;

use App\Models\AktAmount;
use App\Models\ClampData;
use App\Models\Dalolatnoma;
use App\Models\GinBalles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use XBase\TableReader;
use App\Jobs\ProcessFile;


class AktLaboratoryController extends Controller
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
        return view('akt_laboratory.search', compact('tests','from','till','city','crop'));
    }
    //index
    public function add($id)
    {
        $test = Dalolatnoma::with('gin_balles')->find($id);
        return view('akt_laboratory.add', compact('test'));
    }

    public function store(Request $request)
    {
        $id = $request->input('id');
        $user = Auth::user();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->storeAs('uploads/'.$user->state_id, $file->getClientOriginalName());

            ProcessFile::dispatch(['path'=>$filePath,'id'=>$id]);
            Log::info('ProcessFile job dispatched successfully.');die();
        }

        return redirect()->route('akt_laboratory.view',$id)->with('success','Role muvaffaqatli yaratildi.');

    }
    public function edit($id)
    {
        $tests = AktAmount::where('dalolatnoma_id',$id)->get()->toArray();

        $data1 =  array_chunk($tests, ceil(count($tests)/4));

        return view('akt_laboratory.edit', [
            'results' => $data1,
        ]);
    }


    public function save_amount(Request $request)
    {

        $id = $request->input('id');
        $amount = $request->input('amount');
        $result = AktAmount::find($id);
        if($amount > 0 and $amount < 1000){
            $result->amount = $amount;
        }
        $result->save();


        return response()->json(['message' => 'Answer saved successfully']);
    }
    public function view($id)
    {
        $tests = ClampData::where('dalolatnoma_id',$id)->get();
        return view('akt_laboratory.show', [
            'results' => $tests,
            'id' => $id
        ]);
    }

}
