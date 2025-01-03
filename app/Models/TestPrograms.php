<?php

namespace App\Models;

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
class TestPrograms  extends Model

{
    use  LogsActivity;

    protected $table = 'test_programs';

    protected $fillable = [
        'app_id',
        'director_id',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'app_id', 'id');
    }

    public function laboratory(): BelongsTo
    {
        return $this->belongsTo(Laboratories::class, 'laboratory_id', 'id');
    }
    public function result(): BelongsTo
    {
        return $this->belongsTo(FinalResult::class, 'id', 'test_program_id');
    }
    public function dalolatnoma()
    {
        return $this->hasOne(Dalolatnoma::class, 'test_program_id', 'id');
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
                    $query->whereHas('application', function ($query) use ($user_city) {
                        $query->whereHas('prepared', function ($query) use ($user_city) {
                            $query->where('state_id', '=', $user_city);

                        });
                    });
                });
            }
        }

        // Add global scope to exclude deleted status
        static::addGlobalScope('nonDeletedStatusScope', function ($query) use ($year,$crop) {
            $query->whereHas('application', function ($query) use($year,$crop){
                $query->where('status', '!=', Application::STATUS_DELETED)
                    ->where('app_type',$crop)
                    ->whereHas('crops', function ($query) use ($year,$crop) {
                        $query->where('year', '=', $year);
                    });
            });
        });
    }
}
