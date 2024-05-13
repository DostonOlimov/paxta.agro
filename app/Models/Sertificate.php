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
class Sertificate  extends Model

{
    use  LogsActivity,HasAttachment;

    protected $table = 'sertificates';


    public function final_result(): BelongsTo
    {
        return $this->belongsTo(FinalResult::class, 'final_result_id', 'id');
    }
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

}
