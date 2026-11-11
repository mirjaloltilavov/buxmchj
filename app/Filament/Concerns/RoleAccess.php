<?php

namespace App\Filament\Concerns;

/**
 * Rol bo'yicha kirish huquqi (resurslar uchun).
 * Klassda: protected static array $roles = ['super_admin', 'admin', 'editor'];
 */
trait RoleAccess
{
    public static function roleAllowed(): bool
    {
        $u = auth()->user();

        return $u && $u->is_active && in_array($u->role, static::$roles, true);
    }

    public static function canViewAny(): bool
    {
        return static::roleAllowed();
    }

    public static function canCreate(): bool
    {
        return static::roleAllowed();
    }

    public static function canEdit($record): bool
    {
        return static::roleAllowed();
    }

    public static function canView($record): bool
    {
        return static::roleAllowed();
    }

    public static function canDelete($record): bool
    {
        return static::roleAllowed();
    }

    public static function canDeleteAny(): bool
    {
        return static::roleAllowed();
    }
}
