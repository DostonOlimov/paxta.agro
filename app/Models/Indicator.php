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
 * @property int $type_id
 * @property string $number
 */
class Indicator extends Model
{
    protected $table = 'quality_indacators';


    public function crops()
    {
        return $this->belongsTo(CropsName::class, 'crop_id');
    }
    public function child()
    {
        return $this->belongsTo(Indicator::class, 'parent_id','id');
    }



}
