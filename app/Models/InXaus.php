<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Area
 * @package App\Models
 *
 * @property int $id
 * @property int $type
 */
class InXaus extends Model
{
    protected $table = 'in_xaus';

    protected $fillable = ['type','date','created_by'];

    protected $appends = ['status'];

    const TYPE_MIC= 1;
    const TYPE_STRENGTH = 2;
    const TYPE_INIFORMITY = 3;
    const TYPE_LENGTH = 4;

    public static function getType($type = null)
    {
        $arr = [
            self::TYPE_MIC => "Microner",
            self::TYPE_STRENGTH => 'Strength',
            self::TYPE_INIFORMITY => 'Iniformity',
            self::TYPE_LENGTH => 'Length',
        ];

        if ($type === null) {
            return $arr;
        }

        return $arr[$type];
    }
    public function getStatusAttribute()
    {
        $latestDate = self::where('state_id',$this->state_id)->latest('date')->value('date');
        return $this->date == $latestDate ? 'active' : 'passive';
    }

    public function in_xaus_value()
    {
        return $this->hasMany(InXausValue::class,'in_xaus_id','id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'created_by','id');
    }
    public function state()
    {
        return $this->belongsTo(Region::class,'state_id','id');
    }

    public function calculateMetrics(){
        $results = [];

        // Fetch all data grouped by type
        $valuesByType = self::in_xaus_value()->whereIn('type', [
            InXaus::TYPE_MIC,
            InXaus::TYPE_STRENGTH,
            InXaus::TYPE_INIFORMITY,
            InXaus::TYPE_LENGTH,
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
