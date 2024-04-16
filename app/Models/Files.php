<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    use HasFactory;

    protected $table = 'files';

    protected $fillable = [
        'app_id',
        'name'
    ];
    public function application()
    {
        return $this->belongsTo(Application::class, 'app_id', 'id');
    }
}
