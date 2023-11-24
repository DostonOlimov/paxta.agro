<?php

namespace App\Services;

use App\Models\Area;
use App\Models\Level;
use App\Models\Region;
use App\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class LocationService
{
    private static $regions;
    private static $areas;

    public static function getRegion(int $regionId): Region
    {
        return static::getRegions()->firstWhere('id', $regionId);
    }

    public static function getRegions(): Collection
    {
        if (!static::$regions) {
            static::$regions = Cache::remember('regions', 60 * 60, function () {
                return Region::all();
            });
        }

        return static::$regions;
    }

    public static function getAllowedRegions(User $user): Collection
    {
        return self::getRegions()
                   ->filter(function (Region $region) use ($user) {
                       return $user->isAdmin() || in_array($region->id, $user->region_ids);
                   });
    }

    public static function getAllowedAreas(Authenticatable $user = null): Collection
    {
        $user = $user ?? auth()->user();
        throw_unless($user, AuthenticationException::class);

        return self::getAreas()->filter(function (Area $area) use ($user) {
            switch ($user->level->position) {
                case Level::LEVEL_REGION:
                    return in_array($area->state_id, $user->region_ids);
                case Level::LEVEL_DISTRICT:
                    return in_array($area->id, $user->area_ids);
                default:
                    return true;
            }
        });
    }

    public static function getArea(int $areaId): Area
    {
        return static::getAreas()->firstWhere('id', $areaId);
    }

    public static function getAreas(): Collection
    {
        if (!static::$areas) {
            static::$areas = Cache::remember('areas', 60 * 60, function () {
                return Area::all();
            });
        }

        return static::$areas;
    }

    public static function getAreaIds(array $regionIds): Collection
    {
        return Area::whereIn('state_id', $regionIds)->pluck('id');
    }
}
