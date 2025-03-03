<?php

namespace App\Models;

use App\Models\Contracts\TestProgramInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Area
 * @package App\Models
 *
 * @property int $id
 * @property string $name
 * @property int $crop_id
 */
class TestPrograms  extends Model implements TestProgramInterface

{
    /** @var string Table name */
    protected $table = 'test_programs';

    /** @var array Mass assignable fields */
    protected $fillable = [
        'app_id',
        'director_id'
    ];

    // Relationships
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'app_id', 'id');
    }

    public function dalolatnoma(): HasOne
    {
        return $this->hasOne(Dalolatnoma::class, 'test_program_id', 'id');
    }
}
