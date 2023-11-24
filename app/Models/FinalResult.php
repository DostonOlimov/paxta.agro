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
class FinalResult  extends Model

{
    use  LogsActivity,HasAttachment;

    const TYPE_MUV= 1;
    const TYPE_NOMUV = 0;

    protected $table = 'final_results';

    protected $fillable = [
        'test_program_id',
        'number',
        'date',
        'type',
        'folder_number',
        'comment',
    ];

    public function test_program(): BelongsTo
    {
        return $this->belongsTo(TestPrograms::class, 'test_program_id', 'id');
    }
    public function certificate(): BelongsTo
    {
        return $this->belongsTo(Sertificate::class, 'id', 'final_result_id');
    }
    public function decision_maker(): BelongsTo
    {
        return $this->belongsTo(DecisionMaker::class, 'maker', 'id');
    }


    public static function getType($type = null)
    {
        $arr = [
            self::TYPE_MUV => 'Muvofiq',
            self::TYPE_NOMUV => 'Nomuvofiq',
        ];

        if ($type === null) {
            return $arr;
        }
        return $arr[$type];
    }

}
