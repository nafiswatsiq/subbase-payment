<?php

declare(strict_types=1);

namespace Nafiswatsiq\SubbasePayment\Support;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

final class SubbasePaymentPermission
{
    public static function allows(?string $permission, ?string $ability = null, ?string $subject = null): bool
    {
        $permission = trim((string) $permission);

        if ($permission === '') {
            if (! class_exists('Spatie\\Permission\\PermissionRegistrar')) {
                return true;
            }

            if ($ability === null || $subject === null) {
                return true;
            }

            $permission = static::buildShieldPermissionName($ability, $subject);
        }

        if (! class_exists('Spatie\\Permission\\PermissionRegistrar')) {
            return true;
        }

        return static::can($permission);
    }

    private static function can(string $permission): bool
    {
        $user = Auth::user();

        if ($user === null) {
            return false;
        }

        return (bool) $user->can($permission);
    }

    private static function buildShieldPermissionName(string $ability, string $subject): string
    {
        return Str::studly($ability).':'.class_basename($subject);
    }
}
