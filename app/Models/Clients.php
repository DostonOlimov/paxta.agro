<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Class Area
 * @package App\Models
 *
 * @property int $id
 * @property string $name
 * @property int $state_id
 * @property int $kod
 *
 * @property-read Region $region
 */
class Clients extends Model
{
    protected $fillable = [
        'id',
        'name',
        'kod',
        'state_id',
        'tipp'
    ];
    protected $table = 'clients';

    public function state()
    {
        return $this->belongsTo(Region::class, 'state_id');
    }


}
