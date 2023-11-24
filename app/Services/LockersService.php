<?php

namespace App\Services;

use App\Models\VehicleLocker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class LockersService
{
    private static $lockers;

    public static function getLockers(): Collection
    {
        if (!static::$lockers) {
            static::$lockers = Cache::remember('lockers', 60 * 60, function () {
                return VehicleLocker::all();
            });
        }

        return static::$lockers;
    }

    public static function getLocker(int $lockerId): ?VehicleLocker
    {
        return static::getLockers()->firstWhere('id', $lockerId);
    }
}
