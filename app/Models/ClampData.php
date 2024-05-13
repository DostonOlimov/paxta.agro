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

    public function klassiyor(): BelongsTo
    {
        return $this->belongsTo(Klassiyor::class, 'classer_id', 'kode');
    }
    public function dalolatnoma(): BelongsTo
    {
        return $this->belongsTo(Dalolatnoma::class, 'dalolatnoma_id', 'id');
    }
}
