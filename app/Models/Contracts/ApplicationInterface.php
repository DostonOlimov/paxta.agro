<?php

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ApplicationInterface
{
    public function getId(): int;

    public static function getStatus();

    public static function getType();

    public function getYear();

    public function getStatusNameAttribute(): string ;

    public function getStatusColorAttribute(): string ;
}
