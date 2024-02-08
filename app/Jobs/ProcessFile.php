<?php

namespace App\Jobs;

use App\Models\ClampData;
use App\Models\Dalolatnoma;
use App\Models\GinBalles;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use XBase\TableReader;

class ProcessFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $file;
    protected $id;

    public function __construct($file)
    {
        $this->file = $file['path'];
        $this->id = $file['id'];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $file = storage_path('app/' . $this->file);
        $dalolatnoma = Dalolatnoma::find($this->id);
        $gin_id = 0;
        if($dalolatnoma){
            $gin_id = 1000 * $dalolatnoma->test_program->application->prepared->region->clamp_id + $dalolatnoma->test_program->application->prepared->kod;
            $gin_balles = GinBalles::where('dalolatnoma_id',$this->id)->get();
        $table = new TableReader($file);
        foreach ($gin_balles as $balles){
            for($i=$balles->from_number;$i<=$balles->to_number;$i++){
                $data = ClampData::where('gin_id', $gin_id)
                    ->where('gin_bale', $i)
                    ->where('dalolatnoma_id',$this->id)
                    ->first();
                if(!$data){
                    while ($record = $table->nextRecord()) {
                        if ($record->gin_id == $gin_id and $record->gin_bale == $i) {
                                if (!$data) {
                                    $data = new ClampData();
                                    $data->dalolatnoma_id = $this->id;
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
                }

            }
        }
    }
}
