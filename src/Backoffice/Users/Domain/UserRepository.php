<?php

declare(strict_types=1);

namespace CarShop\Backoffice\Users\Domain;

interface UserRepository
{
    public function save(User $user): int;

    public function nextId(): int;
}
