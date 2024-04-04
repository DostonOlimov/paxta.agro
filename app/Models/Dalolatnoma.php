<?php

namespace App\Models;

use App\Models\Traits\HasAttachment;
use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    public function humidity(): BelongsTo
    {
        return $this->belongsTo(Humidity::class, 'id', 'dalolatnoma_id');
    }
    public function decision()
    {
        return $this->belongsTo(Decision::class, 'test_program_id', 'id');
    }
    public function gin_balles()
    {
        return $this->hasMany(GinBalles::class);
    }
    public function clamp_data()
    {
        return $this->hasMany(ClampData::class);
    }
    public function akt_amount()
    {
        return $this->hasMany(AktAmount::class,'id','dalolatnoma_id');
    }
    public function result()
    {
        return $this->hasMany(FinalResult::class, 'id', 'dalolatnoma_id');
    }
    public function calculateMetrics(){
        $results = [];

        // Fetch all data grouped by type
        $valuesByType = self::select('mic','strength','uniform','fiblength')->whereIn('type', [
            InXaus::TYPE_MIC,
            InXaus::TYPE_STRENGTH,
            InXaus::TYPE_LENGTH,
            InXaus::TYPE_INIFORMITY
        ])->get()->groupBy('type');

        foreach ($valuesByType as $type => $values) {
            $n = $values->count();

            if ($n <= 1) {
                $results[$type] = null; // If there's only one record or none, return null to avoid division by zero
            } else {
                // Calculate the average
                $average = $values->avg('value');

                // Calculate the sum of squares of differences from the average
                $sumOfSquares = $values->sum(function ($record) use ($average) {
                    return pow($record->value - $average, 2);
                });

                // Calculate the result
                $result = sqrt(1 / ($n * ($n - 1)) * $sumOfSquares);

                $results[$type] = $result;
            }
        }

        return $results;
    }

}
