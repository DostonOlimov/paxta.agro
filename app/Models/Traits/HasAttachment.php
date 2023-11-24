<?php

namespace App\Models\Traits;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @mixin HasRelationships
 */
trait HasAttachment
{
    public function attachment(): MorphOne
    {
        return $this->morphOne(Attachment::class, 'attachable');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
