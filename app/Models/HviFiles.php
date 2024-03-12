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
class HviFiles extends Model
{
    protected $table = 'hvi_files';

    protected $fillable = [
        'state_id',
        'path',
        'user_id',
        'date',
        'count',
    ];

    public function state()
    {
        return $this->belongsTo(Region::class, 'state_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id', 'id');
    }
}
