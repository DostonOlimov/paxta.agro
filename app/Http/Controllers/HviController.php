<?php

namespace App\Http\Controllers;

use App\Models\AktAmount;
use App\Models\Area;
use App\Models\ClampData;
use Carbon\Carbon;
use App\Models\GinBalles;
use Illuminate\Support\Facades\DB;
use App\Models\HviFiles;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DefaultModels\MyTableReader;
use App\Jobs\ProcessFile;
use App\Models\User;
use XBase\TableReader;

class HviController extends Controller
{
    //search
    public function list(Request $request)
    {
        $user = Auth::user();
        $city = $request->input('city');
        $crop = $request->input('crop');
        $from = $request->input('from');
        $till = $request->input('till');

        $apps= Region::with('organization')
            ->with('hvi_file.user');

        if ($user->branch_id == User::BRANCH_STATE ) {
            $user_city = $user->state_id;
            $apps = $apps->where('id', '=', $user_city);
        }

        $states = $apps->get();

        return view('hvi.list', compact('states','from','till','city','crop'));
    }
    //index
    public function add($id)
    {
        return view('hvi.add', compact('id'));
    }

    public function store(Request $request)
    {
        $state_id = $request->input('id');
        $user = Auth::user();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $hvi = HviFiles::where('state_id', $state_id)->first();

            $table = new MyTableReader($file);
            $count = $table->getTotalCount();

            $gin_balles = $this->getGinBalles($state_id);

            $currentTime = now();

            if (!$hvi || $hvi->updated_at->diffInMinutes($currentTime) > 5 || $user->role == 'admin') {
                $filePath = $this->storeFile($file, $state_id);
                $this->processGinBalles($gin_balles, $filePath, $count, $state_id);
                $this->saveOrUpdateHvi($hvi, $filePath, $user->id, $count,$state_id);
            } else {
                $this->updateHvi($hvi, $user->id);
            }
        }
        return redirect('hvi/list')->with('message', 'Successfully Submitted');
    }

    public function view($id)
    {
        $tests = ClampData::whereHas('dalolatnoma', function ($query) use ($id) {
            $query->whereHas('test_program', function ($query) use ($id) {
                $query->whereHas('application', function ($query) use ($id) {
                    $query->whereHas('organization', function ($query) use ($id) {
                        $query->whereHas('city', function ($query) use ($id) {
                            $query->where('state_id', '=', $id);
                        });
                    });
                });
            });
        })->paginate(100);

        return view('hvi.show', [
            'results' => $tests,
            'id' => $id
        ]);
    }

    private function getGinBalles($state_id)
    {
        return GinBalles::with('dalolatnoma.test_program.application.prepared')
            ->with('dalolatnoma.clamp_data')
            ->whereHas('dalolatnoma.test_program.application.prepared', function ($query) use ($state_id) {
                $query->where('state_id', '=', $state_id);
            })
            ->whereHas('dalolatnoma.clamp_data', function ($query) {
                $query->havingRaw('COUNT(*) != dalolatnoma.toy_count');
            })
            ->get();
    }

    private function storeFile($file, $state_id)
    {
        return $file->storeAs('uploads/' . $state_id, $file->getClientOriginalName());
    }

    private function processGinBalles($gin_balles, $filePath, $count, $state_id)
    {
        foreach ($gin_balles as $balles) {
            $state = Region::find($state_id);
            $gin_id = 1000 * $state->clamp_id + $balles->dalolatnoma->test_program->application->prepared->kod;


//            $file = storage_path('app/' . $filePath);
//            $table = new TableReader($file);
//
//            $clampedData = ClampData::whereIn('gin_bale', range($balles->from_number, $balles->to_number))
//                ->where('gin_id', $gin_id)
//                ->where('dalolatnoma_id', $balles->dalolatnoma_id)
//                ->pluck('gin_bale')
//                ->toArray();
//
//            $myData = [];
//
//
//            while($record = $table->nextRecord()){
//
//                if ($record->gin_id == $gin_id and $record->gin_bale >= $balles->from_number and $record->gin_bale <= $balles->to_number) {
//
//                    if (!in_array($record->gin_bale, $clampedData)) {
//                        if($record->gin_bale){
//                            $myData[] = [
//                                'dalolatnoma_id' => $balles->dalolatnoma_id,
//                                'gin_id' => $record->gin_id,
//                                'gin_bale' => $record->gin_bale,
//                                'lot_number' => $record->lot_num,
//                                'weight' => $record->weight,
//                                'selection' => $record->selection,
//                                'date_recvd' => $record->date_recvd,
//                                'time_recvd' => $record->time_recvd,
//                                'date_hvid' => $record->date_hvid,
//                                'time_hvid' => $record->time_hvid,
//                                'date_class' => $record->date_class,
//                                'time_class' => $record->time_class,
//                                'classer_id' => $record->classer_id,
//                                'qual_ctrl' => $record->qual_ctrl,
//                                'cutout' => $record->cutout,
//                                'reclass' => $record->reclass,
//                                'times_hvid' => $record->times_hvid,
//                                'attempts' => $record->attempts,
//                                'status' => $record->status,
//                                'correction' => $record->correction,
//                                'croptype' => $record->croptype,
//                                'firstgrade' => $record->firstgrade,
//                                'grade' => $record->grade,
//                                'sort' => $record->sort,
//                                'class' => $record->class,
//                                'staple' => $record->staple,
//                                'mic' => $record->mic,
//                                'leaf' => $record->leaf,
//                                'ext_matter' => $record->ext_matter,
//                                'remarks' => $record->remarks,
//                                'strength' => $record->strength,
//                                'color_gr' => $record->color_gr,
//                                'color_rd' => $record->color_rd,
//                                'color_b' => $record->color_b,
//                                'trash' => $record->trash,
//                                'uniform' => $record->uniform,
//                                'fiblength' => $record->fiblength,
//                                'elongation' => $record->elongation,
//                                'sfi' => $record->sfi,
//                                'temperatur' => $record->temperatur,
//                                'humidity' => $record->humidity,
//                                'hvi_num' => $record->hvi_num,
//                            ];
//                        }
//                    }
//                }
//            }
//            if (!empty( $myData )) {
//                // Perform bulk insertion
//                ClampData::insert($myData );
//            }
//            dd($myData);

            ProcessFile::dispatch([
                'path' => $filePath,
                'balles' => $balles,
                'count' => $count,
                'gin_id' => $gin_id,
            ]);
        }
    }

    private function saveOrUpdateHvi($hvi, $filePath, $userId, $count,$state_id)
    {
        if (!$hvi) {
            HviFiles::create([
                'state_id' => $state_id,
                'path' => $filePath,
                'user_id' => $userId,
                'date' => now(),
                'count' => $count,
            ]);
        } else {
            $hvi->path = $filePath;
            $hvi->user_id = $userId;
            $hvi->date = now();
            $hvi->count = $count;
            $hvi->save();
        }
    }

    private function updateHvi($hvi, $userId)
    {
        $hvi->user_id = $userId;
        $hvi->date = now();
        $hvi->save();
    }


}
