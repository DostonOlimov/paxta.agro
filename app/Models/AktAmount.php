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

    public function dalolatnoma()
    {
        return $this->belongsTo(Dalolatnoma::class, 'dalolatnoma_id', 'id');
    }
}
