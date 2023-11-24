<?php

namespace App\Models;

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
class Decision extends Model
{
    use  LogsActivity;

    const STATUS_DELETED = 0;
    const STATUS_NEW = 1;
    const STATUS_ACCEPTED = 2;
    protected $table = 'decisions';

    protected $fillable = [
        'director_id',
        'app_id',
        'laboratory_id',
        'status',
        'requirement',
        'created_by',
        'updated_by',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'app_id', 'id');
    }

    public function director(): BelongsTo
    {
        return $this->belongsTo(User::class, 'director_id', 'id');
    }

    public function laboratory(): BelongsTo
    {
        return $this->belongsTo(Laboratories::class, 'laboratory_id', 'id');
    }
    public function city(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'city_id', 'id');
    }

}
