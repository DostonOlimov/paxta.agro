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

    const CROP_TYPE_1 = 1;
    const CROP_TYPE_2 = 2;
    const CROP_TYPE_3 = 3;
    const CROP_TYPE_4 = 4;
    const CROP_TYPE_5 = 5;

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
    public function types(): HasMany
    {
        return $this->hasMany(CropsType::class, 'crop_id');
    }

    public function generations(): HasMany
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
    protected static function boot()
    {
        parent::boot(); // Always call the parent boot first

        // Retrieve the crop type from the session or default to 1
        $crop = getApplicationType();

        // Add a global scope based on the crop type
        static::addGlobalScope('cropNameType', function ($query) use ($crop) {
            $query->where('crop_type', in_array($crop, [2, 4, 5]) ? $crop : 1);
        });
    }
}
