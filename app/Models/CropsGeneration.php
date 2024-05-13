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
 * @property int $crop_id
 */
class CropsGeneration extends Model
{
    protected $table = 'crops_generation';

    protected $fillable = [
        'name',
        'kod',
       'crop_id ',
    ];

    public function crops()
    {
        return $this->belongsTo(CropsName::class, 'crop_id');
    }

}
