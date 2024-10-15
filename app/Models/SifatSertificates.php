<?php

namespace App\Models;

use App\Models\Traits\HasAttachment;
use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Area
 * @package App\Models
 *
 * @property int $id

 */
class SifatSertificates  extends Model

{
    use  LogsActivity,HasAttachment;

    protected $table = 'sifat_sertificates';


    public function application(): BelongsTo
    {
        return $this->belongsTo(FinalResult::class, 'app_id', 'id');
    }

}
