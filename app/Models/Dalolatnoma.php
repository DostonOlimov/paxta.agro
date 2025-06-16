<?php

namespace App\Models;

use App\Models\Traits\HasAttachment;
use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
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

    protected $fillable = [
        'id',
        'test_program_id',
        'number',
        'date',
        'toy_count',
        'amount',
        'amount2',
        'party',
        'selection_code',
        'sinf',
        'nav',
        'tara'
    ];

    public function test_program(): BelongsTo
    {
        return $this->belongsTo(TestPrograms::class, 'test_program_id', 'id');
    }
    public function laboratory_final_results()
    {
        return $this->hasOne(LaboratoryFinalResults::class);
    }
    public function humidity()
    {
        return $this->hasOne(Humidity::class, 'dalolatnoma_id', 'id');
    }
    public function humidity_result()
    {
        return $this->hasOne(HumidityResult::class, 'dalolatnoma_id', 'id');
    }
    public function selection()
    {
        return $this->belongsTo(CropsSelection::class, 'selection_code', 'id');
    }
    public function gin_balles()
    {
        return $this->hasMany(GinBalles::class,'dalolatnoma_id','id');
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
        return $this->hasOne(MeasurementMistake::class,'dalolatnoma_id','id');
    }
    public function laboratory_result()
    {
        return $this->hasOne(LaboratoryResult::class,'dalolatnoma_id','id');
    }

    /**
     * Get aggregated clamp data.
     */
    public function averageClampData(): ?object
    {
        return ClampData::selectRaw('
            AVG(mic) as mic,
            AVG(staple) as staple,
            AVG(strength) as strength,
            AVG(uniform) as uniform,
            AVG(fiblength) as fiblength
        ')
            ->where('dalolatnoma_id', $this->id)
            ->first();
    }

    public function averageFiberLength(): ?float
    {
        $data = $this->averageClampData();
        return $data?->fiblength ? $data?->fiblength / 100 : null;
    }

    /**
     * Get tip.
     */
    public function findTipByAverageFiberLength(): ?Tips
    {
        $length = $this->averageFiberLength();

        return $length === null ? null : Tips::where('min', '<=', $length)
            ->where('max', '>=', $length)
            ->first();
    }

    /**
     * Get aggregated clamp data.
     */
    public function summarizeClampData(): Collection
    {
        return ClampData::query()
            ->selectRaw("
            clamp_data.sort,
            clamp_data.class,
            COUNT(*) as count,
            SUM(akt_amount.amount) as total_amount,
            AVG(clamp_data.mic) as mic,
            AVG(clamp_data.staple) as staple,
            AVG(clamp_data.strength) as strength,
            AVG(clamp_data.uniform) as uniform,
            AVG(clamp_data.humidity) as humidity
        ")
            ->join('akt_amount', function ($join) {
                $join->on('akt_amount.shtrix_kod', '=', 'clamp_data.gin_bale')
                    ->on('akt_amount.dalolatnoma_id', '=', 'clamp_data.dalolatnoma_id');
            })
            ->where('clamp_data.dalolatnoma_id', $this->id)
            ->groupBy('clamp_data.sort', 'clamp_data.class')
            ->get();
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
        foreach ($selected_array as $key=>$select_item){
            $columnData = $valuesByType->pluck($select_item)->toArray();
            $standardDeviations[$select_item] = self::calculateStandardDeviation($columnData,$key);
        }

        return $standardDeviations;
    }

    /**
     * Calculate standard deviation for a given column data.
     *
     * @param array $columnData
     * @param int $key
     * @return float|null
     */
    protected static function calculateStandardDeviation(array $columnData,int $key)
    {
        $n = count($columnData);

        if ($n <= 1) {
            return null; // If there's only one record or none, return null to avoid division by zero
        }

        // Calculate the average
        $average = round(array_sum($columnData) / $n,8);


        // Calculate the sum of squares of differences from the average
        $sumOfSquares = 0;
        foreach ($columnData as $value) {
            $sumOfSquares += ($value - $average)**2;
        }
        // Calculate the result
        return sqrt(round($sumOfSquares,8) / (221 * (221 - 1)));
    }

    protected static function boot()
    {
        parent::boot(); // Always call the parent boot first

        // Ensure the user is authenticated
        $user = auth()->user();
        $year = session('year', 2024);
        $crop = session('crop', 1);

        if ($user) {
            // Add global scope for filtering by user's state
            if ($user->branch_id == User::BRANCH_STATE) {
                $user_city = $user->state_id;

                static::addGlobalScope('cityStateScope', function ($query) use ($user_city) {
                    $query->whereHas('test_program', function ($query) use ($user_city) {
                        $query->whereHas('application', function ($query) use ($user_city) {
                            $query->whereHas('prepared', function ($query) use ($user_city) {
                                $query->where('state_id', '=', $user_city);
                            });
                        });
                    });
                });
            }
        }

        // Add global scope to exclude deleted status
        static::addGlobalScope('nonDeletedStatusScope', function ($query) use ($year,$crop) {
            $query->whereHas('test_program', function ($query) use($year,$crop) {
                $query->whereHas('application', function ($query) use ($year,$crop) {
                    $query->where('status', '!=', Application::STATUS_DELETED)
                        ->where('app_type',$crop)
                        ->whereHas('crops', function ($query) use ($year,$crop) {
                            $query->where('year', '=', $year);
                        });
                });
            });
        });
    }

}
