<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ClientData extends Model
{
    protected $table = 'client_data';

    protected $fillable = [
        'app_id',
        'client_id',
        'vagon_number',
        'yuk_xati',
    ];

}
