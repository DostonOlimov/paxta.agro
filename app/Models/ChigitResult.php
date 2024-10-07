<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
class ChigitResult extends Model
{
    protected $fillable = [
        'id',
        'app_id',
        'indicator_id',
        'value'
    ];
    protected $table = 'chigit_results';

    public function indicator()
    {
        return $this->belongsTo(Indicator::class, 'indicator_id');
    }

}
