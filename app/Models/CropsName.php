<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Class Area
 * @package App\Models
 *
 * @property int $id
 * @property string $name
 */
class CropsName extends Model
{
    protected $table = 'crops_name';

    protected $fillable = [
        'id', 'name',
    ];
    public function crop_data()
    {
        return $this->hasMany(CropData::class,'name_id','id');
    }
    public function nds(): BelongsTo
    {
        return $this->belongsTo(Nds::class,'id','crop_id');
    }
    public function areas(): HasMany
    {
        return $this->hasMany(CropsType::class, 'crop_id');
    }

    public function listRegion(): HasMany
    {
        return $this->hasMany(CropsGeneration::class, 'crop_id');
    }
    public function selecton(): HasMany
    {
        return $this->hasMany(CropsSelection::class, 'crop_id');
    }
    // Define the relationships (corrected)
    public function applications(): HasManyThrough
    {
        return $this->hasManyThrough(Application::class, CropData::class, 'name_id', 'crop_data_id','id','id'); // Corrected foreign keys
    }
}
