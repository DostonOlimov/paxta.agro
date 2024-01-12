<?php

namespace App\Models;

use App\Models\Traits\HasAttachment;
use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GinBalles  extends Model

{
    protected $table = 'gin_balles';

    protected $fillable = [
        'dalolatnoma_id',
        'from_number',
        'to_number',
        'from_toy',
        'to_toy'
    ];

}
