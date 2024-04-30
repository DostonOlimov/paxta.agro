<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaboratoryFinalResults extends Model
{
    use HasFactory;

    protected $table = 'laboratory_final_results';

    protected $fillable = [
        'dalolatnoma_id',
        'operator_id',
        'klassiyor_id',
        'director_id',
        'number',
        'date',
        'from',
        'vakili',
        'vakil_name',
        'namlik',
        'harorat',
        'yoruglik',
        'status',

    ];

    public function dalolatnoma()
    {
        return $this->belongsTo(Dalolatnoma::class, 'dalolatnoma_id');
    }

    public function operator()
    {
        return $this->belongsTo(LaboratoryOperator::class, 'operator_id');
    }

    public function klassiyor()
    {
        return $this->belongsTo(Klassiyor::class, 'klassiyor_id');
    }
    public function director()
    {
        return $this->belongsTo(User::class, 'director_id');
    }
}
