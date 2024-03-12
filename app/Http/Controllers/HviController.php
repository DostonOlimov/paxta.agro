<?php

namespace App\Http\Controllers;

use App\Models\AktAmount;
use App\Models\Area;
use App\Models\ClampData;
use App\Models\Dalolatnoma;
use App\Models\GinBalles;
use Illuminate\Support\Carbon;
use App\Models\HviFiles;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DefaultModels\MyTableReader;
use App\Jobs\ProcessFile;


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

        $apps= Region::with('organization');

        if ($user->role == \App\Models\User::STATE_EMPLOYEE) {
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

            $gin_balles = GinBalles::whereHas('dalolatnoma.test_program.application.organization', function ($query) use ($state_id) {
                $query->whereHas('city', function ($query) use ($state_id) {
                    $query->where('state_id', '=', $state_id);
                });
            })->whereHas('dalolatnoma.clamp_data', function ($query) {
                $query->havingRaw('COUNT(*) != dalolatnoma.toy_count');
            })->get();

            if (!$hvi) {
                $filePath = $file->storeAs('uploads/'.$user->state_id, $file->getClientOriginalName());

                HviFiles::create([
                    'state_id' => $state_id,
                    'path' => $filePath,
                    'user_id' => $user->id,
                    'date' => date('Y-m-d'), // Using now() helper to get the current date
                    'count' => $count,
                ]);
                foreach ($gin_balles as $balles){
                    $state = Region::find($state_id);
                    $gin_id = 1000 * $state->clamp_id + $balles->dalolatnoma->test_program->application->prepared->kod;
                    ProcessFile::dispatch([
                        'path' => $filePath,
                        'balles' => $balles,
                        'count' => $count,
                        'gin_id' => $gin_id,
                    ]);
                }
            } elseif ($hvi->count != $count) {
                $filePath = $file->storeAs('uploads/'.$user->state_id, $file->getClientOriginalName());
                $hvi->path = $filePath;
                $hvi->user_id = $user->id;
                $hvi->date = date('Y-m-d');
                $hvi->count = $count;
                $hvi->save();
                foreach ($gin_balles as $balles){
                    $state = Region::find($state_id);
                    $gin_id = 1000 * $state->clamp_id + $balles->dalolatnoma->test_program->application->prepared->kod;
                    ProcessFile::dispatch([
                        'path' => $filePath,
                        'balles' => $balles,
                        'count' => $count,
                        'gin_id' => $gin_id,
                    ]);
                }
            } else {
                $hvi->user_id = $user->id;
                $hvi->date = date('Y-m-d');
                $hvi->save();
            }

        }

        return redirect('hvi/list')->with('message', 'Successfully Submitted');
    }

}
