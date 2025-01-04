<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SifatSertificates extends Model
{

    const CIGIT_TYPE_XARIDORLI = 1;
    const CIGIT_TYPE_XARIDORSIZ = 2;
    const PAXTA_TYPE = 3;

    protected $table = 'sifat_sertificates';

    protected $fillable = [
        'id',
        'app_id',
        'number',
        'year',
        'zavod_id',
        'type',
        'created_by',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'app_id', 'id');
    }

    public function zavod(): BelongsTo
    {
        return $this->belongsTo(PreparedCompanies::class, 'zavod_id', 'id');
    }


}
