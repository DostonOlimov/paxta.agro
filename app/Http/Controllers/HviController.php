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

            if (!$hvi || $hvi->updated_at->diffInMinutes($currentTime) > 5) {
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
        return GinBalles::with('dalolatnoma.test_program.application.organization.city')
            ->with('dalolatnoma.clamp_data')
            ->whereHas('dalolatnoma.test_program.application.organization.city', function ($query) use ($state_id) {
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
