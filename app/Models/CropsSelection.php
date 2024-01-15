<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CropsSelection extends Model
{
    protected $table = 'crops_selection';

    public function crops()
    {
        return $this->belongsTo(CropsName::class, 'crop_id');
    }
}
