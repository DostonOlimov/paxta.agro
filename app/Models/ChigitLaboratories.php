<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChigitLaboratories extends Model
{
    protected $table = 'chigit_laboratories';

    public function zavod()
    {
        return $this->belongsTo(PreparedCompanies::class, 'city_id');
    }
}
