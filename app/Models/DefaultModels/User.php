<?php

namespace App\Models\DefaultModels;

use App\Models\Area;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;

/**
 * Class User
 * @package App
 *
 * @property string $name
 * @property string $role
 * @property string $type_id
 * @property-read array $region_ids
 * @property-read array $area_ids
 * @property-read Level $level
 */
class User extends Authenticatable
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $guarded = ['status'];

    protected $appends = [
        'full_name',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class, 'role');
    }

    public function getRegionIdsAttribute()
    {
        return array_filter(explode(',', $this->state_id));
    }

    public function getAreaIdsAttribute()
    {
        return array_filter(explode(',', $this->city_id));
    }

    public function getFullNameAttribute()
    {
        return $this->lastname . ' ' . $this->name;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function access(): BelongsTo
    {
        return $this->belongsTo(tbl_accessrights::class, 'role');
    }

    public static function getTypeId()
    {
        return static::STATUS_ACTIVE;
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'city_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($query) {
            $query->where('users.status', self::STATUS_ACTIVE);
        });

        static::creating(function ($model) {
            $model->status = self::STATUS_ACTIVE;
        });
    }
}
