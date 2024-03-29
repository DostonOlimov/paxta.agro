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
class Dalolatnoma  extends Model

{
    protected $table = 'dalolatnoma';


    public function test_program(): BelongsTo
    {
        return $this->belongsTo(TestPrograms::class, 'test_program_id', 'id');
    }
    public function decision()
    {
        return $this->belongsTo(Decision::class, 'test_program_id', 'id');
    }
    public function gin_balles()
    {
        return $this->hasMany(GinBalles::class);
    }
    public function clamp_data()
    {
        return $this->hasMany(ClampData::class);
    }
    public function akt_amount()
    {
        return $this->hasMany(AktAmount::class,'id','dalolatnoma_id');
    }
    public function result()
    {
        return $this->hasMany(FinalResult::class, 'id', 'dalolatnoma_id');
    }


}
