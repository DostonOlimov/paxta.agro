<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    // Yangi ruxsat qo'shish: konstanta + permissions jadvaliga yozuv (migration) qo'shing.
    const CROPS_SELECTION_MANAGE = 'crops_selection.manage';

    protected $fillable = ['key', 'name'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'permission_user');
    }
}
