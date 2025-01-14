<?php

namespace App\Models;

use App\Models\Traits\HasAttachment;
use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClampData  extends Model

{
    protected $table = 'clamp_data';

    public $timestamps = false;

    protected $fillable = [
        'dalolatnoma_id',
        'gin_id',
        'gin_bale',
        'lot_number',
        'weight',
        'selection',
        'date_class',
        'time_class',
        'classer_id',
        'sort',
        'class',
        'mic',
        'uniform',
        'staple',
        'strength',
        'fiblength',
        'colortype'
    ];

    public function klassiyor(): BelongsTo
    {
        return $this->belongsTo(Klassiyor::class, 'classer_id', 'kode');
    }
    public function dalolatnoma(): BelongsTo
    {
        return $this->belongsTo(Dalolatnoma::class, 'dalolatnoma_id', 'id');
    }
    protected static function boot()
    {
        parent::boot(); // Always call the parent boot first

        // Retrieve year and crop from session or use defaults
        $year = session('year', 2024);
        $crop = session('crop', 1);

        // Ensure the user is authenticated
        if ($user = auth()->user()) {
            // Add global scope for filtering by user's state if in branch state
            if ($user->branch_id == User::BRANCH_STATE) {
                static::addGlobalScope('cityStateScope', function ($query) use ($user) {
                    $query->whereHas('dalolatnoma.test_program.application.prepared', function ($query) use ($user) {
                        $query->where('state_id', $user->state_id);
                    });
                });
            }
        }

        // Add global scope to exclude deleted status and filter crops
        static::addGlobalScope('nonDeletedStatusScope', function ($query) use ($year, $crop) {
            $query->whereHas('dalolatnoma.test_program.application', function ($query) use ($year, $crop) {
                    $query->where('app_type',$crop)
                        ->whereHas('crops', function ($query) use ($year, $crop) {
                            $query->where('year', $year);
                        });
                });
        });
    }
}
