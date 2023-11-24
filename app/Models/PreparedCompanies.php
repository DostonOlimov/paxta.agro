<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class PreparedCompanies extends Model
{
    public function region()
    {
        return $this->belongsTo(Region::class, 'state_id');
    }
}
