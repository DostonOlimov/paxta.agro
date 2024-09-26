<?php

namespace App\Models;

use App\Models\Traits\HasAttachment;
use App\Models\Traits\LogsActivity;
use Generator;
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
class FinalResult  extends Model

{
    use  LogsActivity,HasAttachment;

    const TYPE_MUV= 1;
    const TYPE_NOMUV = 0;

    protected $table = 'final_results';

    protected $fillable = [
        'dalolatnoma_id',
        'class',
       'sort',
        'count',
        'amount',
        'mic',
        'staple',
        'strength',
        'uniform',
        'humidity',
    ];

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(Sertificate::class, 'id', 'final_result_id');
    }
    public function generation(): BelongsTo
    {
        return $this->belongsTo(CropsGeneration::class, 'class',  'kod');
    }

    public function dalolatnoma(): BelongsTo
    {
        return $this->belongsTo(Dalolatnoma::class, 'dalolatnoma_id', 'id');
    }
    public function decision_maker(): BelongsTo
    {
        return $this->belongsTo(DecisionMaker::class, 'maker', 'id');
    }

    public static function getType($type = null)
    {
        $arr = [
            self::TYPE_MUV => 'Muvofiq',
            self::TYPE_NOMUV => 'Nomuvofiq',
        ];

        if ($type === null) {
            return $arr;
        }
        return $arr[$type];
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
                    $query->whereHas('dalolatnoma', function ($query) use ($user_city) {
                        $query->whereHas('test_program', function ($query) use ($user_city) {
                            $query->whereHas('application', function ($query) use ($user_city) {
                                $query->whereHas('organization', function ($query) use ($user_city) {
                                    $query->whereHas('city', function ($query) use ($user_city) {
                                        $query->where('state_id', '=', $user_city);
                                    });
                                });
                            });
                        });
                    });
                });
            }
            if ($user->crop_branch == User::CROP_BRANCH_CHIGIT) {
                // Add global scope for filtering by chigit's apps
                static::addGlobalScope('chigitAppScope', function ($query) {
                    $query->whereHas('dalolatnoma', function ($query) {
                        $query->whereHas('test_program', function ($query) {
                            $query->whereHas('application', function ($query) {
                                $query->whereHas('crops', function ($query) {
                                    $query->where('name_id', '=', 2);
                                });
                            });
                        });
                    });
                });
            } elseif ($user->crop_branch == User::CROP_BRANCH_TOLA) {
                // Add global scope for filtering by chigit's apps
                static::addGlobalScope('chigitAppScope', function ($query) {
                    $query->whereHas('dalolatnoma', function ($query) {
                        $query->whereHas('test_program', function ($query) {
                            $query->whereHas('application', function ($query) {
                                $query->whereHas('crops', function ($query) {
                                    $query->where('name_id', '=', 1);
                                });
                            });
                        });
                    });
                });
            }
        }

        // Add global scope to exclude deleted status
        static::addGlobalScope('nonDeletedStatusScope', function ($query) use ($year) {
            $query->whereHas('dalolatnoma', function ($query) use($year) {
                $query->whereHas('test_program', function ($query) use ($year) {
                    $query->whereHas('application', function ($query) use ($year) {
                        $query->where('status', '!=', Application::STATUS_DELETED)
                            ->whereHas('crops', function ($query) use ($year) {
                                $query->where('year', '=', $year);
                            });
                    });
                });
            });
        });
    }
}
