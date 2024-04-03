<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Area
 * @package App\Models
 *
 * @property int $id
 */
class InXausValue extends Model
{
    protected $table = 'in_xaus_value';

    public function in_xaus()
    {
        return $this->belongsTo(InXaus::class, 'in_xaus_id');
    }

}
