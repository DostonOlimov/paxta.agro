<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalConclusionResult extends Model
{
    use HasFactory;

    protected $table = 'final_conclusion_results';

    protected $fillable = [
        'dalolatnoma_id',
        'invoice_number',
        'vehicle_number',
        'cmr_number',
        'conclusion_part_1',
        'conclusion_part_2',
        'conclusion_part_3',
        'type',
    ];
}
