<?php

declare(strict_types=1);

namespace CarShop\Backoffice\Users\Infrastructure\Authorization;

enum UsersPermissions: string
{
    case CREATE = 'backoffice.users.create';

    public function label(): string
    {
        return match ($this) {
            self::CREATE => 'Create user',
        };
    }
}
