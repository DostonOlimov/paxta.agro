<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laboratories extends Model
{
    protected $table = 'laboratories';

    public function city()
    {
        return $this->belongsTo(Area::class, 'city_id');
    }
    public function getFullAddressAttribute(){
        $city = str_word_count(optional($this->city)->name) == 1 ? optional($this->city)->name.' tuman' : optional($this->city)->name;
        return optional($this->city->region)->name.','.$city.','.$this->address;
    }
}
