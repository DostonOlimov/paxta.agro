<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessFile2;
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
use PhpOffice\PhpSpreadsheet\IOFactory;
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

    //add LClass data
    public function addLclass($id)
    {
        return view('hvi.add2', compact('id'));
    }

    //store LClass data
    public function storeLclass (Request $request)
    {
        $state_id = $request->input('id');
        $user = Auth::user();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $hvi = HviFiles::where('state_id', $state_id)->first();


            $gin_balles = $this->getGinBalles($state_id);

            $filePath = $this->storeFile($file, $state_id);
            $this->processGinBallesForLClass($gin_balles, $filePath, $state_id);
            $this->updateHvi($hvi, $user->id);

        }
        return redirect('hvi/list')->with('message', 'Successfully Submitted');
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

//
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
//
//            while($record = $table->nextRecord()){
//
//                if ($record->gin_id == $gin_id and $record->gin_bale >= $balles->from_number and $record->gin_bale <= $balles->to_number) {
//
//                    if (!in_array($record->gin_bale, $clampedData) and $record->mic) {
//
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
//            dd($myData);
//            if (!empty( $myData )) {
//                // Perform bulk insertion
//                ClampData::insert($myData );
//            }
//            dd($myData);

            ProcessFile::dispatch([
                'path' => $filePath,
                'balles' => $balles,
                'gin_id' => $gin_id,
            ]);
        }
    }
    private function processGinBallesForLClass($gin_balles, $filePath, $state_id)
    {
        foreach ($gin_balles as $balles) {
            if($balles->dalolatnoma->clamp_data()->count() != 0){
                $state = Region::find($state_id);
                $gin_id = 1000 * $state->clamp_id + $balles->dalolatnoma->test_program->application->prepared->kod;

                // Load the spreadsheet file
//                $file = storage_path('app/' . $filePath);
//                $spreadsheet = IOFactory::load($file);
//                $worksheet = $spreadsheet->getActiveSheet();
//
//                // Parse Excel data into an array
//                $excelData = $this->parseWorksheet($worksheet);
//
//                // Retrieve existing clamped data
//                $clampedData = $this->getClampedData($balles,$gin_id);
//
//                // Process the data and prepare for insertion or updates
//                [$dataToInsert, $dataToUpdate] = $this->processExcelData($excelData, $clampedData,$gin_id,$balles);
//
//                // Perform bulk insertion for new records
//                if (!empty($dataToInsert)) {
//                    ClampData::insert($dataToInsert);
//                }
//
//                // Update existing records
//                foreach ($dataToUpdate as $update) {
//                    $this->updateClampedData($update,$balles);
//                }

                ProcessFile2::dispatch([
                    'path' => $filePath,
                    'balles' => $balles,
                    'gin_id' => $gin_id,
                ]);
            }
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


    //sdafaf

//    /**
//     * Parse the worksheet to extract data.
//     */
//    private function parseWorksheet($worksheet)
//    {
//        $data = [];
//        foreach ($worksheet->getRowIterator() as $row) {
//            $rowData = [];
//            foreach ($row->getCellIterator() as $cell) {
//                $rowData[] = $cell->getValue();
//            }
//            $data[] = $rowData;
//        }
//        return $data;
//    }
//
//    /**
//     * Retrieve clamped data from the database.
//     */
//    private function getClampedData($balles,$gin_id)
//    {
//        return ClampData::whereIn('gin_bale', range($balles->from_number, $balles->to_number))
//            ->where('gin_id', $gin_id)
//            ->where('dalolatnoma_id', $balles->dalolatnoma_id)
//            ->pluck('gin_bale')
//            ->toArray();
//    }
//
//    /**
//     * Process Excel data to prepare for insertion and updates.
//     */
//    private function processExcelData(array $excelData, array $clampedData, $gin_id, $balles)
//    {
//        $dataToInsert = [];
//        $dataToUpdate = [];
//
//        foreach ($excelData as $data) {
//            $ginId = $data[0] ?? null;
//            $ginBale = $data[1] ?? null;
//
//            // Skip rows with invalid gin_id or gin_bale
//            if (!$ginId || !$ginBale) {
//                continue;
//            }
//
//            if ($ginId == $gin_id && $ginBale >= $balles->from_number && $ginBale <= $balles->to_number) {
//                if (!in_array($ginBale, $clampedData)) {
//                    $dataToInsert[] = $this->prepareInsertData($data,$balles);
//                } else {
//                    $dataToUpdate[] = $this->prepareUpdateData($data);
//                }
//            }
//        }
//
//        return [$dataToInsert, $dataToUpdate];
//    }
//
//    /**
//     * Prepare data for insertion.
//     */
//    private function prepareInsertData(array $data,$balles)
//    {
//        return [
//            'dalolatnoma_id' => $balles->dalolatnoma_id,
//            'gin_id' => $data[0],
//            'gin_bale' => $data[1],
//            'lot_number' => $data[2],
//            'weight' => null,
//            'selection' => $data[12],
//            'date_class' => $data[4],
//            'time_class' => $data[5],
//            'classer_id' => $data[10],
//            'sort' => $data[6],
//            'class' => $data[7],
//        ];
//    }
//
//    /**
//     * Prepare data for updating existing records.
//     */
//    private function prepareUpdateData(array $data)
//    {
//        return [
//            'gin_id' => $data[0],
//            'gin_bale' => $data[1],
//            'sort' => $data[6],
//            'class' => $data[7],
//            'classer_id' => $data[10],
//        ];
//    }
//
//    /**
//     * Update existing clamped data in the database.
//     */
//    private function updateClampedData(array $data,$balles)
//    {
//        $clamp = ClampData::where('gin_bale', $data['gin_bale'])
//            ->where('gin_id', $data['gin_id'])
//            ->where('dalolatnoma_id', $balles->dalolatnoma_id)
//            ->first();
//
//        if ($clamp) {
//            $clamp->update([
//                'sort' => $data['sort'],
//                'class' => $data['class'],
//                'classer_id' => $data['classer_id'],
//            ]);
//        }
//    }

}
