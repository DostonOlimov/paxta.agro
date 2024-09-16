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
    public function dalolatnoma(): BelongsTo
    {
        return $this->belongsTo(Dalolatnoma::class, 'id', 'test_program_id');
    }
    protected static function boot()
    {
        $year =  session('year') ?  session('year') : 2024;

        parent::boot(); // Always call the parent boot first

        // Ensure the user is authenticated
        $user = auth()->user();

        if ($user) {
            // Add global scope for filtering by user's state
            if ($user->branch_id == User::BRANCH_STATE) {
                $user_city = $user->state_id;

                static::addGlobalScope('cityStateScope', function ($query) use ($user_city) {
                    $query->whereHas('application', function ($query) use ($user_city) {
                        $query->whereHas('organization', function ($query) use ($user_city) {
                            $query->whereHas('city', function ($query) use ($user_city) {
                                $query->where('state_id', '=', $user_city);
                            });
                        });
                    });
                });
            }
            if ($user->crop_branch == User::CROP_BRANCH_CHIGIT) {
                // Add global scope for filtering by chigit's apps
                static::addGlobalScope('chigitAppScope', function ($query) {
                    $query->whereHas('application', function ($query) {
                        $query->whereHas('crops', function ($query) {
                            $query->where('name_id', '=', 2);
                        });
                    });
                });
            } elseif ($user->crop_branch == User::CROP_BRANCH_TOLA) {
                // Add global scope for filtering by chigit's apps
                static::addGlobalScope('chigitAppScope', function ($query) {
                    $query->whereHas('application', function ($query) {
                        $query->whereHas('crops', function ($query) {
                            $query->where('name_id', '=', 1);
                        });
                    });
                });
            }
        }

        // Add global scope to exclude deleted status
        static::addGlobalScope('nonDeletedStatusScope', function ($query) use ($year) {
            $query->whereHas('application', function ($query) use($year){
                $query->where('status', '!=', Application::STATUS_DELETED)
                    ->whereHas('crops', function ($query) use ($year) {
                        $query->where('year', '=', $year);
                    });
            });
        });
    }
}
