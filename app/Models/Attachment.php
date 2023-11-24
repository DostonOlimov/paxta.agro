<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Attachment
 * @package App\Models
 */
class Attachment extends Model
{
    protected $fillable = [
        'url'
    ];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }
}
