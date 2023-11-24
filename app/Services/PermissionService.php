<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class PermissionService
{
    private static $permissions;

    private static $roles;

    public static function getPermissions(): Collection
    {
        if (!self::$permissions) {
            self::$permissions = Cache::remember('permissions', 60 * 60, function () {
                return Permission::all();
            });
        }

        return self::$permissions;
    }

    public static function getPermission($roleId, $key): ?Permission
    {
        return self::getPermissions()->where('role_id', $roleId)->firstWhere('key_name', $key);
    }

    public static function getRoles(): Collection
    {
        if (!self::$roles) {
            self::$roles = Cache::remember('roles', 60 * 60, function () {
                return Role::all();
            });
        }

        return self::$roles;
    }

    public static function getRole($roleId): ?Role
    {
        return self::getRoles()->firstWhere('id', $roleId);
    }
}
