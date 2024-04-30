<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaboratoryOperator extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'laboratory_id',
        'status',
    ];

    public function laboratory()
    {
        return $this->belongsTo(Laboratories::class);
    }
    public function laboratory_final_results()
    {
        return $this->hasMany(LaboratoryFinalResults::class);
    }
}
