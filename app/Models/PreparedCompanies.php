<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class PreparedCompanies extends Model
{
    protected $table = 'prepared_companies';

    protected $fillable = [
        'name',
        'kod',
        'tara',
        'state_id',
    ];
    public function region()
    {
        return $this->belongsTo(Region::class, 'state_id');
    }
    public function chigit_laboratory()
    {
        return $this->belongsTo(ChigitLaboratories::class, 'kod','zavod_kod');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id','zavod_id');
    }
}
