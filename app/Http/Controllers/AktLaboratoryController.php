<?php

namespace App\Http\Controllers;

use App\Models\AktAmount;
use App\Models\ClampData;
use App\Models\Dalolatnoma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use XBase\TableReader;

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
        return view('akt_laboratory.search', compact('tests','from','till','city','crop'));
    }
    //index
    public function add($id)
    {
        $test = Dalolatnoma::find($id);
        return view('akt_laboratory.add', compact('test'));
    }

    public function store(Request $request)
    {
        $id = $request->input('id');
        $dalolatnoma = Dalolatnoma::find($id);
        $gin_id = 0;
        $from_number = 0;
        $to_number = 0;
        if($dalolatnoma){
            $gin_id = 1000 * $dalolatnoma->test_program->application->prepared->region->clamp_id + $dalolatnoma->test_program->application->prepared->kod;
            $from_number = $dalolatnoma->gin_ball->from_number;
            $to_number = $dalolatnoma->gin_ball->to_number;
        }
        if ($request->hasFile('file')) {
            $file = $request->file('file');
        }
              $table = new TableReader($file);

              while ($record = $table->nextRecord()) {
              if($record->gin_id == $gin_id){
                  if($record->gin_bale >= $from_number and $record->gin_bale <= $to_number){
                      $data = ClampData::where('gin_id',$record->gin_id)
                          ->where('gin_bale',$record->gin_bale)
                          ->first();
                      if(!$data){
                          $data = new ClampData();
                          $data->dalolatnoma_id = $id;
                          $data->gin_id = $record->gin_id;
                          $data->gin_bale = $record->gin_bale;
                          $data->lot_number = $record->lot_num;
                          $data->weight = $record->weight;
                          $data->selection = $record->selection;
                          $data->date_recvd = $record->date_recvd;
                          $data->time_recvd = $record->time_recvd;
                          $data->date_hvid = $record->date_hvid;
                          $data->time_hvid = $record->time_hvid;
                          $data->date_class = $record->date_class;
                          $data->time_class = $record->time_class;
                          $data->classer_id = $record->classer_id;
                          $data->qual_ctrl = $record->qual_ctrl;
                          $data->cutout = $record->cutout;
                          $data->reclass = $record->reclass;
                          $data->times_hvid = $record->times_hvid;
                          $data->attempts = $record->attempts;
                          $data->status = $record->status;
                          $data->correction = $record->correction;
                          $data->croptype = $record->croptype;
                          $data->firstgrade = $record->firstgrade;
                          $data->grade = $record->grade;
                          $data->sort = $record->sort;
                          $data->class = $record->class;
                          $data->staple = $record->staple;
                          $data->mic = $record->mic;
                          $data->leaf = $record->leaf;
                          $data->ext_matter = $record->ext_matter;
                          $data->remarks = $record->remarks;
                          $data->strength = $record->strength;
                          $data->color_gr = $record->color_gr;
                          $data->color_rd = $record->color_rd;
                          $data->color_b = $record->color_b;
                          $data->trash = $record->trash;
                          $data->uniform = $record->uniform;
                          $data->fiblength = $record->fiblength;
                          $data->elongation = $record->elongation;
                          $data->sfi = $record->sfi;
                          $data->temperatur = $record->temperatur;
                          $data->humidity = $record->humidity;
                          $data->hvi_num = $record->hvi_num;
                          $data->save();
                    }
                  }
              }

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
