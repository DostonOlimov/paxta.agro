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
class DecisionMaker  extends Model

{
    protected $table = 'decision_makers';

}
