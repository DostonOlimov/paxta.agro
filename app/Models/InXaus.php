<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Area
 * @package App\Models
 *
 * @property int $id
 * @property int $type
 */
class InXaus extends Model
{
    protected $table = 'in_xaus';

    protected $fillable = ['type','date','created_by'];

    protected $appends = ['status'];

    const TYPE_MIC= 1;
    const TYPE_STRENGTH = 2;
    const TYPE_INIFORMITY = 3;
    const TYPE_LENGTH = 4;

    public static function getType($type = null)
    {
        $arr = [
            self::TYPE_MIC => "Microner",
            self::TYPE_STRENGTH => 'Strength',
            self::TYPE_INIFORMITY => 'Iniformity',
            self::TYPE_LENGTH => 'Length',
        ];

        if ($type === null) {
            return $arr;
        }

        return $arr[$type];
    }
    public function getStatusAttribute()
    {
        $latestDate = self::latest('date')->value('date');
        return $this->date == $latestDate ? 'active' : 'passive';
    }

    public function in_xaus_value()
    {
        return $this->hasMany(InXausValue::class,'in_xaus_id','id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'created_by','id');
    }

}
