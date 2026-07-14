<?php

namespace App\Filament\Resources\Concerns;

trait HasRoleAccess
{
    public static function canViewAny(): bool
    {
        return static::checkRole('view');
    }

    public static function canCreate(): bool
    {
        return static::checkRole('create');
    }

    public static function canEdit($record): bool
    {
        return static::checkRole('edit');
    }

    public static function canDelete($record): bool
    {
        return static::canEdit($record);
    }

    protected static function getRoleAccess(): array
    {
        return [];
    }

    protected static function checkRole(string $action): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if ($user->role === 'admin') {
            return true;
        }

        $allowed = static::getRoleAccess()[$action] ?? static::getRoleAccess()['view'] ?? [];

        return in_array($user->role, $allowed);
    }
}
