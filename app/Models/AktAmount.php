<?php

namespace App\Models;

use App\Models\Traits\HasAttachment;
use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AktAmount  extends Model

{
    protected $table = 'akt_amount';

    protected $fillable = [
        'dalolatnoma_id',
        'shtrix_kod',
        'amount',
        // Add other fields that you want to allow mass assignment
    ];

    public function dalolatnoma()
    {
        return $this->belongsTo(Dalolatnoma::class, 'dalolatnoma_id', 'id');
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
                    $query->whereHas('dalolatnoma', function ($query) use ($user_city) {
                        $query->whereHas('test_program', function ($query) use ($user_city) {
                            $query->whereHas('application', function ($query) use ($user_city) {
                                $query->whereHas('prepared', function ($query) use ($user_city) {
                                    $query->where('state_id', '=', $user_city);
                                });
                            });
                        });
                    });
                });
            }
        }

        // Add global scope to exclude deleted status
        static::addGlobalScope('nonDeletedStatusScope', function ($query) use ($year,$crop) {
            $query->whereHas('dalolatnoma', function ($query) use($year,$crop) {
                $query->whereHas('test_program', function ($query) use ($year,$crop) {
                    $query->whereHas('application', function ($query) use ($year,$crop) {
                        $query->where('status', '!=', Application::STATUS_DELETED)
                            ->where('app_type', '=', $crop)
                            ->whereHas('crops', function ($query) use ($year,$crop) {
                                $query->where('year', '=', $year);
                            });
                    });
                });
            });
        });
    }
}
