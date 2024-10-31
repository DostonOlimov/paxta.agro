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
    public function getFullAddressAttribute(){
        $city = str_word_count(optional($this->city)->name) == 1 ? optional($this->city)->name.' tuman' : optional($this->city)->name;
        return optional($this->city->region)->name.','.$city.','.$this->address;
    }
    protected static function boot()
    {
        parent::boot(); // Always call the parent boot first

        // Ensure the user is authenticated
        if ($user = auth()->user()) {
            // Add global scope for filtering by user's state if in branch state
            if ($user->branch_id == User::BRANCH_STATE) {
                static::addGlobalScope('cityStateScope', function ($query) use ($user) {
                    $query->whereHas('area', function ($query) use ($user) {
                        $query->where('state_id', $user->state_id);
                    });
                });
            }
        }
    }
}
