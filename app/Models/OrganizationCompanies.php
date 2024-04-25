<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganizationCompanies extends Model
{
    protected $fillable = [
        'name',
        'city_id',
        'address',
        'owner_name',
        'phone_number',
        'inn',
    ];
    public function city()
    {
        return $this->belongsTo(Area::class, 'city_id');
    }
    public function area()
    {
        return $this->belongsTo(Area::class, 'city_id');
    }
    public function application(): HasMany
    {
        return $this->hasMany(Application::class, 'organization_id');
    }
}
