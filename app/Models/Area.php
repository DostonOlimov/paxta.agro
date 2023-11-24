<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Area
 * @package App\Models
 *
 * @property int $id
 * @property string $name
 * @property int $state_id
 * @property string $soato
 *
 * @property-read Region $region
 */
class Area extends Model
{
    protected $table = 'tbl_cities';

    public function region()
    {
        return $this->belongsTo(Region::class, 'state_id');
    }

    public function listArea(): BelongsTo
    {
        return $this->belongsTo(ListArea::class, 'list_id');
    }
}
