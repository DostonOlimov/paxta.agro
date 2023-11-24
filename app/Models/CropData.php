<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class CropData extends Model
{
    protected $table = 'crop_data';

    const MEASURE_TYPE_TONNA= 1;
    const MEASURE_TYPE_KG = 2;
    const MEASURE_TYPE_DONA = 3;

    public function name()
    {
        return $this->belongsTo(CropsName::class, 'name_id');
    }

    public function type()
    {
        return $this->belongsTo(CropsType::class, 'type_id');
    }
    public function generation()
    {
        return $this->belongsTo(CropsGeneration::class, 'generation_id');
    }
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
    public function application()
    {
        return $this->hasMany(Application::class,'crop_data_id','id');
    }

    public static function getMeasureType($type = null)
    {
        $arr = [
            self::MEASURE_TYPE_TONNA => 'tonna',
            self::MEASURE_TYPE_KG => 'kg',
            self::MEASURE_TYPE_DONA => 'dona',
        ];

        if ($type === null) {
            return $arr;
        }

        return $arr[$type];
    }
    public static function getYear($type = null)
    {
        $arr = [
            2018 => 2018,
            2019 => 2019,
            2020 => 2020,
            2021 => 2021,
            2022 => 2022,
            2023 => 2023,

        ];

        if ($type === null) {
            return $arr;
        }

        return $arr[$type];
    }
    public function getAmountNameAttribute()
    {
        if($this->amount){
            return$this->amount. ' ' .self::getMeasureType($this->measure_type);
        }
      return null;
    }
}
