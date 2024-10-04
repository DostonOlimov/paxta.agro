<?php

namespace App\Models\DefaultModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class tbl_activities extends Model
{
    protected $guarded = [];
    protected $fillable = [
        'user_id',
        'action_id',
        'city_id',
        'action_type',
        'ip_adress',
        'action',
        'time'
    ];
}
