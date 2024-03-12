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
class TestPrograms  extends Model

{
    use  LogsActivity;

    protected $table = 'test_programs';

    protected $fillable = [
        'app_id',
        'count',
        'measure_type',
        'weigth',
        'created_by',
        'updated_by',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'app_id', 'id');
    }

    public function laboratory(): BelongsTo
    {
        return $this->belongsTo(Laboratories::class, 'laboratory_id', 'id');
    }
    public function result(): BelongsTo
    {
        return $this->belongsTo(FinalResult::class, 'id', 'test_program_id');
    }
    public function dalolatnoma(): BelongsTo
    {
        return $this->belongsTo(Dalolatnoma::class, 'id', 'test_program_id');
    }

}
