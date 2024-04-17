<?php

namespace App\Models;

use App\Models\Traits\HasAttachment;
use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Selection;

/**
 * Class Area
 * @package App\Models
 *
 * @property int $id
 * @property string $name
 * @property int $crop_id
 */
class Humidity  extends Model

{
    protected $table = 'humidity';
    protected $fillable = [
        'id',
        'dalolatnoma_id',
        'number',
        'date',
        'toy_amount',
        'toy_count',
        'party',
        'selection_code',
        'sinf',
        'nav'
    ];


    public function dalolatnoma(): BelongsTo
    {
        return $this->belongsTo(Dalolatnoma::class, 'dalolatnoma_id', 'id');
    }
    public function selection(): BelongsTo
    {
        return $this->belongsTo(CropsSelection::class, 'selection_code', 'id');
    }

}
