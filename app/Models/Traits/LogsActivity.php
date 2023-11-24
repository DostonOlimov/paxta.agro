<?php

namespace App\Models\Traits;

use Spatie\Activitylog\Traits\LogsActivity as LA;

trait LogsActivity
{
    use LA;
    protected static $logAttributes = ['*'];
    protected static $ignoreChangedAttributes = ['updated_at'];
    protected static $logAttributesToIgnore = ['updated_at'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
}
