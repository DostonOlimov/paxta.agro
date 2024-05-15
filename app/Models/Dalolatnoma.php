<?php

namespace App\Models;

use App\Models\Traits\HasAttachment;
use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * Class Area
 * @package App\Models
 *
 * @property int $id
 * @property string $name
 * @property int $crop_id
 */
class Dalolatnoma  extends Model

{
    protected $table = 'dalolatnoma';


    public function test_program(): BelongsTo
    {
        return $this->belongsTo(TestPrograms::class, 'test_program_id', 'id');
    }
    public function laboratory_final_results()
    {
        return $this->hasOne(LaboratoryFinalResults::class);
    }
    public function humidity(): BelongsTo
    {
        return $this->belongsTo(Humidity::class, 'id', 'dalolatnoma_id');
    }
    public function humidity_result(): BelongsTo
    {
        return $this->belongsTo(HumidityResult::class, 'id', 'dalolatnoma_id');
    }
    public function selection()
    {
        return $this->belongsTo(CropsSelection::class, 'selection_code', 'id');
    }
    public function gin_balles()
    {
        return $this->hasMany(GinBalles::class);
    }
    public function clamp_data()
    {
        return $this->hasMany(ClampData::class,'dalolatnoma_id','id');
    }
    public function akt_amount()
    {
        return $this->hasMany(AktAmount::class,'dalolatnoma_id','id');
    }
    public function result()
    {
        return $this->hasMany(FinalResult::class, 'dalolatnoma_id', 'id');
    }
    public function measurement_mistake()
    {
        return $this->belongsTo(MeasurementMistake::class, 'id', 'dalolatnoma_id');
    }
    public function laboratory_result()
    {
        return $this->belongsTo(LaboratoryResult::class, 'id', 'dalolatnoma_id');
    }

    /**
     * Calculate standard deviation for specified columns.
     *
     * @return array
     */
    public function calculateDeviations()
    {
        $selected_array = ['mic', 'strength', 'uniform', 'fiblength'];
        // Retrieve data for mic, strength, uniform, and fiblength columns from the database
        $valuesByType = self::clamp_data()->select('mic', 'strength', 'uniform',DB::raw('fiblength/100 as fiblength'))->get();

        // Calculate standard deviation for each column
        $standardDeviations = [];
        foreach ($selected_array as $select_item){
            $columnData = $valuesByType->pluck($select_item)->toArray();
            $standardDeviations[$select_item] = self::calculateStandardDeviation($columnData);
        }

        return $standardDeviations;
    }

    /**
     * Calculate standard deviation for a given column data.
     *
     * @param  array  $columnData
     * @return float|null
     */
    protected static function calculateStandardDeviation(array $columnData)
    {
        $n = count($columnData);

        if ($n <= 1) {
            return null; // If there's only one record or none, return null to avoid division by zero
        }

        // Calculate the average
        $average = round(array_sum($columnData) / $n,3);

        // Calculate the sum of squares of differences from the average
        $sumOfSquares = 0;
        foreach ($columnData as $value) {
            $sumOfSquares += pow($value - $average, 2);
        }

        // Calculate the result
        return sqrt(1 / ($n * ($n - 1)) * $sumOfSquares);
    }

}
