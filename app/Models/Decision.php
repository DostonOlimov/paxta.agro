<?php

namespace App\Models;

use App\Models\Contracts\DecisionInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Area
 * @package App\Models
 *
 * @property int $id
 * @property int $number
 * @property int $app_id
 */
class Decision extends Model implements DecisionInterface
{
    // Constants grouped by category with type hints in PHPDoc
    /** @var int Status codes */
    public const STATUS_DELETED = 0;
    public const STATUS_NEW = 1;
    public const STATUS_ACCEPTED = 2;


    /** @var string Table name */
    protected $table = 'decisions';

    /** @var array Mass assignable fields */
    protected $fillable = [
        'director_id',
        'app_id',
        'number',
        'date',
        'laboratory_id',
        'status',
        'created_by',
    ];

    // Relationships
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

}
