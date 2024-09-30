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
class HumidityResult  extends Model

{
    protected $table = 'humidity_result';
    protected $fillable = [
        'id',
        'dalolatnoma_id',
        'number',
        'date',
        'm0',
        'm1',
        'mk0',
        'mk1',
        'kalibrovka',
    ];


    public function dalolatnoma(): BelongsTo
    {
        return $this->belongsTo(Dalolatnoma::class, 'dalolatnoma_id', 'id');
    }

    public function getHumidityAttribute()
    {
        $value1 = 100 * ($this->m0 - $this->mk0) / $this->mk0 - 0.4;
        $value2 = 100 * ($this->m1 - $this->mk1) / $this->mk1 - 0.4;

        $value = ($value1 + $value2) / 2;

        return $value;
    }
    public function calculateMistake()
    {
        $e1 = 50 / $this->mk0;
        $e2 = 50 / $this->mk1;
        $f1 = (50 * $this->m0) / ($this->mk0 * $this->mk0);
        $f2 = (50 * $this->m1) / ($this->mk1 * $this->mk1);
        $h = $this->kalibrovka / 2;
        $i = sqrt( $e1**2 + $f1**2  + $e2**2  + $f2**2 );

        return 2*$i*$h ;
    }
}
