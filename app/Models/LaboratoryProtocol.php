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
class LaboratoryProtocol  extends Model

{
    protected $table = 'laboratory_protocol';
    protected $fillable = [
        'id',
        'klassiyor_id',
        'date',
        'number',
    ];


    public function klassiyor(): BelongsTo
    {
        return $this->belongsTo(Klassiyor::class, 'klassiyor_id', 'id');
    }

}
