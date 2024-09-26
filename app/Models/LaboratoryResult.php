<?php

namespace App\Models;

use App\Models\Traits\HasAttachment;
use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Area
 * @package App\Models
 *
 * @property int $id
 * @property string $name
 * @property int $crop_id
 */
class LaboratoryResult  extends Model

{
    use  LogsActivity,HasAttachment;


    protected $table = 'laboratory_results';

    protected $fillable = [
        'dalolatnoma_id',
        'tip_id',
        'mic',
        'staple',
        'strength',
        'uniform',
        'humidity',
        'fiblength'
    ];

    public function dalolatnoma(): BelongsTo
    {
        return $this->belongsTo(Dalolatnoma::class, 'dalolatnoma_id', 'id');
    }
    public function tip()
    {
        return $this->belongsTo(Tips::class, 'tip_id', 'id');
    }

}
