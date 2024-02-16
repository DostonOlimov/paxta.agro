<?php

namespace App\Jobs;

use App\Models\ClampData;
use App\Models\Dalolatnoma;
use App\Models\GinBalles;
use App\Models\Region;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use XBase\TableReader;

class InsideQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $path;
    protected $id;
    protected $i;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($array)
    {
        $this->path = $array['path'];
        $this->id = $array['id'];
        $this->i = $array['i'];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $file = storage_path('app/' . $this->path);
        $dalolatnoma = Dalolatnoma::with('test_program.application.prepared.region')->find($this->id);
        $gin_balles = GinBalles::where('dalolatnoma_id',$this->id)->get();
        $gin_id = 0;
        $gin_id = 1000 * $dalolatnoma->test_program->application->prepared->region->clamp_id + $dalolatnoma->test_program->application->prepared->kod;

        foreach ($gin_balles as $balles){

            $clampedData = ClampData::whereIn('gin_bale', range($balles->from_number, $balles->to_number))
                ->where('gin_id', $gin_id)
                ->where('dalolatnoma_id', $this->id)
                ->get();

            $clampedDataMap = $clampedData->keyBy('gin_bale');

            for ($k = 500 * ($this->i - 1); $k <= 500 * $this->i; $k++) {

                $table = new TableReader($file);
                $record = $table->pickRecord($k);

                for ($i = $balles->from_number; $i <= $balles->to_number; $i++) {
                    $my_data = [];

                    if ($record->gin_id == $gin_id and $record->gin_bale == $i) {

                        if (!$clampedDataMap->has($i)) {
                            $my_data[] = [
                                'dalolatnoma_id' => $this->id,
                                'gin_id' => $record->gin_id,
                                'gin_bale' => $record->gin_bale,
                                'lot_number' => $record->lot_num,
                                'weight' => $record->weight,
                                'selection' => $record->selection,
                                'date_recvd' => $record->date_recvd,
                                'time_recvd' => $record->time_recvd,
                                'date_hvid' => $record->date_hvid,
                                'time_hvid' => $record->time_hvid,
                                'date_class' => $record->date_class,
                                'time_class' => $record->time_class,
                                'classer_id' => $record->classer_id,
                                'qual_ctrl' => $record->qual_ctrl,
                                'cutout' => $record->cutout,
                                'reclass' => $record->reclass,
                                'times_hvid' => $record->times_hvid,
                                'attempts' => $record->attempts,
                                'status' => $record->status,
                                'correction' => $record->correction,
                                'croptype' => $record->croptype,
                                'firstgrade' => $record->firstgrade,
                                'grade' => $record->grade,
                                'sort' => $record->sort,
                                'class' => $record->class,
                                'staple' => $record->staple,
                                'mic' => $record->mic,
                                'leaf' => $record->leaf,
                                'ext_matter' => $record->ext_matter,
                                'remarks' => $record->remarks,
                                'strength' => $record->strength,
                                'color_gr' => $record->color_gr,
                                'color_rd' => $record->color_rd,
                                'color_b' => $record->color_b,
                                'trash' => $record->trash,
                                'uniform' => $record->uniform,
                                'fiblength' => $record->fiblength,
                                'elongation' => $record->elongation,
                                'sfi' => $record->sfi,
                                'temperatur' => $record->temperatur,
                                'humidity' => $record->humidity,
                                'hvi_num' => $record->hvi_num,
                            ];
                        }
                    }
                    if (!empty($my_data)) {
                        ClampData::insert($my_data);
                    }
                }
            }
        }
    }
}
