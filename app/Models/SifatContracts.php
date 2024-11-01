<?php

namespace App\Models;

use App\Models\Traits\HasAttachment;
use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Area
 * @package App\Models
 *
 * @property int $id

 */
class SifatContracts  extends Model

{
    use  LogsActivity,HasAttachment;

    protected $table = 'sifat_contracts';

    protected $fillable = ['organization_id','number','date'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(OrganizationCompanies::class, 'organization_id', 'id');
    }


}
