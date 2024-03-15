<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;


/**
 * Class DriverLicence
 * @package App\Models
 *
 * @property string $code
 * @property string $name
 * @property string $soato
 */
class Region extends Model
{
    protected $table = 'tbl_states';

    protected $fillable = [
        'id', 'name',
    ];

    public function areas(): HasMany
    {
        return $this->hasMany(Area::class, 'state_id');
    }

    public function applications(): HasManyThrough
    {
        return $this->hasManyThrough(Application::class, OrganizationCompanies::class, 'city_id', 'organization_id', 'id', 'id');
    }
    public function listRegion(): HasMany
    {
        return $this->hasMany(ListRegion::class, 'list_id');
    }

    public function organization(): HasMany
    {
        return $this->hasMany(OrganizationCompanies::class, 'city_id');
    }

    public function hvi_file()
    {
        return $this->belongsTo(HviFiles::class, 'id','state_id');
    }
}
